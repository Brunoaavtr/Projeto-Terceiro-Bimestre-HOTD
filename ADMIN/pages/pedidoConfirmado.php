<?php

/* Verifica se o usuário está logado. */
if (!isset($_SESSION["fogoEsangue"])) {
    header("Location: login");
    exit;
}

/* Pega o ID do pedido pela URL. */
$idPedido = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$idPedido) {
    header("Location: pedidos");
    exit;
}

/* Pega o ID do usuário logado. */
$idUsuario = (int)$_SESSION["fogoEsangue"];

/* Busca o pedido do usuário. */
$sql = "SELECT
            ID_PEDIDO,
            ID_USUARIO,
            VL_PRODUTOS,
            VL_FRETE,
            VL_TOTAL,
            DS_CEP,
            DS_CIDADE,
            DS_UF,
            DS_STATUS,
            DT_PEDIDO
        FROM pedido
        WHERE ID_PEDIDO = :pedido
        AND ID_USUARIO = :usuario
        LIMIT 1";

/* Executa a consulta do pedido. */
$consulta = $pdo->prepare($sql);
$consulta->bindValue(":pedido", $idPedido, PDO::PARAM_INT);
$consulta->bindValue(":usuario", $idUsuario, PDO::PARAM_INT);
$consulta->execute();

/* Guarda os dados do pedido. */
$pedido = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: pedidos");
    exit;
}

/* Busca os produtos que fazem parte do pedido. */
$sqlItens = "SELECT
                pi.ID_PRODUTO,
                pi.QT_PRODUTO,
                pi.VL_UNITARIO,
                pi.VL_SUBTOTAL,
                p.NM_PRODUTO,
                (
                    SELECT pim.DS_IMAGEM
                    FROM produto_imagem pim
                    WHERE pim.ID_PRODUTO = p.ID_PRODUTO
                    ORDER BY pim.FL_PRINCIPAL DESC, pim.NR_ORDEM ASC
                    LIMIT 1
                ) AS DS_IMAGEM
            FROM pedido_item pi
            INNER JOIN produto p ON p.ID_PRODUTO = pi.ID_PRODUTO
            WHERE pi.ID_PEDIDO = :pedido
            ORDER BY pi.ID_PEDIDO_ITEM";

/* Executa a consulta dos itens do pedido. */
$consultaItens = $pdo->prepare($sqlItens);
$consultaItens->bindValue(":pedido", $idPedido, PDO::PARAM_INT);
$consultaItens->execute();

/* Guarda os itens encontrados. */
$itens = $consultaItens->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Detalhes do pedido</h1>
                <p>Confira as informações da sua compra.</p>
            </div>

            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h2>
                        Pedido #<?= (int)$pedido["ID_PEDIDO"] ?>
                    </h2>

                    <p>
                        Status:
                        <?= htmlspecialchars($pedido["DS_STATUS"]) ?>
                    </p>

                    <p>
                        Data:
                        <?= date("d/m/Y H:i", strtotime($pedido["DT_PEDIDO"])) ?>
                    </p>
                </div>

                <h2 class="mb-4">Itens do pedido</h2>

                <?php foreach ($itens as $item): ?>
                    <div class="carrinhoProduto">
                        <div class="carrinhoImagem">

                            <!-- Mostra a imagem do produto. -->
                            <?php if (!empty($item["DS_IMAGEM"])): ?>
                                <img
                                    src="<?= htmlspecialchars($item["DS_IMAGEM"]) ?>"
                                    alt="<?= htmlspecialchars($item["NM_PRODUTO"]) ?>">
                            <?php else: ?>
                                <span>Nenhuma imagem cadastrada.</span>
                            <?php endif; ?>

                        </div>

                        <div class="carrinhoInformacoes">
                            <h3>
                                <?= htmlspecialchars($item["NM_PRODUTO"]) ?>
                            </h3>

                            <p>
                                Quantidade:
                                <?= (int)$item["QT_PRODUTO"] ?>
                            </p>

                            <p>
                                Preço unitário:
                                R$ <?= number_format((float)$item["VL_UNITARIO"], 2, ",", ".") ?>
                            </p>

                            <p class="carrinhoSubtotal">
                                Subtotal:
                                R$ <?= number_format((float)$item["VL_SUBTOTAL"], 2, ",", ".") ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="carrinhoResumo mt-4">
                    <p>
                        Produtos:
                        R$ <?= number_format((float)$pedido["VL_PRODUTOS"], 2, ",", ".") ?>
                    </p>

                    <p>
                        Frete:
                        R$ <?= number_format((float)$pedido["VL_FRETE"], 2, ",", ".") ?>
                    </p>

                    <h2>
                        Total:
                        R$ <?= number_format((float)$pedido["VL_TOTAL"], 2, ",", ".") ?>
                    </h2>
                </div>

                <div class="mt-4">
                    <h2>Endereço de entrega</h2>

                    <p>
                        CEP:
                        <?= htmlspecialchars($pedido["DS_CEP"]) ?>
                    </p>

                    <p>
                        Cidade:
                        <?= htmlspecialchars($pedido["DS_CIDADE"]) ?>
                    </p>

                    <p>
                        Estado:
                        <?= htmlspecialchars($pedido["DS_UF"]) ?>
                    </p>
                </div>

                <div class="cadastroMarcaBotoes mt-4">

                    <!-- Volta para a lista de pedidos. -->
                    <a href="pedidos" class="btn botaoCovil">
                        Voltar para meus pedidos
                    </a>

                    <!-- Leva o usuário novamente para a loja. -->
                    <a href="loja" class="btn botaoCovil">
                        Continuar comprando
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>