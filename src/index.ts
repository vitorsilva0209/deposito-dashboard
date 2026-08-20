import { processarMetricasDashboard } from "./dashboardServices.js";
import type {
    DadosDashboard,
    MetricasDashboard
} from "./types.js";

async function buscarDadosDashboard(): Promise<DadosDashboard> {

    const resposta = await fetch("api_dashboard.php");

    if (!resposta.ok) {
        throw new Error(`Erro HTTP: ${resposta.status}`);
    }

    const dados: DadosDashboard = await resposta.json();

    return dados;
}

export async function carregarDashboard(): Promise<void> {

    try {

        const dados = await buscarDadosDashboard();

        const metricas: MetricasDashboard =
            processarMetricasDashboard(dados);

        renderizarDashboard(metricas);

    } catch (erro) {

        console.error(
            "Erro ao carregar dashboard:",
            erro
        );

        exibirMensagemVazia(
            "Erro ao carregar dados."
        );
    }
}

function renderizarDashboard(
    metricas: MetricasDashboard
): void {

    const elProdutos =
        document.getElementById("total-produtos");

    const elClientes =
        document.getElementById("total-clientes");

    const elFuncionarios =
        document.getElementById("total-funcionarios");

    const elFaturamento =
        document.getElementById("faturamento-total");

    const elMaisVendido =
        document.getElementById("produto-mais-vendido");

    if (elProdutos) {
        elProdutos.textContent =
            String(metricas.totalProdutos);
    }

    if (elClientes) {
        elClientes.textContent =
            String(metricas.totalClientes);
    }

    if (elFuncionarios) {
        elFuncionarios.textContent =
            String(metricas.totalFuncionarios);
    }

    if (elFaturamento) {
        elFaturamento.textContent =
            metricas.faturamentoTotalFormatado;
    }

    if (elMaisVendido) {
        elMaisVendido.textContent =
            metricas.produtoMaisVendido;
    }

    renderizarTabela(metricas);
}

function renderizarTabela(
    metricas: MetricasDashboard
): void {

    const tabela =
        document.getElementById("tabela-produtos-corpo");

    if (!tabela) {
        return;
    }

    if (
        metricas.produtosFormatadosParaTabela.length === 0
    ) {

        tabela.innerHTML = `
            <tr>
                <td colspan="6"
                    style="text-align:center;">
                    Nenhum produto registrado.
                </td>
            </tr>
        `;

        return;
    }

    tabela.innerHTML =
        metricas.produtosFormatadosParaTabela
            .map((produto) => {

                return `
                    <tr>

                        <td>#${produto.id}</td>

                        <td>
                            <img
                                src="${produto.imagemUrl}"
                                alt="${produto.nome}"
                                style="
                                    width:50px;
                                    height:50px;
                                    object-fit:cover;
                                    border-radius:5px;
                                "
                                onerror="this.src='imagens/sem-imagem.jpg'"
                            >
                        </td>

                        <td>
                            <strong>
                                ${produto.nome}
                            </strong>
                        </td>

                        <td>
                            ${produto.categoria}
                        </td>

                        <td>
                            ${produto.precoFormatado}
                        </td>

                        <td>
                            <button
                                class="btn-detalhes"
                                data-nome="${produto.nome}"
                                data-categoria="${produto.categoria}"
                                data-preco="${produto.precoFormatado}"
                                data-descricao="${produto.descricao}"
                                data-imagem="${produto.imagemUrl}">
                                Ver Detalhes
                            </button>
                        </td>

                    </tr>
                `;

            })
            .join("");

    configurarEventosModal();
}

function configurarEventosModal(): void {

    const botoes =
        document.querySelectorAll(".btn-detalhes");

    const modal =
        document.getElementById("modal-produto");

    const fecharModal =
        document.getElementById("fechar-modal");

    botoes.forEach((botao) => {

        botao.addEventListener("click", (evento: Event) => {

            const target =
                evento.currentTarget as HTMLButtonElement;

            const nome =
                target.getAttribute("data-nome") || "";

            const categoria =
                target.getAttribute("data-categoria") || "";

            const preco =
                target.getAttribute("data-preco") || "";

            const descricao =
                target.getAttribute("data-descricao") || "";

            const imagem =
                target.getAttribute("data-imagem") || "";

            const elNome =
                document.getElementById("modal-nome");

            const elCategoria =
                document.getElementById("modal-categoria");

            const elPreco =
                document.getElementById("modal-preco");

            const elDescricao =
                document.getElementById("modal-descricao");

            const elImagem =
                document.getElementById("modal-imagem");

            if (elNome) {
                elNome.textContent = nome;
            }

            if (elCategoria) {
                elCategoria.textContent =
                    `Categoria: ${categoria}`;
            }

            if (elPreco) {
                elPreco.textContent = preco;
            }

            if (elDescricao) {
                elDescricao.textContent = descricao;
            }

            if (
                elImagem instanceof HTMLImageElement
            ) {
                elImagem.src = imagem;
            }

            if (modal) {
                modal.style.display = "flex";
            }

        });

    });

    if (fecharModal && modal) {

        fecharModal.addEventListener("click", () => {

            modal.style.display = "none";

        });

    }
}

function exibirMensagemVazia(
    mensagem: string
): void {

    const elFaturamento =
        document.getElementById("faturamento-total");

    if (elFaturamento) {
        elFaturamento.textContent = mensagem;
    }
}

document.addEventListener(
    "DOMContentLoaded",
    () => {
        carregarDashboard();
    }
);