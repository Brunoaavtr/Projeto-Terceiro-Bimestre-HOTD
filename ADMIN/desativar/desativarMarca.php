<?php
/*
    Este arquivo desativa uma marca sem apagar o registro do banco.
    O campo FL_ATIVO passa de 1 para 0 e a marca deixa de aparecer
    na lista de marcas ativas, mas seus dados continuam salvos.

    Somente administradores podem executar esta ação pelo método POST.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: marcas");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    mensagem("Erro", "Marca inválida.", "error", "marcas");
    exit;
}

$sqlVerificar = "SELECT ID_MARCA
                 FROM marca
                 WHERE ID_MARCA = :id";

$consultaVerificar = $pdo->prepare($sqlVerificar);
$consultaVerificar->bindValue(":id", $id, PDO::PARAM_INT);
$consultaVerificar->execute();

if (!$consultaVerificar->fetch()) {
    mensagem("Erro", "Marca não encontrada.", "error", "marcas");
    exit;
}

$sql = "UPDATE marca
        SET FL_ATIVO = 0
        WHERE ID_MARCA = :id";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);

if ($consulta->execute()) {
    mensagem(
        "Marca desativada!",
        "A marca foi desativada com sucesso.",
        "success",
        "marcas"
    );
    exit;
}

mensagem(
    "Erro",
    "Não foi possível desativar a marca.",
    "error",
    "marcas"
);
