import { processarMetricasDashboard } from "./dashboardServices.js";
/**
 * Busca os dados da API PHP.
 */
async function buscarDadosDashboard() {
    const resposta = await fetch("api_dashboard.php");
    if (!resposta.ok) {
        throw new Error(`Erro HTTP: ${resposta.status}`);
    }
    const dados = await resposta.json();
    if (!dados.sucesso) {
        throw new Error("A API retornou uma resposta inválida.");
    }
    return dados;
}
/**
 * Carrega e processa a Dashboard.
 */
export async function carregarDashboard() {
    try {
        const dados = await buscarDadosDashboard();
        const metricas = processarMetricasDashboard(dados);
        renderizarDashboard(metricas);
    }
    catch (erro) {
        console.error("Erro ao carregar dashboard:", erro);
        const mensagem = erro instanceof Error
            ? erro.message
            : "Erro desconhecido.";
        exibirMensagemVazia(`Erro ao carregar dados: ${mensagem}`);
    }
}
/**
 * Renderiza as métricas no HTML.
 */
function renderizarDashboard(metricas) {
    const elFaturamento = document.getElementById("faturamento-total");
    const elMaisVendido = document.getElementById("produto-mais-vendido");
    const elTabelaCorpo = document.getElementById("tabela-produtos-corpo");
    /*
    ==========================================
    FATURAMENTO
    ==========================================
    */
    if (elFaturamento) {
        elFaturamento.textContent =
            metricas.faturamentoTotalFormatado;
    }
    /*
    ==========================================
    PRODUTO MAIS VENDIDO
    ==========================================
    */
    if (elMaisVendido) {
        elMaisVendido.textContent =
            metricas.produtoMaisVendido;
    }
    /*
    ==========================================
    TABELA
    ==========================================
    */
    if (elTabelaCorpo) {
        if (metricas
            .produtosFormatadosParaTabela
            .length === 0) {
            elTabelaCorpo.innerHTML = `
                <tr>
                    <td
                        colspan="6"
                        style="
                            text-align: center;
                            color: #888;
                            padding: 30px;
                        "
                    >
                        Nenhum produto registrado.
                    </td>
                </tr>
            `;
            return;
        }
        elTabelaCorpo.innerHTML =
            metricas
                .produtosFormatadosParaTabela
                .map((produto) => {
                return `
                        <tr>

                            <td>
                                #${produto.id}
                            </td>

                            <td>

                                <img
                                    src="${produto.imagemUrl}"
                                    alt="${produto.nome}"
                                    style="
                                        width: 40px;
                                        height: 40px;
                                        object-fit: cover;
                                        border-radius: 4px;
                                    "
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

                                    data-imagem="${produto.imagemUrl}"

                                    style="
                                        padding: 6px 12px;
                                        background: #d32f2f;
                                        color: #fff;
                                        border: none;
                                        border-radius: 4px;
                                        cursor: pointer;
                                    "
                                >

                                    Ver Detalhes

                                </button>

                            </td>

                        </tr>
                    `;
            })
                .join("");
        configurarEventosModal();
    }
}
/**
 * Configura os botões "Ver Detalhes".
 */
function configurarEventosModal() {
    const botoes = document.querySelectorAll(".btn-detalhes");
    const modal = document.getElementById("modal-produto");
    const fecharModal = document.getElementById("fechar-modal");
    botoes.forEach((botao) => {
        botao.addEventListener("click", (evento) => {
            const target = evento.currentTarget;
            if (!(target instanceof
                HTMLButtonElement)) {
                return;
            }
            const nome = target.getAttribute("data-nome") ?? "";
            const categoria = target.getAttribute("data-categoria") ?? "";
            const preco = target.getAttribute("data-preco") ?? "";
            const descricao = target.getAttribute("data-descricao") ?? "";
            const imagem = target.getAttribute("data-imagem") ?? "";
            const elNome = document.getElementById("modal-nome");
            const elCategoria = document.getElementById("modal-categoria");
            const elPreco = document.getElementById("modal-preco");
            const elDescricao = document.getElementById("modal-descricao");
            const elImagem = document.getElementById("modal-imagem");
            if (elNome) {
                elNome.textContent =
                    nome;
            }
            if (elCategoria) {
                elCategoria.textContent =
                    `Categoria: ${categoria}`;
            }
            if (elPreco) {
                elPreco.textContent =
                    preco;
            }
            if (elDescricao) {
                elDescricao.textContent =
                    descricao;
            }
            if (elImagem instanceof
                HTMLImageElement) {
                elImagem.src =
                    imagem;
                elImagem.alt =
                    nome;
            }
            if (modal) {
                modal.style.display =
                    "flex";
            }
        });
    });
    if (fecharModal && modal) {
        fecharModal.addEventListener("click", () => {
            modal.style.display =
                "none";
        });
    }
    if (modal) {
        modal.addEventListener("click", (evento) => {
            if (evento.target === modal) {
                modal.style.display =
                    "none";
            }
        });
    }
}
/**
 * Mostra mensagem de erro na Dashboard.
 */
function exibirMensagemVazia(mensagem) {
    const elFaturamento = document.getElementById("faturamento-total");
    if (elFaturamento) {
        elFaturamento.textContent =
            mensagem;
    }
}
/**
 * Inicia a Dashboard depois
 * que o HTML estiver carregado.
 */
document.addEventListener("DOMContentLoaded", () => {
    carregarDashboard();
});
