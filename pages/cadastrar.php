<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../config/conexao.php");

if (isset($_POST["salvar"])) {

    $nome = trim($_POST["nome"]);
    $categoria = trim($_POST["categoria"]);
    $preco = (float) $_POST["preco"];
    $estoque = (int) $_POST["estoque"];
    $descricao = trim($_POST["descricao"]);
    $imagem_url = trim($_POST["imagem_url"]);

    $verifica = $conn->prepare(
        "SELECT id FROM produtos WHERE nome = ?"
    );

    $verifica->bind_param("s", $nome);
    $verifica->execute();

    $resultado = $verifica->get_result();

    if ($resultado->num_rows > 0) {

        $mensagem = "Produto já cadastrado.";

    } else {

        $sql = $conn->prepare(
            "INSERT INTO produtos
            (nome, categoria, preco, estoque, descricao, imagem_url)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $sql->bind_param(
            "ssdiss",
            $nome,
            $categoria,
            $preco,
            $estoque,
            $descricao,
            $imagem_url
        );

        if ($sql->execute()) {

            header("Location: listar.php?mensagem=Produto cadastrado com sucesso!");
            exit();

        } else {

            $mensagem = "Erro ao cadastrar produto.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Cadastrar Produto - Depósito Brasil</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">

    <div class="container-fluid">

        <a
            class="navbar-brand"
            href="../../dashboard.php"
        >
            Depósito Brasil
        </a>

        <div class="d-flex align-items-center">

            <span class="text-white me-3">

                Olá,
                <?php
                echo htmlspecialchars($_SESSION["nome"]);
                ?>

            </span>

            <a
                href="../../logout.php"
                class="btn btn-light"
            >
                Sair
            </a>

        </div>

    </div>

</nav>

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header bg-danger text-white">

            <h3 class="mb-0">
                Cadastrar Produto
            </h3>

        </div>

        <div class="card-body">

            <?php if (isset($mensagem)): ?>

                <div class="alert alert-warning">

                    <?php
                    echo htmlspecialchars($mensagem);
                    ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        Nome do produto
                    </label>

                    <input
                        type="text"
                        name="nome"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Categoria
                    </label>

                    <input
                        type="text"
                        name="categoria"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Preço
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="preco"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Estoque
                    </label>

                    <input
                        type="number"
                        min="0"
                        name="estoque"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Descrição
                    </label>

                    <textarea
                        name="descricao"
                        class="form-control"
                        rows="4"
                        placeholder="Digite a descrição do produto"
                    ></textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        URL da imagem
                    </label>

                    <input
                        type="text"
                        name="imagem_url"
                        class="form-control"
                        placeholder="imagens/produto.jpg"
                    >

                </div>

                <button
                    type="submit"
                    name="salvar"
                    class="btn btn-success"
                >
                    Salvar Produto
                </button>

                <a
                    href="listar.php"
                    class="btn btn-secondary"
                >
                    Voltar
                </a>

            </form>

        </div>

    </div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>