<?php
/*
    Este arquivo desativa um produto sem apagar o registro do banco.
    O campo FL_ATIVO passa de 1 para 0, fazendo com que o produto
    deixe de aparecer na lista de produtos ativos.

    Somente administradores podem realizar esta ação.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: produtos");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: produtos");
    exit;
}

$sql = "SELECT ID_PRODUTO
        FROM produto
        WHERE ID_PRODUTO = :id
        AND FL_ATIVO = 1
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

$produto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: produtos");
    exit;
}

$sql = "UPDATE produto
        SET FL_ATIVO = 0
        WHERE ID_PRODUTO = :id";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);

if ($consulta->execute()) {
    header("Location: produtos");
    exit;
}

header("Location: produtos");
exit;
