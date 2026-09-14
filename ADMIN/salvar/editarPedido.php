<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pedidos");
    exit;
}

/* Pega o ID do pedido e o novo status. */
$idPedido = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$status = trim($_POST["status"] ?? "");

/* Define os status permitidos para o pedido. */
$statusDisponiveis = [
    "Pendente",
    "Pago",
    "Enviado",
    "Entregue",
    "Cancelado"
];

/* Valida o ID e o status informado. */
if (!$idPedido || !in_array($status, $statusDisponiveis, true)) {
    header("Location: pedidos");
    exit;
}

/* Verifica se o pedido existe. */
$sql = "SELECT ID_PEDIDO
        FROM pedido
        WHERE ID_PEDIDO = :id
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $idPedido, PDO::PARAM_INT);
$consulta->execute();

/* Guarda o pedido encontrado. */
$pedido = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: pedidos");
    exit;
}

/* Atualiza o status do pedido. */
$sql = "UPDATE pedido
        SET DS_STATUS = :status
        WHERE ID_PEDIDO = :id";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":status", $status, PDO::PARAM_STR);
$consulta->bindValue(":id", $idPedido, PDO::PARAM_INT);
$consulta->execute();

/* Volta para a lista de pedidos. */
header("Location: pedidos");
exit;
?>