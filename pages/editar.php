<?php

include("../../config/conexao.php");

$id = $_GET['id'] ?? 0;

$produto = $conn->query(
    "SELECT * FROM produtos WHERE id = $id"
)->fetch_assoc();

if (!$produto) {

    echo "<script>
    alert('Produto não encontrado.');
    window.location='listar.php';
    </script>";

    exit();

}

if (isset($_POST['editar'])) {

    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $descricao = $_POST['descricao'];
    $imagem_url = $_POST['imagem_url'];

    $sql = "UPDATE produtos SET

        nome = '$nome',
        categoria = '$categoria',
        preco = '$preco',
        estoque = '$estoque',
        descricao = '$descricao',
        imagem_url = '$imagem_url'

        WHERE id = $id";

    if ($conn->query($sql)) {

        header("Location: listar.php");
        exit();

    } else {

        echo "<script>
        alert('Erro ao atualizar produto: " . $conn->error . "');
        </script>";

    }

}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Produto</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Editar Produto</h2>

<form method="POST">

<div class="mb-3">

<label class="form-label">
Nome
</label>

<input
type="text"
name="nome"
value="<?= htmlspecialchars($produto['nome']); ?>"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Categoria
</label>

<input
type="text"
name="categoria"
value="<?= htmlspecialchars($produto['categoria']); ?>"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Preço
</label>

<input
type="number"
step="0.01"
name="preco"
value="<?= $produto['preco']; ?>"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Estoque
</label>

<input
type="number"
name="estoque"
value="<?= $produto['estoque']; ?>"
class="form-control"
min="0"
required>

</div>

<div class="mb-3">

<label class="form-label">
Descrição
</label>

<textarea
name="descricao"
class="form-control"
rows="4"><?= htmlspecialchars($produto['descricao'] ?? ''); ?></textarea>

</div>

<div class="mb-3">

<label class="form-label">
Imagem
</label>

<input
type="text"
name="imagem_url"
value="<?= htmlspecialchars($produto['imagem_url'] ?? ''); ?>"
class="form-control">

</div>

<button
type="submit"
name="editar"
class="btn btn-primary">

Atualizar

</button>

<a
href="listar.php"
class="btn btn-secondary">

Cancelar

</a>

</form>

</div>

</body>

</html>