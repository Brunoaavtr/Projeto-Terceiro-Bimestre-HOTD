<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: marcas");
    exit;
}

/* Pega os dados enviados pelo formulário. */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$nome = trim($_POST["nome"] ?? "");

if (!$id || $nome === "") {
    mensagem("Erro", "Preencha corretamente os dados da marca.", "error", "marcas");
    exit;
}

/* Busca os dados atuais da marca. */
$sqlMarca = "SELECT ID_MARCA, NM_MARCA, DS_IMAGEM
             FROM marca
             WHERE ID_MARCA = :id";

$consultaMarca = $pdo->prepare($sqlMarca);
$consultaMarca->bindValue(":id", $id, PDO::PARAM_INT);
$consultaMarca->execute();

/* Guarda os dados da marca. */
$marcaAtual = $consultaMarca->fetch(PDO::FETCH_ASSOC);

if (!$marcaAtual) {
    mensagem("Erro", "Marca não encontrada.", "error", "marcas");
    exit;
}

/* Verifica se outra marca possui o mesmo nome. */
$sqlVerificar = "SELECT ID_MARCA
                 FROM marca
                 WHERE NM_MARCA = :nome
                 AND ID_MARCA <> :id";

$consultaVerificar = $pdo->prepare($sqlVerificar);
$consultaVerificar->bindValue(":nome", $nome, PDO::PARAM_STR);
$consultaVerificar->bindValue(":id", $id, PDO::PARAM_INT);
$consultaVerificar->execute();

if ($consultaVerificar->fetch()) {
    mensagem("Atenção", "Já existe outra marca com esse nome.", "warning", "editarMarca?id=" . $id);
    exit;
}

/* Mantém a imagem atual caso nenhuma nova seja enviada. */
$caminhoImagem = $marcaAtual["DS_IMAGEM"];
$imagemNova = false;

/* Verifica se uma nova imagem foi enviada. */
if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $imagem = $_FILES["imagem"];

    /* Verifica se a imagem foi recebida corretamente. */
    if ($imagem["error"] !== UPLOAD_ERR_OK) {
        mensagem("Erro", "Não foi possível receber a nova imagem.", "error", "editarMarca?id=" . $id);
        exit;
    }

    /* Verifica o tamanho da imagem. */
    if ($imagem["size"] > 5 * 1024 * 1024) {
        mensagem("Erro", "A imagem deve ter no máximo 5 MB.", "error", "editarMarca?id=" . $id);
        exit;
    }

    /* Verifica a extensão da imagem. */
    $extensao = strtolower(pathinfo($imagem["name"], PATHINFO_EXTENSION));
    $extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($extensao, $extensoesPermitidas)) {
        mensagem("Erro", "Formato de imagem não permitido. Use JPG, JPEG, PNG ou WEBP.", "error", "editarMarca?id=" . $id);
        exit;
    }

    /* Define a pasta onde a nova imagem será salva. */
    $pastaMarcas = __DIR__ . "/../../IMG/marcas/";

    if (!is_dir($pastaMarcas)) {
        mkdir($pastaMarcas, 0777, true);
    }

    /* Cria o nome da nova imagem. */
    $nomeImagem = uniqid("marca_", true) . "." . $extensao;
    $novoArquivo = $pastaMarcas . $nomeImagem;

    /* Salva a nova imagem na pasta de marcas. */
    if (!move_uploaded_file($imagem["tmp_name"], $novoArquivo)) {
        mensagem("Erro", "Não foi possível salvar a nova imagem.", "error", "editarMarca?id=" . $id);
        exit;
    }

    $caminhoImagem = "IMG/marcas/" . $nomeImagem;
    $imagemNova = true;
}

/* Atualiza os dados da marca. */
$sql = "UPDATE marca
        SET NM_MARCA = :nome,
            DS_IMAGEM = :imagem
        WHERE ID_MARCA = :id";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
$consulta->bindValue(":imagem", $caminhoImagem, PDO::PARAM_STR);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);

/* Verifica se a marca foi atualizada. */
if ($consulta->execute()) {

    /* Remove a imagem antiga depois da atualização. */
    if ($imagemNova && !empty($marcaAtual["DS_IMAGEM"])) {
        $imagemAntiga = __DIR__ . "/../../" . $marcaAtual["DS_IMAGEM"];

        if (file_exists($imagemAntiga)) {
            unlink($imagemAntiga);
        }
    }

    mensagem("Marca atualizada!", "As informações da marca foram alteradas com sucesso.", "success", "marcas");
    exit;
}

/* Remove a nova imagem caso a atualização do banco falhe. */
if ($imagemNova && isset($novoArquivo) && file_exists($novoArquivo)) {
    unlink($novoArquivo);
}

mensagem("Erro", "Não foi possível atualizar a marca.", "error", "editarMarca?id=" . $id);
