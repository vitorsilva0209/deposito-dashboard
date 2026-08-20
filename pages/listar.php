<?php

include("../../config/conexao.php");

$sql = "SELECT * FROM produtos ORDER BY id DESC";
$resultado = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Produtos - Depósito Brasil</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-3">

<h2>Produtos</h2>

<a href="cadastrar.php" class="btn btn-success">
Novo Produto
</a>

</div>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Nome</th>
<th>Categoria</th>
<th>Preço</th>
<th>Estoque</th>
<th>Ações</th>

</tr>

</thead>

<tbody>

<?php if ($resultado && $resultado->num_rows > 0): ?>

<?php while ($produto = $resultado->fetch_assoc()): ?>

<tr>

<td>
#<?= $produto['id']; ?>
</td>

<td>
<?= htmlspecialchars($produto['nome']); ?>
</td>

<td>
<?= htmlspecialchars($produto['categoria']); ?>
</td>

<td>
R$ <?= number_format($produto['preco'], 2, ',', '.'); ?>
</td>

<td>
<?= $produto['estoque']; ?>
</td>

<td>

<a
href="editar.php?id=<?= $produto['id']; ?>"
class="btn btn-primary btn-sm">

Editar

</a>

<a
href="excluir.php?id=<?= $produto['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Deseja realmente excluir este produto?');">

Excluir

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="6" class="text-center">

Nenhum produto cadastrado.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

<a href="../../dashboard.php" class="btn btn-secondary">

Voltar para Dashboard

</a>

</div>

</body>

</html>