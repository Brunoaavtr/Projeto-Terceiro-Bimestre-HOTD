import type {
    ProdutoDashboard,
    RespostaDashboard,
    VendaDetalhada,
    RespostaVendas
} from "./tipos.js";

/* Carrega os dados consolidados do dashboard pela API. */
async function carregarDashboard(): Promise<RespostaDashboard> {
    try {
        /* Faz a requisição para a API do dashboard. */
        const resposta = await fetch("ADMIN/api/dashboard.php");

        if (!resposta.ok) {
            throw new Error(`Erro na requisição: Status ${resposta.status}`);
        }

        /* Converte a resposta da API para o tipo do dashboard. */
        const dados: RespostaDashboard = await resposta.json();

        /* Converte os valores recebidos da API para números. */
        dados.dados = dados.dados.map((produto: ProdutoDashboard): ProdutoDashboard => {
            return {
                ID_PRODUTO: Number(produto.ID_PRODUTO),
                NM_PRODUTO: produto.NM_PRODUTO,
                NM_MARCA: produto.NM_MARCA,
                ID_CATEGORIA: Number(produto.ID_CATEGORIA),
                NM_CATEGORIA: produto.NM_CATEGORIA,
                QT_VENDIDA: produto.QT_VENDIDA === null ? null : Number(produto.QT_VENDIDA),
                VL_FATURADO: produto.VL_FATURADO === null ? null : Number(produto.VL_FATURADO)
            };
        });

        return dados;

    } catch (erro) {
        /* Mostra o erro no console caso a API não responda corretamente. */
        console.error("Falha ao carregar o dashboard:", erro);

        return {
            sucesso: false,
            pagina: 1,
            por_pagina: 10,
            categoria: null,
            dados: [],
            erro: "Não foi possível carregar os dados do dashboard."
        };
    }
}

/* Carrega os dados detalhados das vendas pela API. */
async function carregarVendas(): Promise<RespostaVendas> {
    try {
        /* Faz a requisição para a API de vendas. */
        const resposta = await fetch("ADMIN/api/vendas.php");

        if (!resposta.ok) {
            throw new Error(`Erro na requisição: Status ${resposta.status}`);
        }

        /* Converte a resposta da API para o tipo de vendas. */
        const dados: RespostaVendas = await resposta.json();

        /* Converte os valores recebidos da API para números. */
        dados.dados = dados.dados.map((venda: VendaDetalhada): VendaDetalhada => {
            return {
                ID_PEDIDO: Number(venda.ID_PEDIDO),
                DT_PEDIDO: venda.DT_PEDIDO,
                ID_PRODUTO: Number(venda.ID_PRODUTO),
                NM_PRODUTO: venda.NM_PRODUTO,
                NM_MARCA: venda.NM_MARCA,
                ID_CATEGORIA: Number(venda.ID_CATEGORIA),
                NM_CATEGORIA: venda.NM_CATEGORIA,
                QT_PRODUTO: Number(venda.QT_PRODUTO),
                VL_UNITARIO: Number(venda.VL_UNITARIO),
                VL_SUBTOTAL: Number(venda.VL_SUBTOTAL)
            };
        });

        return dados;

    } catch (erro) {
        /* Mostra o erro no console caso a API não responda corretamente. */
        console.error("Falha ao carregar as vendas:", erro);

        return {
            sucesso: false,
            categoria: null,
            data_inicial: null,
            data_final: null,
            dados: [],
            erro: "Não foi possível carregar os dados das vendas."
        };
    }
}

/* Filtra as vendas de acordo com a categoria selecionada. */
function filtrarVendasPorCategoria(
    vendas: VendaDetalhada[],
    categoriaSelecionada: number | null
): VendaDetalhada[] {
    return vendas.filter((venda: VendaDetalhada): boolean => {
        return categoriaSelecionada === null || venda.ID_CATEGORIA === categoriaSelecionada;
    });
}

/* Filtra as vendas de acordo com o período selecionado. */
function filtrarVendasPorPeriodo(
    vendas: VendaDetalhada[],
    dataInicial: string | null,
    dataFinal: string | null
): VendaDetalhada[] {
    return vendas.filter((venda: VendaDetalhada): boolean => {
        const dataVenda = new Date(venda.DT_PEDIDO);

        if (Number.isNaN(dataVenda.getTime())) {
            return false;
        }

        const inicio = dataInicial ? new Date(`${dataInicial}T00:00:00`) : null;
        const fim = dataFinal ? new Date(`${dataFinal}T23:59:59`) : null;

        if (inicio && dataVenda < inicio) {
            return false;
        }

        if (fim && dataVenda > fim) {
            return false;
        }

        return true;
    });
}

/* Calcula as principais métricas das vendas. */
function calcularMetricasVendas(
    vendas: VendaDetalhada[]
): {
    quantidadeVendida: number;
    faturamento: number;
    produtosVendidos: number;
} {
    /* Soma a quantidade total de produtos vendidos. */
    const quantidadeVendida = vendas.reduce(
        (total: number, venda: VendaDetalhada): number => {
            const quantidade = Number(venda.QT_PRODUTO);

            if (!Number.isFinite(quantidade)) {
                return total;
            }

            return total + quantidade;
        },
        0
    );

    /* Calcula o faturamento total das vendas. */
    const faturamento = vendas.reduce(
        (total: number, venda: VendaDetalhada): number => {
            const quantidade = Number(venda.QT_PRODUTO);
            const valorUnitario = Number(venda.VL_UNITARIO);

            if (!Number.isFinite(quantidade) || !Number.isFinite(valorUnitario)) {
                return total;
            }

            return total + quantidade * valorUnitario;
        },
        0
    );

    /* Cria uma lista com os IDs dos produtos vendidos sem repetição. */
    const produtosUnicos = new Set<number>();

    vendas.forEach((venda: VendaDetalhada): void => {
        const idProduto = Number(venda.ID_PRODUTO);

        if (Number.isFinite(idProduto)) {
            produtosUnicos.add(idProduto);
        }
    });

    return {
        quantidadeVendida,
        faturamento,
        produtosVendidos: produtosUnicos.size
    };
}

/* Encontra o produto que possui a maior quantidade de vendas. */
function encontrarProdutoMaisVendidoVendas(
    vendas: VendaDetalhada[]
): {
    nome: string;
    quantidade: number;
} | null {
    if (vendas.length === 0) {
        return null;
    }

    /* Guarda a quantidade vendida de cada produto. */
    const frequenciaProdutos: Record<number, number> = {};

    /* Guarda o nome de cada produto pelo seu ID. */
    const nomesProdutos: Record<number, string> = {};

    vendas.forEach((venda: VendaDetalhada): void => {
        const idProduto = Number(venda.ID_PRODUTO);
        const quantidade = Number(venda.QT_PRODUTO);

        if (!Number.isFinite(idProduto) || !Number.isFinite(quantidade)) {
            return;
        }

        frequenciaProdutos[idProduto] = (frequenciaProdutos[idProduto] ?? 0) + quantidade;
        nomesProdutos[idProduto] = venda.NM_PRODUTO;
    });

    let idMaisVendido: number | null = null;
    let maiorQuantidade = 0;

    /* Procura o produto com a maior quantidade vendida. */
    Object.entries(frequenciaProdutos).forEach(
        ([id, quantidade]: [string, number]): void => {
            if (quantidade > maiorQuantidade) {
                maiorQuantidade = quantidade;
                idMaisVendido = Number(id);
            }
        }
    );

    if (idMaisVendido === null) {
        return null;
    }

    return {
        nome: nomesProdutos[idMaisVendido],
        quantidade: maiorQuantidade
    };
}

/* Transforma os dados das vendas para serem exibidos na tabela. */
function transformarVendas(
    vendas: VendaDetalhada[]
): Array<{
    nome: string;
    marca: string;
    categoria: string;
    quantidade: number;
    faturamento: number;
    faturamentoFormatado: string;
}> {
    return vendas.map((venda: VendaDetalhada) => {
        const quantidade = Number(venda.QT_PRODUTO);
        const faturamento = Number(venda.VL_UNITARIO) * quantidade;

        return {
            nome: venda.NM_PRODUTO,
            marca: venda.NM_MARCA,
            categoria: venda.NM_CATEGORIA,
            quantidade,
            faturamento,
            faturamentoFormatado: faturamento.toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
            })
        };
    });
}

/* Exibe as métricas calculadas nos cards do dashboard. */
function exibirMetricas(vendas: VendaDetalhada[]): void {
    /* Pega os elementos onde as métricas serão exibidas. */
    const totalVendido = document.getElementById("total-vendido");
    const faturamentoTotal = document.getElementById("faturamento-total");
    const produtosVendidos = document.getElementById("produtos-vendidos");
    const produtoMaisVendido = document.getElementById("produto-mais-vendido");

    if (!totalVendido || !faturamentoTotal || !produtosVendidos || !produtoMaisVendido) {
        return;
    }

    /* Mostra valores zerados quando não existem vendas. */
    if (vendas.length === 0) {
        totalVendido.innerText = "0";
        faturamentoTotal.innerText = "R$ 0,00";
        produtosVendidos.innerText = "0";
        produtoMaisVendido.innerText = "Nenhum";
        return;
    }

    /* Calcula as métricas e o produto mais vendido. */
    const metricas = calcularMetricasVendas(vendas);
    const produtoRanking = encontrarProdutoMaisVendidoVendas(vendas);

    totalVendido.innerText = metricas.quantidadeVendida.toString();

    faturamentoTotal.innerText = metricas.faturamento.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });

    produtosVendidos.innerText = metricas.produtosVendidos.toString();

    /* Mostra o produto que ficou em primeiro no ranking. */
    if (produtoRanking) {
        produtoMaisVendido.innerText = `${produtoRanking.nome} (${produtoRanking.quantidade} un.)`;
    } else {
        produtoMaisVendido.innerText = "Nenhum";
    }
}

/* Exibe os produtos filtrados na tabela do dashboard. */
function exibirTabela(
    produtos: ReturnType<typeof transformarVendas>
): void {
    /* Pega o corpo da tabela. */
    const tabela = document.getElementById("tabela-dashboard");

    if (!tabela) {
        return;
    }

    /* Limpa os dados anteriores da tabela. */
    tabela.innerHTML = "";

    /* Mostra uma mensagem quando não existem dados. */
    if (produtos.length === 0) {
        tabela.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">Nenhum dado registrado.</td>
            </tr>
        `;
        return;
    }

    /* Cria uma linha para cada produto. */
    produtos.forEach((produto): void => {
        const linha = document.createElement("tr");

        linha.innerHTML = `
            <td>${produto.nome}</td>
            <td>${produto.marca}</td>
            <td>${produto.categoria}</td>
            <td>${produto.quantidade}</td>
            <td>${produto.faturamentoFormatado}</td>
        `;

        tabela.appendChild(linha);
    });
};

/* Aguarda o carregamento completo da página. */
document.addEventListener("DOMContentLoaded", async (): Promise<void> => {
    /* Carrega os dados das duas APIs. */
    const resultado: RespostaDashboard = await carregarDashboard();
    const resultadoVendas: RespostaVendas = await carregarVendas();

    /* Mostra no console os dados recebidos pelas APIs. */
    console.log("Dados recebidos da API do dashboard:", resultado);
    console.log("Dados recebidos da API de vendas:", resultadoVendas);

    /* Pega o elemento utilizado para mostrar mensagens de erro. */
    const mensagem = document.getElementById("mensagem-dashboard");

    /* Verifica se houve erro na API do dashboard. */
    if (!resultado.sucesso) {
        if (mensagem) {
            mensagem.innerText = resultado.erro ?? "Erro ao carregar os dados.";
        }

        return;
    }

    /* Verifica se houve erro na API de vendas. */
    if (!resultadoVendas.sucesso) {
        if (mensagem) {
            mensagem.innerText = resultadoVendas.erro ?? "Erro ao carregar as vendas.";
        }

        return;
    }

    /* Pega os elementos utilizados pelos filtros. */
    const filtroCategoria = document.getElementById("filtro-categoria") as HTMLSelectElement | null;
    const dataInicial = document.getElementById("data-inicial") as HTMLInputElement | null;
    const dataFinal = document.getElementById("data-final") as HTMLInputElement | null;

    /* Atualiza os dados sempre que algum filtro for alterado. */
    const atualizarFiltros = (): void => {
        const valorCategoria = filtroCategoria?.value ?? "";

        /* Converte a categoria selecionada para número. */
        const categoriaSelecionada: number | null = valorCategoria === "" ? null : Number(valorCategoria);

        let vendasFiltradas = resultadoVendas.dados;

        /* Aplica o filtro de categoria. */
        vendasFiltradas = filtrarVendasPorCategoria(
            vendasFiltradas,
            categoriaSelecionada
        );

        /* Aplica o filtro de período. */
        vendasFiltradas = filtrarVendasPorPeriodo(
            vendasFiltradas,
            dataInicial?.value || null,
            dataFinal?.value || null
        );

        /* Transforma os dados para exibição. */
        const produtosTransformados = transformarVendas(vendasFiltradas);

        /* Atualiza as métricas e a tabela. */
        exibirMetricas(vendasFiltradas);
        exibirTabela(produtosTransformados);

        /* Mostra os filtros e resultados no console. */
        console.log("Categoria:", categoriaSelecionada);
        console.log("Data inicial:", dataInicial?.value || "Todas");
        console.log("Data final:", dataFinal?.value || "Todas");
        console.log("Vendas filtradas:", vendasFiltradas);
    };

    /* Adiciona o evento de alteração ao filtro de categoria. */
    if (filtroCategoria) {
        filtroCategoria.addEventListener("change", atualizarFiltros);
    }

    /* Adiciona o evento de alteração à data inicial. */
    if (dataInicial) {
        dataInicial.addEventListener("change", atualizarFiltros);
    }

    /* Adiciona o evento de alteração à data final. */
    if (dataFinal) {
        dataFinal.addEventListener("change", atualizarFiltros);
    }

    /* Exibe o dashboard inicialmente sem filtros. */
    atualizarFiltros();
});