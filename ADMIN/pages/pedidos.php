<?php

/* Verifica se o usuário está logado. */
if (!isset($_SESSION["fogoEsangue"])) {
    header("Location: login");
    exit;
}

/* Pega o ID do usuário logado. */
$idUsuario = $_SESSION["fogoEsangue"];

/* Busca os pedidos realizados pelo usuário. */
$sqlPedidos = "SELECT
                    ID_PEDIDO,
                    VL_PRODUTOS,
                    VL_FRETE,
                    VL_TOTAL,
                    DS_CEP,
                    DS_CIDADE,
                    DS_UF,
                    DS_STATUS,
                    DT_PEDIDO
               FROM pedido
               WHERE ID_USUARIO = :idUsuario
               ORDER BY DT_PEDIDO DESC";

/* Executa a consulta dos pedidos. */
$consultaPedidos = $pdo->prepare($sqlPedidos);
$consultaPedidos->execute([
    ":idUsuario" => $idUsuario
]);

/* Guarda os pedidos encontrados. */
$pedidos = $consultaPedidos->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pedidosUsuarioPainel">
    <div class="card-body p-4">
        <?php if (count($pedidos) > 0): ?>
            <div class="listaPedidos">
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedidoCard">
                        <div class="pedidoInformacoes">
                            <h2>Pedido #<?= (int)$pedido["ID_PEDIDO"] ?></h2>
                            <p>Data: <?= date("d/m/Y H:i", strtotime($pedido["DT_PEDIDO"])) ?></p>
                            <p>Status: <?= htmlspecialchars($pedido["DS_STATUS"]) ?></p>
                            <p>Endereço: <?= htmlspecialchars($pedido["DS_CIDADE"]) ?> - <?= htmlspecialchars($pedido["DS_UF"]) ?></p>
                            <p>Valor dos produtos: R$ <?= number_format((float)$pedido["VL_PRODUTOS"], 2, ",", ".") ?></p>
                            <p>Frete: R$ <?= number_format((float)$pedido["VL_FRETE"], 2, ",", ".") ?></p>
                            <p class="pedidoTotal">Total: R$ <?= number_format((float)$pedido["VL_TOTAL"], 2, ",", ".") ?></p>
                        </div>

                        <div class="pedidoBotoes">
                            <!-- Acessa os detalhes do pedido. -->
                            <a href="pedidoConfirmado?id=<?= (int)$pedido["ID_PEDIDO"] ?>" class="btn botaoCovil">Ver pedido</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="carrinhoResumoBotoes mt-4">
                <!-- Volta para a área de compras. -->
                <a href="carrinho" class="btn botaoCovil">Voltar para compras</a>

                <!-- Leva o usuário para a loja. -->
                <a href="loja" class="btn botaoCovil">Continuar comprando</a>
            </div>

        <?php else: ?>
            <div class="pedidoVazio">
                <h3>Nenhum pedido realizado</h3>
                <p>Você ainda não realizou nenhuma compra no Covil do Dragão.</p>

                <div class="carrinhoResumoBotoes">
                    <!-- Volta para a área de compras. -->
                    <a href="carrinho" class="btn botaoCovil">Voltar para compras</a>

                    <!-- Leva o usuário para a loja. -->
                    <a href="loja" class="btn botaoCovil">Ir para a loja</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>