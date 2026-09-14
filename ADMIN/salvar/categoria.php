<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastrarCategoria");
    exit;
}

/* Pega os dados enviados pelo formulário. */
$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");

/* Valida o nome da categoria. */
if ($nome === "" || mb_strlen($nome) > 100) {
    header("Location: cadastrarCategoria");
    exit;
}

/* Valida o tamanho da descrição. */
if (mb_strlen($descricao) > 255) {
    header("Location: cadastrarCategoria");
    exit;
}

/* Verifica se já existe uma categoria com o mesmo nome. */
$sql = "SELECT ID_CATEGORIA
        FROM categoria
        WHERE NM_CATEGORIA = :nome
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
$consulta->execute();

/* Guarda a categoria encontrada. */
$categoriaExistente = $consulta->fetch(PDO::FETCH_ASSOC);

if ($categoriaExistente) {
    header("Location: cadastrarCategoria");
    exit;
}

/* Cadastra a nova categoria como ativa. */
$sql = "INSERT INTO categoria (
            NM_CATEGORIA,
            DS_CATEGORIA,
            FL_ATIVO
        ) VALUES (
            :nome,
            :descricao,
            1
        )";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
$consulta->bindValue(":descricao", $descricao, PDO::PARAM_STR);

/* Verifica se a categoria foi cadastrada. */
if ($consulta->execute()) {
    header("Location: categorias");
    exit;
}

/* Volta para o cadastro caso aconteça algum erro. */
header("Location: cadastrarCategoria");
exit;
