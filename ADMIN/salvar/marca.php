<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastrarMarca");
    exit;
}

/* Pega o nome da marca enviado pelo formulário. */
$nome = trim($_POST["nome"] ?? "");

if ($nome === "") {
    mensagem("Erro", "Digite o nome da marca.", "error", "cadastrarMarca");
    exit;
}

/* Verifica se uma imagem foi enviada. */
if (!isset($_FILES["imagem"]) || $_FILES["imagem"]["error"] !== UPLOAD_ERR_OK) {
    mensagem("Erro", "Selecione uma imagem para a marca.", "error", "cadastrarMarca");
    exit;
}

$imagem = $_FILES["imagem"];

/* Verifica o tamanho da imagem. */
if ($imagem["size"] > 5 * 1024 * 1024) {
    mensagem("Erro", "A imagem deve ter no máximo 5 MB.", "error", "cadastrarMarca");
    exit;
}

/* Verifica a extensão da imagem. */
$extensao = strtolower(pathinfo($imagem["name"], PATHINFO_EXTENSION));
$extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];

if (!in_array($extensao, $extensoesPermitidas)) {
    mensagem(
        "Erro",
        "Formato de imagem não permitido. Use JPG, JPEG, PNG ou WEBP.",
        "error",
        "cadastrarMarca"
    );
    exit;
}

/* Verifica se a marca já está cadastrada. */
$sqlVerificar = "SELECT ID_MARCA
                 FROM marca
                 WHERE NM_MARCA = :nome";

$consultaVerificar = $pdo->prepare($sqlVerificar);
$consultaVerificar->bindValue(":nome", $nome, PDO::PARAM_STR);
$consultaVerificar->execute();

if ($consultaVerificar->fetch()) {
    mensagem(
        "Atenção",
        "Essa marca já está cadastrada.",
        "warning",
        "cadastrarMarca"
    );
    exit;
}

/* Define a pasta onde a imagem será salva. */
$pastaMarcas = __DIR__ . "/../../IMG/marcas/";

if (!is_dir($pastaMarcas)) {
    mkdir($pastaMarcas, 0777, true);
}

/* Cria o nome e o caminho da imagem. */
$nomeImagem = uniqid("marca_", true) . "." . $extensao;
$caminhoImagem = $pastaMarcas . $nomeImagem;

/* Salva a imagem na pasta de marcas. */
if (!move_uploaded_file($imagem["tmp_name"], $caminhoImagem)) {
    mensagem(
        "Erro",
        "Não foi possível salvar a imagem da marca.",
        "error",
        "cadastrarMarca"
    );
    exit;
}

/* Define o caminho da imagem que será salvo no banco. */
$caminhoBanco = "IMG/marcas/" . $nomeImagem;

/* Cadastra a marca no banco de dados. */
$sql = "INSERT INTO marca (
            NM_MARCA,
            FL_ATIVO,
            DS_IMAGEM
        ) VALUES (
            :nome,
            1,
            :imagem
        )";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
$consulta->bindValue(":imagem", $caminhoBanco, PDO::PARAM_STR);

/* Verifica se a marca foi cadastrada. */
if ($consulta->execute()) {
    mensagem(
        "Marca cadastrada!",
        "A marca foi cadastrada com sucesso.",
        "success",
        "marcas"
    );
    exit;
}

/* Remove a imagem caso o cadastro no banco falhe. */
if (file_exists($caminhoImagem)) {
    unlink($caminhoImagem);
}

mensagem(
    "Erro",
    "Não foi possível cadastrar a marca.",
    "error",
    "cadastrarMarca"
);
?>