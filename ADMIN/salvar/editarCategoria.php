<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: categorias");
    exit;
}

/* Pega os dados enviados pelo formulário. */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");

if (!$id) {
    header("Location: categorias");
    exit;
}

/* Valida o nome e a descrição da categoria. */
if ($nome === "" || mb_strlen($nome) > 100 || mb_strlen($descricao) > 255) {
    header("Location: editarCategoria?id=" . $id);
    exit;
}

/* Verifica se a categoria existe. */
$sql = "SELECT ID_CATEGORIA
        FROM categoria
        WHERE ID_CATEGORIA = :id
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

/* Guarda a categoria encontrada. */
$categoria = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    header("Location: categorias");
    exit;
}

/* Verifica se outra categoria já possui o mesmo nome. */
$sql = "SELECT ID_CATEGORIA
        FROM categoria
        WHERE NM_CATEGORIA = :nome
        AND ID_CATEGORIA <> :id
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

/* Guarda a categoria que possui o mesmo nome. */
$categoriaExistente = $consulta->fetch(PDO::FETCH_ASSOC);

if ($categoriaExistente) {
    header("Location: editarCategoria?id=" . $id);
    exit;
}

/* Atualiza os dados da categoria. */
$sql = "UPDATE categoria
        SET NM_CATEGORIA = :nome,
            DS_CATEGORIA = :descricao
        WHERE ID_CATEGORIA = :id";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
$consulta->bindValue(":descricao", $descricao, PDO::PARAM_STR);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);

/* Verifica se a categoria foi atualizada. */
if ($consulta->execute()) {
    header("Location: categorias");
    exit;
}

/* Volta para a edição caso aconteça algum erro. */
header("Location: editarCategoria?id=" . $id);
exit;
