<?php
/*
    Esta página lista todos os pedidos realizados pelos usuários.
    O administrador pode visualizar cliente, data, status, endereço,
    valores e as opções de cada pedido.

    Cada pedido pode ser visualizado em detalhes ou ter seu status editado.
    Somente administradores podem acessar esta página.
    Os pedidos mais recentes aparecem primeiro.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

$sql = "SELECT p.ID_PEDIDO, p.VL_PRODUTOS, p.VL_FRETE, p.VL_TOTAL,
               p.DS_CEP, p.DS_CIDADE, p.DS_UF, p.DS_STATUS, p.DT_PEDIDO,
               u.NM_USUARIO, u.DS_EMAIL
        FROM pedido p
        INNER JOIN usuario u ON u.ID_USUARIO = p.ID_USUARIO
        ORDER BY p.DT_PEDIDO DESC, p.ID_PEDIDO DESC";

$consulta = $pdo->query($sql);
$pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card pedidosPainel">
            <div class="card-header text-center pedidosCabecalho">
                <h1>Pedidos</h1>
                <p>Gerencie os pedidos realizados no Covil do Dragão</p>
            </div>

            <div class="card-body p-4">
                <?php if (count($pedidos) > 0): ?>
                    <div class="listaPedidos row g-4">
                        <?php foreach ($pedidos as $pedido): ?>
                            <div class="col-md-6 pedidoColuna">
                                <div class="pedidoCard">
                                    <div class="pedidoInformacoes">
                                        <h2>Pedido #<?= (int)$pedido["ID_PEDIDO"] ?></h2>

                                        <p>
                                            <strong>Cliente:</strong>
                                            <?= htmlspecialchars($pedido["NM_USUARIO"]) ?>
                                        </p>

                                        <p>
                                            <strong>E-mail:</strong>
                                            <?= htmlspecialchars($pedido["DS_EMAIL"]) ?>
                                        </p>

                                        <p>
                                            <strong>Data:</strong>
                                            <?= date("d/m/Y H:i", strtotime($pedido["DT_PEDIDO"])) ?>
                                        </p>

                                        <p>
                                            <strong>Status:</strong>
                                            <?= htmlspecialchars($pedido["DS_STATUS"]) ?>
                                        </p>

                                        <p>
                                            <strong>Entrega:</strong>
                                            <?= htmlspecialchars($pedido["DS_CEP"]) ?> -
                                            <?= htmlspecialchars($pedido["DS_CIDADE"]) ?> -
                                            <?= htmlspecialchars($pedido["DS_UF"]) ?>
                                        </p>

                                        <p>
                                            <strong>Produtos:</strong>
                                            R$ <?= number_format((float)$pedido["VL_PRODUTOS"], 2, ",", ".") ?>
                                        </p>

                                        <p>
                                            <strong>Frete:</strong>
                                            R$ <?= number_format((float)$pedido["VL_FRETE"], 2, ",", ".") ?>
                                        </p>

                                        <p class="pedidoTotal">
                                            <strong>Total:</strong>
                                            R$ <?= number_format((float)$pedido["VL_TOTAL"], 2, ",", ".") ?>
                                        </p>
                                    </div>

                                    <div class="pedidoBotoes">
                                        <a href="detalhesPedido?id=<?= (int)$pedido["ID_PEDIDO"] ?>" class="btn botaoCovil">Ver pedido</a>
                                        <a href="editarPedido?id=<?= (int)$pedido["ID_PEDIDO"] ?>" class="btn botaoCovil">Editar status</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="pedidoVazio">
                        <h3>Nenhum pedido cadastrado</h3>
                        <p>Ainda não existem pedidos realizados no sistema.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>