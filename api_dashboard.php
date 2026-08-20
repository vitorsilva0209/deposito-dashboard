<?php

header('Content-Type: application/json; charset=utf-8');

try {

    $conexao = new mysqli(
        "localhost",
        "root",
        "",
        "deposito_brasil"
    );

    if ($conexao->connect_error) {
        throw new Exception(
            "Erro na conexão com o banco: " . $conexao->connect_error
        );
    }

    $conexao->set_charset("utf8mb4");

    $sqlProdutos = "
        SELECT
            p.id,
            p.nome,
            p.categoria,
            p.preco,
            p.estoque,
            p.descricao,
            p.imagem_url,
            COALESCE(
                (
                    SELECT SUM(v.quantidade)
                    FROM vendas v
                    WHERE v.produto_id = p.id
                ),
                0
            ) AS quantidadeVendida
        FROM produtos p
        ORDER BY p.id ASC
    ";

    $resultadoProdutos = $conexao->query($sqlProdutos);

    if (!$resultadoProdutos) {
        throw new Exception(
            "Erro ao buscar produtos: " . $conexao->error
        );
    }

    $produtos = [];

    while ($produto = $resultadoProdutos->fetch_assoc()) {

        $produtos[] = [
            "id" => (int) $produto["id"],
            "nome" => $produto["nome"],
            "categoria" => $produto["categoria"],
            "preco" => (float) $produto["preco"],
            "estoque" => (int) $produto["estoque"],
            "quantidadeVendida" => (int) $produto["quantidadeVendida"],
            "descricao" => $produto["descricao"] ?? "",
            "imagemUrl" => $produto["imagem_url"] ?? ""
        ];
    }

    $sqlClientes = "
        SELECT
            id,
            nome,
            email,
            telefone
        FROM clientes
        ORDER BY id ASC
    ";

    $resultadoClientes = $conexao->query($sqlClientes);

    if (!$resultadoClientes) {
        throw new Exception(
            "Erro ao buscar clientes: " . $conexao->error
        );
    }

    $clientes = [];

    while ($cliente = $resultadoClientes->fetch_assoc()) {

        $clientes[] = [
            "id" => (int) $cliente["id"],
            "nome" => $cliente["nome"],
            "email" => $cliente["email"] ?? "",
            "telefone" => $cliente["telefone"] ?? ""
        ];
    }

    $sqlFuncionarios = "
        SELECT
            id,
            nome,
            cargo,
            salario
        FROM funcionarios
        ORDER BY id ASC
    ";

    $resultadoFuncionarios = $conexao->query($sqlFuncionarios);

    if (!$resultadoFuncionarios) {
        throw new Exception(
            "Erro ao buscar funcionários: " . $conexao->error
        );
    }

    $funcionarios = [];

    while ($funcionario = $resultadoFuncionarios->fetch_assoc()) {

        $funcionarios[] = [
            "id" => (int) $funcionario["id"],
            "nome" => $funcionario["nome"],
            "cargo" => $funcionario["cargo"],
            "salario" => (float) $funcionario["salario"]
        ];
    }

    $resposta = [
        "sucesso" => true,
        "produtos" => $produtos,
        "clientes" => $clientes,
        "funcionarios" => $funcionarios
    ];

    echo json_encode(
        $resposta,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_PRETTY_PRINT
    );

    $conexao->close();

} catch (Throwable $erro) {

    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "erro" => $erro->getMessage(),
        "produtos" => [],
        "clientes" => [],
        "funcionarios" => []
    ], JSON_UNESCAPED_UNICODE);
}