<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Conta as marcas ativas. */
$sqlMarcas = "SELECT COUNT(*) FROM marca WHERE FL_ATIVO = 1";
$quantidadeMarcas = (int)$pdo->query($sqlMarcas)->fetchColumn();

/* Conta as categorias ativas. */
$sqlCategorias = "SELECT COUNT(*) FROM categoria WHERE FL_ATIVO = 1";
$quantidadeCategorias = (int)$pdo->query($sqlCategorias)->fetchColumn();

/* Conta os produtos ativos. */
$sqlProdutos = "SELECT COUNT(*) FROM produto WHERE FL_ATIVO = 1";
$quantidadeProdutos = (int)$pdo->query($sqlProdutos)->fetchColumn();

/* Conta os pedidos registrados. */
$sqlPedidos = "SELECT COUNT(*) FROM pedido";
$quantidadePedidos = (int)$pdo->query($sqlPedidos)->fetchColumn();

?>

<div class="container py-5">
    <div class="informacoesAdmin">
        <div class="adminCabecalho text-center">
            <h1>Área administrativa</h1>
            <p>Gerencie os dados do Covil do Dragão</p>
        </div>
        <div class="adminCards">
            <div class="adminCard marcasCard">
                <i class="fa-solid fa-tags"></i>
                <h2>Marcas</h2>
                <p><?= $quantidadeMarcas ?> marcas ativas cadastradas</p>
                <div class="adminOpcoes">
                    <a href="marcas" class="botaoAdmin">Listar</a>
                    <a href="cadastrarMarca" class="botaoAdmin">Cadastrar</a>
                </div>
            </div>
            <div class="adminCard categoriasCard">
                <i class="fa-solid fa-layer-group"></i>
                <h2>Categorias</h2>
                <p><?= $quantidadeCategorias ?> categorias ativas cadastradas</p>
                <div class="adminOpcoes">
                    <a href="categorias" class="botaoAdmin">Listar</a>
                    <a href="cadastrarCategoria" class="botaoAdmin">Cadastrar</a>
                </div>
            </div>
            <div class="adminCard produtosCard">
                <i class="fa-solid fa-box-open"></i>
                <h2>Produtos</h2>
                <p><?= $quantidadeProdutos ?> produtos ativos cadastrados</p>
                <div class="adminOpcoes">
                    <a href="produtos" class="botaoAdmin">Listar</a>
                    <a href="cadastrarProduto" class="botaoAdmin">Cadastrar</a>
                </div>
            </div>
            <div class="adminCard pedidosCard">
                <i class="fa-solid fa-clipboard-list"></i>
                <h2>Pedidos</h2>
                <p><?= $quantidadePedidos ?> pedidos registrados</p>
                <div class="adminOpcoes">
                    <a href="pedidos" class="botaoAdmin">Listar pedidos</a>
                </div>
            </div>
            <div class="adminCard dashboardCard">
                <i class="fa-solid fa-chart-line"></i>
                <h2>Dashboard</h2>
                <p>Visualize os dados e indicadores de vendas</p>
                <div class="adminOpcoes">
                    <a href="dashboard" class="botaoAdmin">Visualizar</a>
                </div>
            </div>
        </div>
    </div>
</div>