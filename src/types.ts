export interface Produto {
    id: number;
    nome: string;
    categoria: string;
    preco: number;
    estoque: number;
    quantidadeVendida: number;
    descricao: string;
    imagemUrl: string;
}

export interface Cliente {
    id: number;
    nome: string;
    email: string;
    telefone: string;
}

export interface Funcionario {
    id: number;
    nome: string;
    cargo: string;
    salario: number;
}

export interface DadosDashboard {
    sucesso: boolean;
    produtos: Produto[];
    clientes: Cliente[];
    funcionarios: Funcionario[];
}

export interface ProdutoFormatado {
    id: number;
    nome: string;
    categoria: string;
    precoFormatado: string;
    descricao: string;
    imagemUrl: string;
}

export interface MetricasDashboard {
    faturamentoTotalFormatado: string;
    totalProdutos: number;
    totalClientes: number;
    totalFuncionarios: number;
    produtosEstoqueCritico: Produto[];
    produtoMaisVendido: string;
    produtosFormatadosParaTabela: ProdutoFormatado[];
}