<?php

/* Verifica se o usuário está logado. */
if (!isset($_SESSION["fogoEsangue"])) {
    header("Location: " . $baseUrl . "/login");
    exit;
}

/* Cria o carrinho na sessão caso ele ainda não exista. */
if (!isset($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = [];
}

/* Pega a ação e o ID do produto. */
$acao = $subrota ?? "";
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: " . $baseUrl . "/loja");
    exit;
}

/* Busca o estoque e o status do produto. */
$sql = "SELECT
            ID_PRODUTO,
            QT_ESTOQUE,
            FL_ATIVO
        FROM produto
        WHERE ID_PRODUTO = :id
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

/* Guarda os dados do produto. */
$produto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$produto || (int)$produto["FL_ATIVO"] !== 1) {
    header("Location: " . $baseUrl . "/loja");
    exit;
}

$estoque = (int)$produto["QT_ESTOQUE"];
$quantidadeAtual = (int)($_SESSION["carrinho"][$id] ?? 0);

/* Adiciona uma unidade do produto ao carrinho. */
if ($acao === "adicionar") {
    if ($estoque > 0) {
        if ($quantidadeAtual < $estoque) {
            $_SESSION["carrinho"][$id] = $quantidadeAtual + 1;
        } else {
            $_SESSION["carrinho"][$id] = $estoque;
        }
    }

    /* Aumenta a quantidade do produto. */
} elseif ($acao === "mais") {
    if ($quantidadeAtual < $estoque) {
        $_SESSION["carrinho"][$id] = $quantidadeAtual + 1;
    }

    /* Diminui a quantidade do produto. */
} elseif ($acao === "menos") {
    if ($quantidadeAtual > 1) {
        $_SESSION["carrinho"][$id] = $quantidadeAtual - 1;
    } else {
        unset($_SESSION["carrinho"][$id]);
    }

    /* Remove o produto do carrinho. */
} elseif ($acao === "remover") {
    unset($_SESSION["carrinho"][$id]);

    /* Volta para o carrinho quando a ação não é reconhecida. */
} else {
    header("Location: " . $baseUrl . "/carrinho");
    exit;
}

/* Volta para a lista de produtos do carrinho. */
header("Location: " . $baseUrl . "/carrinho/itens");
exit;
