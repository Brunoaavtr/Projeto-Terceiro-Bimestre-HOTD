/* Define os dados dos produtos exibidos no dashboard*/
export interface ProdutoDashboard {
    ID_PRODUTO: number;
    NM_PRODUTO: string;
    NM_MARCA: string;
    ID_CATEGORIA: number;
    NM_CATEGORIA: string;
    QT_VENDIDA: number | null;
    VL_FATURADO: number | null;
}

/* Define a resposta recebida pela API do dashboard*/
export interface RespostaDashboard {
    sucesso: boolean;
    pagina: number;
    por_pagina: number;
    categoria: number | null;
    dados: ProdutoDashboard[];
    erro?: string;
}

/* Define os dados detalhados de cada venda */
export interface VendaDetalhada {
    ID_PEDIDO: number;
    DT_PEDIDO: string;
    ID_PRODUTO: number;
    NM_PRODUTO: string;
    NM_MARCA: string;
    ID_CATEGORIA: number;
    NM_CATEGORIA: string;
    QT_PRODUTO: number;
    VL_UNITARIO: number;
    VL_SUBTOTAL: number;
}

/* Define a resposta recebida pela API de vendas detalhadas */
export interface RespostaVendas {
    sucesso: boolean;
    categoria: number | null;
    data_inicial: string | null;
    data_final: string | null;
    dados: VendaDetalhada[];
    erro?: string;
}