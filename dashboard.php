<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

include("config/conexao.php");

$totalProdutos = 0;
$totalClientes = 0;
$totalFuncionarios = 0;
$faturamentoTotal = 0;
$produtoMaisVendido = "Nenhum produto";

$resultado = $conn->query("SELECT COUNT(*) AS total FROM produtos");

if ($resultado) {
    $dados = $resultado->fetch_assoc();
    $totalProdutos = (int)$dados["total"];
}

$resultado = $conn->query("SELECT COUNT(*) AS total FROM clientes");

if ($resultado) {
    $dados = $resultado->fetch_assoc();
    $totalClientes = (int)$dados["total"];
}

$resultado = $conn->query("SELECT COUNT(*) AS total FROM funcionarios");

if ($resultado) {
    $dados = $resultado->fetch_assoc();
    $totalFuncionarios = (int)$dados["total"];
}

$resultado = $conn->query("
    SELECT COALESCE(SUM(p.preco * v.quantidade), 0) AS total
    FROM vendas v
    INNER JOIN produtos p ON p.id = v.produto_id
");

if ($resultado) {
    $dados = $resultado->fetch_assoc();
    $faturamentoTotal = (float)$dados["total"];
}

$resultado = $conn->query("
    SELECT
        p.nome,
        SUM(v.quantidade) AS total_vendido
    FROM vendas v
    INNER JOIN produtos p ON p.id = v.produto_id
    GROUP BY p.id, p.nome
    ORDER BY total_vendido DESC
    LIMIT 1
");

if ($resultado && $resultado->num_rows > 0) {
    $dados = $resultado->fetch_assoc();

    $produtoMaisVendido =
        $dados["nome"] .
        " (" .
        $dados["total_vendido"] .
        " vendas)";
}

$resProdutos = $conn->query("
    SELECT *
    FROM produtos
    ORDER BY id DESC
    LIMIT 10
");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Depósito Brasil</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            background: #f4f6f9;
            min-height: 100vh;
            color: #333;
        }

        .sidebar {
            width: 220px;
            background: #1e1e1e;
            color: #fff;
            min-height: 100vh;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #b0b0b0;
            text-decoration: none;
            font-size: 15px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #d32f2f;
            color: #fff;
            font-weight: bold;
        }

        .content {
            flex: 1;
            min-width: 0;
        }

        .header {
            background: #d32f2f;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            background: #fff;
            color: #d32f2f;
            padding: 6px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 10px;
        }

        .main {
            padding: 30px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card-stat {
            padding: 20px;
            border-radius: 8px;
            color: #fff;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .card-stat h1 {
            font-size: 36px;
            margin-bottom: 5px;
        }

        .bg-blue {
            background: #007bff;
        }

        .bg-green {
            background: #28a745;
        }

        .bg-yellow {
            background: #ffc107;
            color: #333;
        }

        .bg-red {
            background: #dc3545;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 3px solid #d32f2f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        th {
            background: #f8f9fa;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .btn-detalhes {
            padding: 7px 12px;
            background: #d32f2f;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-detalhes:hover {
            background: #a92323;
        }

        .produto-imagem {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 5px;
        }

        .produto-mais-vendido {
            font-size: 18px;
            color: #333;
        }

        .estoque-critico {
            margin-top: 20px;
        }

        .estoque-critico ul {
            margin-top: 15px;
            padding-left: 20px;
        }

        .estoque-critico li {
            margin-bottom: 8px;
        }

        @media (max-width: 900px) {

            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .sidebar {
                width: 180px;
            }

        }

        @media (max-width: 600px) {

            body {
                display: block;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .main {
                padding: 15px;
                overflow-x: auto;
            }

        }

    </style>

</head>

<body>

    <div class="sidebar">

        <h2>Painel</h2>

        <a href="dashboard.php" class="active">
            Dashboard
        </a>

        <a href="produtos.php">
            Produtos
        </a>

        <a href="clientes.php">
            Clientes
        </a>

        <a href="funcionarios.php">
            Funcionários
        </a>

    </div>


    <div class="content">

        <div class="header">

            <h3>
                Depósito Brasil
            </h3>

            <div>

                <span>
                    Olá,
                    <?php
                    echo htmlspecialchars($_SESSION["nome"]);
                    ?>
                </span>

                <a href="logout.php">
                    Sair
                </a>

            </div>

        </div>


        <div class="main">

            <h2>
                Dashboard
            </h2>

            <br>


            <div class="cards-grid">

                <div class="card-stat bg-blue">

                <h1>
    <?php echo $totalProdutos; ?>
</h1>

                    <p>
                        Produtos
                    </p>

                </div>


                <div class="card-stat bg-green">

                    <h1>
    <?php echo $totalClientes; ?>
</h1>

                    <p>
                        Clientes
                    </p>

                </div>


                <div class="card-stat bg-yellow">

                <h1>
    <?php echo $totalFuncionarios; ?>
</h1>

                    <p>
                        Funcionários
                    </p>

                </div>


                <div class="card-stat bg-red">

                   <h1>
    R$
    <?php
    echo number_format(
        $faturamentoTotal,
        2,
        ",",
        "."
    );
    ?>
</h1>
                    <p>
                        Faturamento Total
                    </p>

                </div>

            </div>


            <div class="card">

                <h3>
                    Produto Mais Vendido
                </h3>

                <br>

               <p>
    <?php echo htmlspecialchars($produtoMaisVendido); ?>
</p>

            </div>


            <br>


            <div class="card">

                <h3>
                    Produtos
                </h3>

                <table>

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Imagem</th>

                            <th>Produto</th>

                            <th>Categoria</th>

                            <th>Preço</th>

                            <th>Ações</th>

                        </tr>

                    </thead>


                  <tbody>

<?php if ($resProdutos && $resProdutos->num_rows > 0): ?>

    <?php while ($p = $resProdutos->fetch_assoc()): ?>

        <tr>

            <td>
                #<?php echo $p["id"]; ?>
            </td>

            <td>

                <?php if (!empty($p["imagem_url"])): ?>

                    <img
                        src="<?php echo htmlspecialchars($p["imagem_url"]); ?>"
                        alt="<?php echo htmlspecialchars($p["nome"]); ?>"
                        style="
                            width:45px;
                            height:45px;
                            object-fit:cover;
                            border-radius:5px;
                        "
                    >

                <?php else: ?>

                    Sem imagem

                <?php endif; ?>

            </td>

            <td>
                <strong>
                    <?php echo htmlspecialchars($p["nome"]); ?>
                </strong>
            </td>

            <td>
                <?php echo htmlspecialchars($p["categoria"]); ?>
            </td>

            <td>
                R$
                <?php
                echo number_format(
                    $p["preco"],
                    2,
                    ",",
                    "."
                );
                ?>
            </td>

            <td>

                <button
                    class="btn-detalhes"
                    type="button"
                    onclick="alert('Produto: <?php echo htmlspecialchars($p["nome"]); ?>')"
                >
                    Ver Detalhes
                </button>

            </td>

        </tr>

    <?php endwhile; ?>

<?php else: ?>

    <tr>

        <td
            colspan="6"
            style="text-align:center;"
        >
            Nenhum produto cadastrado.
        </td>

    </tr>

<?php endif; ?>

</tbody>

                </table>

            </div>


            <br>


            <div class="card estoque-critico">

                <h3>
                    Produtos com Estoque Crítico
                </h3>

                <ul id="lista-estoque-critico">

                    <li>
                        Carregando...
                    </li>

                </ul>

            </div>

        </div>

    </div>


    <div
        id="modal-produto"
        style="
            display:none;
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.6);
            justify-content:center;
            align-items:center;
            z-index:1000;
        "
    >

        <div
            style="
                background:#fff;
                padding:25px;
                border-radius:8px;
                width:90%;
                max-width:450px;
                position:relative;
            "
        >

            <button
                id="fechar-modal"
                style="
                    position:absolute;
                    top:10px;
                    right:15px;
                    border:none;
                    background:none;
                    font-size:22px;
                    cursor:pointer;
                "
            >
                &times;
            </button>


            <img
                id="modal-imagem"
                src=""
                alt="Produto"
                style="
                    width:100%;
                    max-height:220px;
                    object-fit:contain;
                    border-radius:6px;
                "
            >


            <h2 id="modal-nome">
            </h2>


            <p id="modal-categoria">
            </p>


            <h3 id="modal-preco">
            </h3>


            <p id="modal-descricao">
            </p>

        </div>

    </div>




</body>

</html>