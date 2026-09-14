<?php

/* Verifica se o usuário está logado. */
if (!isset($_SESSION["fogoEsangue"])) {
    header("Location: login");
    exit;
}

/* Verifica se a compra foi enviada por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: carrinho/itens");
    exit;
}

/* Verifica se o carrinho possui produtos. */
if (!isset($_SESSION["carrinho"]) || count($_SESSION["carrinho"]) === 0) {
    header("Location: carrinho/itens");
    exit;
}

/* Pega os dados do usuário e da entrega. */
$idUsuario = (int)$_SESSION["fogoEsangue"];
$cep = preg_replace("/\D/", "", $_POST["cep"] ?? "");
$cidade = trim($_POST["cidade"] ?? "");
$uf = strtoupper(trim($_POST["uf"] ?? ""));
$freteInformado = (float)($_POST["frete"] ?? 0);

/* Valida os dados da entrega. */
if (strlen($cep) !== 8 || $cidade === "" || strlen($uf) !== 2 || $freteInformado < 0) {
    header("Location: finalizarCompra");
    exit;
}

/* Pega os produtos armazenados no carrinho. */
$carrinho = $_SESSION["carrinho"];
$ids = array_keys($carrinho);
$idsValidos = [];

/* Verifica quais IDs de produtos são válidos. */
foreach ($ids as $id) {
    if (filter_var($id, FILTER_VALIDATE_INT) !== false) {
        $idsValidos[] = (int)$id;
    }
}

if (count($idsValidos) === 0) {
    header("Location: carrinho/itens");
    exit;
}

try {
    /* Inicia a transação da compra. */
    $pdo->beginTransaction();

    /* Cria os espaços para os IDs dos produtos. */
    $placeholders = implode(",", array_fill(0, count($idsValidos), "?"));

    /* Busca os produtos e bloqueia os registros durante a compra. */
    $sql = "SELECT
                ID_PRODUTO,
                NM_PRODUTO,
                VL_PRODUTO,
                QT_ESTOQUE,
                FL_ATIVO
            FROM produto
            WHERE ID_PRODUTO IN ($placeholders)
            FOR UPDATE";

    $consulta = $pdo->prepare($sql);

    /* Adiciona os IDs dos produtos na consulta. */
    foreach ($idsValidos as $indice => $id) {
        $consulta->bindValue($indice + 1, $id, PDO::PARAM_INT);
    }

    $consulta->execute();

    /* Guarda os produtos encontrados. */
    $produtosBanco = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $produtosPedido = [];
    $valorProdutos = 0;

    /* Confere cada produto antes de finalizar a compra. */
    foreach ($idsValidos as $idProduto) {
        $produtoEncontrado = null;

        foreach ($produtosBanco as $produto) {
            if ((int)$produto["ID_PRODUTO"] === $idProduto) {
                $produtoEncontrado = $produto;
                break;
            }
        }

        if (!$produtoEncontrado) {
            throw new Exception("Um dos produtos do carrinho não foi encontrado.");
        }

        /* Verifica se o produto continua ativo. */
        if ((int)$produtoEncontrado["FL_ATIVO"] !== 1) {
            throw new Exception("Um dos produtos do carrinho está inativo.");
        }

        $quantidade = (int)($carrinho[$idProduto] ?? 0);
        $estoque = (int)$produtoEncontrado["QT_ESTOQUE"];

        /* Verifica a quantidade solicitada. */
        if ($quantidade < 1) {
            throw new Exception("Quantidade de produto inválida.");
        }

        /* Verifica se existe estoque suficiente. */
        if ($quantidade > $estoque) {
            throw new Exception("O produto " . $produtoEncontrado["NM_PRODUTO"] . " não possui estoque suficiente.");
        }

        /* Calcula o valor do produto e seu subtotal. */
        $valorUnitario = (float)$produtoEncontrado["VL_PRODUTO"];
        $subtotal = $valorUnitario * $quantidade;

        $produtosPedido[] = [
            "ID_PRODUTO" => $idProduto,
            "QUANTIDADE" => $quantidade,
            "VL_UNITARIO" => $valorUnitario,
            "VL_SUBTOTAL" => $subtotal
        ];

        $valorProdutos += $subtotal;
    }

    /* Calcula o valor total da compra. */
    $valorTotal = $valorProdutos + $freteInformado;

    /* Cadastra o pedido. */
    $sqlPedido = "INSERT INTO pedido (
                    ID_USUARIO,
                    VL_PRODUTOS,
                    VL_FRETE,
                    VL_TOTAL,
                    DS_CEP,
                    DS_CIDADE,
                    DS_UF,
                    DS_STATUS
                ) VALUES (
                    :usuario,
                    :produtos,
                    :frete,
                    :total,
                    :cep,
                    :cidade,
                    :uf,
                    'Pendente'
                )";

    $stmtPedido = $pdo->prepare($sqlPedido);
    $stmtPedido->execute([
        ":usuario" => $idUsuario,
        ":produtos" => $valorProdutos,
        ":frete" => $freteInformado,
        ":total" => $valorTotal,
        ":cep" => $cep,
        ":cidade" => $cidade,
        ":uf" => $uf
    ]);

    /* Pega o ID do pedido criado. */
    $idPedido = (int)$pdo->lastInsertId();

    /* Prepara o cadastro dos itens do pedido. */
    $sqlItem = "INSERT INTO pedido_item (
                    ID_PEDIDO,
                    ID_PRODUTO,
                    QT_PRODUTO,
                    VL_UNITARIO,
                    VL_SUBTOTAL
                ) VALUES (
                    :pedido,
                    :produto,
                    :quantidade,
                    :unitario,
                    :subtotal
                )";

    $stmtItem = $pdo->prepare($sqlItem);

    /* Prepara a atualização do estoque. */
    $sqlEstoque = "UPDATE produto
                   SET QT_ESTOQUE = QT_ESTOQUE - :quantidade
                   WHERE ID_PRODUTO = :produto
                   AND QT_ESTOQUE >= :quantidade";

    $stmtEstoque = $pdo->prepare($sqlEstoque);

    /* Salva os itens e diminui o estoque dos produtos. */
    foreach ($produtosPedido as $item) {
        $stmtItem->execute([
            ":pedido" => $idPedido,
            ":produto" => $item["ID_PRODUTO"],
            ":quantidade" => $item["QUANTIDADE"],
            ":unitario" => $item["VL_UNITARIO"],
            ":subtotal" => $item["VL_SUBTOTAL"]
        ]);

        $stmtEstoque->execute([
            ":produto" => $item["ID_PRODUTO"],
            ":quantidade" => $item["QUANTIDADE"]
        ]);

        /* Verifica se o estoque foi atualizado corretamente. */
        if ($stmtEstoque->rowCount() !== 1) {
            throw new Exception("Não foi possível atualizar o estoque de um dos produtos.");
        }
    }

    /* Confirma todos os dados da compra. */
    $pdo->commit();

    /* Limpa o carrinho depois da compra. */
    $_SESSION["carrinho"] = [];

    /* Mostra os detalhes do pedido realizado. */
    header("Location: pedidoConfirmado?id=" . $idPedido);
    exit;

} catch (Throwable $e) {
    /* Desfaz a compra caso aconteça algum erro. */
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    /* Guarda a mensagem de erro para a página de finalização. */
    $_SESSION["erroCompra"] = "Não foi possível finalizar a compra.";

    header("Location: finalizarCompra");
    exit;
}
?>