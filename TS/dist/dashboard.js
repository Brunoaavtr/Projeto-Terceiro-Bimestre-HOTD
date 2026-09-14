/*
    Este arquivo é responsável pela comunicação entre o dashboard
    e as APIs PHP.

    O TypeScript utiliza fetch() com async/await para buscar os dados
    das APIs e depois utiliza o DOM para apresentar essas informações.

    Também são utilizadas funções de processamento de arrays, como
    reduce() para calcular as métricas e o faturamento, filter() para
    aplicar os filtros de categoria e período, map() para transformar
    os dados recebidos da API antes de exibi-los e um objeto do tipo
    Record para realizar o ranking do produto mais vendido.

    O dashboard utiliza duas APIs:

    dashboard.php
        Retorna os produtos consolidados através da Stored Procedure.

    vendas.php
        Retorna os dados detalhados das vendas através da View
        vw_dashboard_vendas_detalhadas.

    O fluxo fica:

    Dashboard
        ↓
    TypeScript
        ↓
    fetch()
        ↓
    API PHP
        ↓
    View / Stored Procedure
        ↓
    MariaDB
*/
async function carregarDashboard() {
    try {
        const resposta = await fetch("ADMIN/api/dashboard.php");
        if (!resposta.ok) {
            throw new Error(`Erro na requisição: Status ${resposta.status}`);
        }
        const dados = await resposta.json();
        dados.dados = dados.dados.map((produto) => {
            return {
                ID_PRODUTO: Number(produto.ID_PRODUTO),
                NM_PRODUTO: produto.NM_PRODUTO,
                NM_MARCA: produto.NM_MARCA,
                ID_CATEGORIA: Number(produto.ID_CATEGORIA),
                NM_CATEGORIA: produto.NM_CATEGORIA,
                QT_VENDIDA: produto.QT_VENDIDA === null
                    ? null
                    : Number(produto.QT_VENDIDA),
                VL_FATURADO: produto.VL_FATURADO === null
                    ? null
                    : Number(produto.VL_FATURADO)
            };
        });
        return dados;
    }
    catch (erro) {
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
async function carregarVendas() {
    try {
        const resposta = await fetch("ADMIN/api/vendas.php");
        if (!resposta.ok) {
            throw new Error(`Erro na requisição: Status ${resposta.status}`);
        }
        const dados = await resposta.json();
        dados.dados = dados.dados.map((venda) => {
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
    }
    catch (erro) {
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
function filtrarVendasPorCategoria(vendas, categoriaSelecionada) {
    return vendas.filter((venda) => {
        return (categoriaSelecionada === null ||
            venda.ID_CATEGORIA === categoriaSelecionada);
    });
}
function filtrarVendasPorPeriodo(vendas, dataInicial, dataFinal) {
    return vendas.filter((venda) => {
        const dataVenda = new Date(venda.DT_PEDIDO);
        if (Number.isNaN(dataVenda.getTime())) {
            return false;
        }
        const inicio = dataInicial
            ? new Date(`${dataInicial}T00:00:00`)
            : null;
        const fim = dataFinal
            ? new Date(`${dataFinal}T23:59:59`)
            : null;
        if (inicio && dataVenda < inicio) {
            return false;
        }
        if (fim && dataVenda > fim) {
            return false;
        }
        return true;
    });
}
function calcularMetricasVendas(vendas) {
    const quantidadeVendida = vendas.reduce((total, venda) => {
        const quantidade = Number(venda.QT_PRODUTO);
        if (!Number.isFinite(quantidade)) {
            return total;
        }
        return total + quantidade;
    }, 0);
    const faturamento = vendas.reduce((total, venda) => {
        const quantidade = Number(venda.QT_PRODUTO);
        const valorUnitario = Number(venda.VL_UNITARIO);
        if (!Number.isFinite(quantidade) ||
            !Number.isFinite(valorUnitario)) {
            return total;
        }
        return total + quantidade * valorUnitario;
    }, 0);
    const produtosUnicos = new Set();
    vendas.forEach((venda) => {
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
function encontrarProdutoMaisVendidoVendas(vendas) {
    if (vendas.length === 0) {
        return null;
    }
    const frequenciaProdutos = {};
    const nomesProdutos = {};
    vendas.forEach((venda) => {
        const idProduto = Number(venda.ID_PRODUTO);
        const quantidade = Number(venda.QT_PRODUTO);
        if (!Number.isFinite(idProduto) ||
            !Number.isFinite(quantidade)) {
            return;
        }
        frequenciaProdutos[idProduto] =
            (frequenciaProdutos[idProduto] ?? 0) +
                quantidade;
        nomesProdutos[idProduto] = venda.NM_PRODUTO;
    });
    let idMaisVendido = null;
    let maiorQuantidade = 0;
    Object.entries(frequenciaProdutos).forEach(([id, quantidade]) => {
        if (quantidade > maiorQuantidade) {
            maiorQuantidade = quantidade;
            idMaisVendido = Number(id);
        }
    });
    if (idMaisVendido === null) {
        return null;
    }
    return {
        nome: nomesProdutos[idMaisVendido],
        quantidade: maiorQuantidade
    };
}
function transformarVendas(vendas) {
    return vendas.map((venda) => {
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
function exibirMetricas(vendas) {
    const totalVendido = document.getElementById("total-vendido");
    const faturamentoTotal = document.getElementById("faturamento-total");
    const produtosVendidos = document.getElementById("produtos-vendidos");
    const produtoMaisVendido = document.getElementById("produto-mais-vendido");
    if (!totalVendido ||
        !faturamentoTotal ||
        !produtosVendidos ||
        !produtoMaisVendido) {
        return;
    }
    if (vendas.length === 0) {
        totalVendido.innerText = "0";
        faturamentoTotal.innerText = "R$ 0,00";
        produtosVendidos.innerText = "0";
        produtoMaisVendido.innerText = "Nenhum";
        return;
    }
    const metricas = calcularMetricasVendas(vendas);
    const produtoRanking = encontrarProdutoMaisVendidoVendas(vendas);
    totalVendido.innerText =
        metricas.quantidadeVendida.toString();
    faturamentoTotal.innerText =
        metricas.faturamento.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });
    produtosVendidos.innerText =
        metricas.produtosVendidos.toString();
    if (produtoRanking) {
        produtoMaisVendido.innerText =
            `${produtoRanking.nome} (${produtoRanking.quantidade} un.)`;
    }
    else {
        produtoMaisVendido.innerText = "Nenhum";
    }
}
function exibirTabela(produtos) {
    const tabela = document.getElementById("tabela-dashboard");
    if (!tabela) {
        return;
    }
    tabela.innerHTML = "";
    if (produtos.length === 0) {
        tabela.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">
                    Nenhum dado registrado.
                </td>
            </tr>
        `;
        return;
    }
    produtos.forEach((produto) => {
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
}
document.addEventListener("DOMContentLoaded", async () => {
    const resultado = await carregarDashboard();
    const resultadoVendas = await carregarVendas();
    console.log("Dados recebidos da API do dashboard:", resultado);
    console.log("Dados recebidos da API de vendas:", resultadoVendas);
    const mensagem = document.getElementById("mensagem-dashboard");
    if (!resultado.sucesso) {
        if (mensagem) {
            mensagem.innerText =
                resultado.erro ??
                    "Erro ao carregar os dados.";
        }
        return;
    }
    if (!resultadoVendas.sucesso) {
        if (mensagem) {
            mensagem.innerText =
                resultadoVendas.erro ??
                    "Erro ao carregar as vendas.";
        }
        return;
    }
    const filtroCategoria = document.getElementById("filtro-categoria");
    const dataInicial = document.getElementById("data-inicial");
    const dataFinal = document.getElementById("data-final");
    const atualizarFiltros = () => {
        const valorCategoria = filtroCategoria?.value ?? "";
        const categoriaSelecionada = valorCategoria === ""
            ? null
            : Number(valorCategoria);
        let vendasFiltradas = resultadoVendas.dados;
        vendasFiltradas =
            filtrarVendasPorCategoria(vendasFiltradas, categoriaSelecionada);
        vendasFiltradas =
            filtrarVendasPorPeriodo(vendasFiltradas, dataInicial?.value || null, dataFinal?.value || null);
        const produtosTransformados = transformarVendas(vendasFiltradas);
        exibirMetricas(vendasFiltradas);
        exibirTabela(produtosTransformados);
        console.log("Categoria:", categoriaSelecionada);
        console.log("Data inicial:", dataInicial?.value || "Todas");
        console.log("Data final:", dataFinal?.value || "Todas");
        console.log("Vendas filtradas:", vendasFiltradas);
    };
    if (filtroCategoria) {
        filtroCategoria.addEventListener("change", atualizarFiltros);
    }
    if (dataInicial) {
        dataInicial.addEventListener("change", atualizarFiltros);
    }
    if (dataFinal) {
        dataFinal.addEventListener("change", atualizarFiltros);
    }
    atualizarFiltros();
});
export {};
