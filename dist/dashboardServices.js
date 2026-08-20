/**
 * Formata um número para moeda brasileira.
 */
export function formatarMoeda(valor) {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL"
    }).format(valor);
}
/**
 * Processa os dados recebidos da API
 * e gera as métricas utilizadas na Dashboard.
 */
export function processarMetricasDashboard(dados) {
    const produtos = dados.produtos ?? [];
    const clientes = dados.clientes ?? [];
    const funcionarios = dados.funcionarios ?? [];
    /*
    ==========================================
    REDUCE
    ==========================================

    Calcula o faturamento total.

    Fórmula:

    preço × quantidade vendida
    */
    const faturamentoTotal = produtos.reduce((total, produto) => {
        const preco = Number.isFinite(produto.preco)
            ? produto.preco
            : 0;
        const quantidade = Number.isFinite(produto.quantidadeVendida)
            ? produto.quantidadeVendida
            : 0;
        return total + (preco * quantidade);
    }, 0);
    /*
    ==========================================
    FILTER
    ==========================================

    Produtos com estoque crítico.

    Consideramos crítico:
    estoque menor ou igual a 5.
    */
    const produtosEstoqueCritico = produtos.filter((produto) => {
        const estoque = Number.isFinite(produto.estoque)
            ? produto.estoque
            : 0;
        return estoque <= 5;
    });
    /*
    ==========================================
    RANKING
    ==========================================

    Descobre qual produto possui
    a maior quantidade de vendas.
    */
    const produtoMaisVendido = produtos.reduce((produtoAtual, produto) => {
        if (produtoAtual === null) {
            return produto;
        }
        return produto.quantidadeVendida >
            produtoAtual.quantidadeVendida
            ? produto
            : produtoAtual;
    }, null);
    /*
    Se não existir nenhum produto vendido,
    mostramos uma mensagem amigável.
    */
    const nomeProdutoMaisVendido = produtoMaisVendido !== null &&
        produtoMaisVendido.quantidadeVendida > 0
        ? `${produtoMaisVendido.nome} (${produtoMaisVendido.quantidadeVendida} vendas)`
        : "Nenhum produto vendido";
    /*
    ==========================================
    MAP
    ==========================================

    Prepara os produtos para serem
    apresentados na tabela da Dashboard.
    */
    const produtosFormatadosParaTabela = produtos.map((produto) => {
        return {
            id: produto.id,
            nome: produto.nome,
            categoria: produto.categoria || "Sem categoria",
            precoFormatado: formatarMoeda(Number.isFinite(produto.preco)
                ? produto.preco
                : 0),
            descricao: produto.descricao ||
                "Nenhuma descrição disponível.",
            imagemUrl: produto.imagemUrl ||
                "imagens/sem-imagem.jpg"
        };
    });
    return {
        faturamentoTotalFormatado: formatarMoeda(faturamentoTotal),
        totalProdutos: produtos.length,
        totalClientes: clientes.length,
        totalFuncionarios: funcionarios.length,
        produtosEstoqueCritico,
        produtoMaisVendido: nomeProdutoMaisVendido,
        produtosFormatadosParaTabela
    };
}
