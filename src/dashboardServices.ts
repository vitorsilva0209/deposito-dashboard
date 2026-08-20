import type {
    DadosDashboard,
    MetricasDashboard,
    Produto
} from "./types.js";

export function formatarMoeda(valor: number): string {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL"
    }).format(valor);
}

export function processarMetricasDashboard(
    dados: DadosDashboard
): MetricasDashboard {

    const produtos: Produto[] = dados.produtos ?? [];
    const clientes = dados.clientes ?? [];
    const funcionarios = dados.funcionarios ?? [];

    const faturamentoTotal = produtos.reduce(
        (total, produto) =>
            total + ((produto.preco || 0) * (produto.quantidadeVendida || 0)),
        0
    );

    const produtosEstoqueCritico = produtos.filter(
        (produto) => (produto.estoque ?? 0) <= 5
    );

    const produtoMaisVendido = produtos.reduce(
        (maisVendido, produto) => {

            if (!maisVendido) {
                return produto;
            }

            return (produto.quantidadeVendida ?? 0) >
                (maisVendido.quantidadeVendida ?? 0)
                ? produto
                : maisVendido;
        },
        null as Produto | null
    );

    const produtosFormatadosParaTabela = produtos.map((produto) => ({
        id: produto.id,
        nome: produto.nome,
        categoria: produto.categoria,
        precoFormatado: formatarMoeda(produto.preco || 0),
        descricao:
            produto.descricao ||
            "Nenhuma descrição disponível.",
        imagemUrl:
            produto.imagemUrl ||
            "imagens/sem-imagem.jpg"
    }));

    return {
        faturamentoTotalFormatado: formatarMoeda(faturamentoTotal),

        totalProdutos: produtos.length,

        totalClientes: clientes.length,

        totalFuncionarios: funcionarios.length,

        produtosEstoqueCritico,

        produtoMaisVendido: produtoMaisVendido
            ? `${produtoMaisVendido.nome} (${produtoMaisVendido.quantidadeVendida ?? 0} vendas)`
            : "Nenhum produto vendido",

        produtosFormatadosParaTabela
    };
}