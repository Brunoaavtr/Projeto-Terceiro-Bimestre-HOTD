<?php
/*
    Este arquivo desativa uma categoria sem apagar o registro do banco.
    A categoria continua salva, mas o campo FL_ATIVO passa de 1 para 0.

    Somente administradores podem executar essa ação.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: categorias");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: categorias");
    exit;
}

$sql = "SELECT ID_CATEGORIA, FL_ATIVO
        FROM categoria
        WHERE ID_CATEGORIA = :id
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

$categoria = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$categoria || (int)$categoria["FL_ATIVO"] === 0) {
    header("Location: categorias");
    exit;
}

$sql = "UPDATE categoria
        SET FL_ATIVO = 0
        WHERE ID_CATEGORIA = :id";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

header("Location: categorias");
exit;
?>
