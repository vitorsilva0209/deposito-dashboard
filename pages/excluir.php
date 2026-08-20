<?php

include("../../config/conexao.php");

$id = $_GET['id'] ?? 0;

$verifica = $conn->query(
    "SELECT id FROM produtos WHERE id = $id"
);

if (!$verifica || $verifica->num_rows == 0) {

    echo "<script>
    alert('Produto não encontrado.');
    window.location='listar.php';
    </script>";

    exit();

}

$excluir = $conn->query(
    "DELETE FROM produtos WHERE id = $id"
);

if ($excluir) {

    echo "<script>
    alert('Produto excluído com sucesso!');
    window.location='listar.php';
    </script>";

} else {

    echo "<script>
    alert('Não foi possível excluir este produto. Ele pode possuir vendas vinculadas.');
    window.location='listar.php';
    </script>";

}
?>