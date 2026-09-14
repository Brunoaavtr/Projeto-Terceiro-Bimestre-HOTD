<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Pega o ID do pedido pela URL. */
$idPedido = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$idPedido) {
    header("Location: pedidos");
    exit;
}

/* Busca os dados do pedido. */
$sql = "SELECT
            p.ID_PEDIDO,
            p.VL_PRODUTOS,
            p.VL_FRETE,
            p.VL_TOTAL,
            p.DS_CEP,
            p.DS_CIDADE,
            p.DS_UF,
            p.DS_STATUS,
            p.DT_PEDIDO,
            u.NM_USUARIO,
            u.DS_EMAIL
        FROM pedido p
        INNER JOIN usuario u ON u.ID_USUARIO = p.ID_USUARIO
        WHERE p.ID_PEDIDO = :id
        LIMIT 1";

/* Executa a consulta do pedido. */
$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $idPedido, PDO::PARAM_INT);
$consulta->execute();

/* Guarda os dados encontrados. */
$pedido = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: pedidos");
    exit;
}

/* Define os status disponíveis para o pedido. */
$statusDisponiveis = [
    "Pendente",
    "Pago",
    "Enviado",
    "Entregue",
    "Cancelado"
];
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Editar pedido</h1>
                <p>Altere o status do pedido selecionado</p>
            </div>

            <div class="card-body p-4">
                <div class="mb-4">
                    <h2>Pedido #<?= (int)$pedido["ID_PEDIDO"] ?></h2>
                    <p>Cliente: <?= htmlspecialchars($pedido["NM_USUARIO"]) ?></p>
                    <p>E-mail: <?= htmlspecialchars($pedido["DS_EMAIL"]) ?></p>
                    <p>Data: <?= date("d/m/Y H:i", strtotime($pedido["DT_PEDIDO"])) ?></p>
                    <p>
                        Endereço:
                        <?= htmlspecialchars($pedido["DS_CEP"]) ?> -
                        <?= htmlspecialchars($pedido["DS_CIDADE"]) ?> -
                        <?= htmlspecialchars($pedido["DS_UF"]) ?>
                    </p>
                </div>

                <div class="carrinhoResumo mb-4">
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

                <!-- Formulário para alterar o status do pedido. -->
                <form method="post" action="index.php?param=salvar/editarPedido">
                    <input type="hidden" name="id" value="<?= (int)$pedido["ID_PEDIDO"] ?>">

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Status do pedido:</div>

                        <select name="status" id="status" class="form-select" required>
                            <?php foreach ($statusDisponiveis as $status): ?>
                                <option
                                    value="<?= htmlspecialchars($status) ?>"
                                    <?= $pedido["DS_STATUS"] === $status ? "selected" : "" ?>>
                                    <?= htmlspecialchars($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="cadastroMarcaBotoes">
                        <!-- Volta para a lista de pedidos. -->
                        <a href="pedidos" class="btn botaoCovil">Cancelar</a>

                        <!-- Salva o novo status do pedido. -->
                        <button type="submit" class="btn botaoCovil">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>