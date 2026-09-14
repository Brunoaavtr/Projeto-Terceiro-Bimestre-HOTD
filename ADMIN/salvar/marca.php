<?php
/*
    Processa o cadastro de uma nova marca.

    Este arquivo valida o administrador, nome e imagem; impede nomes duplicados;
    salva a imagem em IMG/marcas; cadastra a marca no banco e remove o arquivo
    caso o INSERT falhe. Todas as falhas retornam para o formulário com uma
    mensagem SweetAlert, evitando a antiga tela em branco da rota salvar/marca.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: " . $baseUrl . "/home");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . $baseUrl . "/cadastrarMarca");
    exit;
}

$nome = trim($_POST["nome"] ?? "");

if ($nome === "") {
    mensagem("Erro", "Digite o nome da marca.", "error", "cadastrarMarca");
}

if (!isset($_FILES["imagem"])) {
    mensagem("Erro", "Selecione uma imagem para a marca.", "error", "cadastrarMarca");
}

$imagem = $_FILES["imagem"];

$mensagensUpload = [
    UPLOAD_ERR_INI_SIZE => "A imagem ultrapassa o limite de upload configurado no PHP.",
    UPLOAD_ERR_FORM_SIZE => "A imagem ultrapassa o tamanho permitido pelo formulário.",
    UPLOAD_ERR_PARTIAL => "A imagem foi enviada apenas parcialmente. Tente novamente.",
    UPLOAD_ERR_NO_FILE => "Selecione uma imagem para a marca.",
    UPLOAD_ERR_NO_TMP_DIR => "A pasta temporária de upload não está disponível.",
    UPLOAD_ERR_CANT_WRITE => "O servidor não conseguiu gravar a imagem.",
    UPLOAD_ERR_EXTENSION => "Uma extensão do PHP interrompeu o upload da imagem."
];

if ($imagem["error"] !== UPLOAD_ERR_OK) {
    $texto = $mensagensUpload[$imagem["error"]] ?? "Não foi possível receber a imagem.";
    mensagem("Erro no upload", $texto, "error", "cadastrarMarca");
}

if ((int)$imagem["size"] <= 0 || (int)$imagem["size"] > 5 * 1024 * 1024) {
    mensagem("Erro", "A imagem deve ter no máximo 5 MB.", "error", "cadastrarMarca");
}

$extensao = strtolower(pathinfo($imagem["name"], PATHINFO_EXTENSION));
$extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];

if (!in_array($extensao, $extensoesPermitidas, true)) {
    mensagem("Erro", "Formato não permitido. Use JPG, JPEG, PNG ou WEBP.", "error", "cadastrarMarca");
}

$dadosImagem = @getimagesize($imagem["tmp_name"]);
$tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

if ($dadosImagem === false || !in_array($dadosImagem["mime"] ?? "", $tiposPermitidos, true)) {
    mensagem("Erro", "O arquivo selecionado não é uma imagem válida.", "error", "cadastrarMarca");
}

try {
    $sqlVerificar = "SELECT ID_MARCA FROM marca WHERE NM_MARCA = :nome LIMIT 1";
    $consultaVerificar = $pdo->prepare($sqlVerificar);
    $consultaVerificar->bindValue(":nome", $nome, PDO::PARAM_STR);
    $consultaVerificar->execute();

    if ($consultaVerificar->fetch()) {
        mensagem("Atenção", "Essa marca já está cadastrada.", "warning", "cadastrarMarca");
    }

    $pastaMarcas = __DIR__ . "/../../IMG/marcas/";

    if (!is_dir($pastaMarcas) && !mkdir($pastaMarcas, 0777, true)) {
        throw new RuntimeException("Não foi possível criar a pasta IMG/marcas.");
    }

    if (!is_writable($pastaMarcas)) {
        throw new RuntimeException("A pasta IMG/marcas não possui permissão de escrita.");
    }

    $nomeImagem = uniqid("marca_", true) . "." . $extensao;
    $caminhoImagem = $pastaMarcas . $nomeImagem;

    if (!move_uploaded_file($imagem["tmp_name"], $caminhoImagem)) {
        throw new RuntimeException("Não foi possível mover a imagem enviada para IMG/marcas.");
    }

    try {
        $caminhoBanco = "IMG/marcas/" . $nomeImagem;
        $sql = "INSERT INTO marca (NM_MARCA, FL_ATIVO, DS_IMAGEM)
                VALUES (:nome, 1, :imagem)";

        $consulta = $pdo->prepare($sql);
        $consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
        $consulta->bindValue(":imagem", $caminhoBanco, PDO::PARAM_STR);
        $consulta->execute();
    } catch (Throwable $e) {
        if (file_exists($caminhoImagem)) {
            unlink($caminhoImagem);
        }
        throw $e;
    }

    mensagem("Marca cadastrada!", "A marca foi cadastrada com sucesso.", "success", "marcas");
} catch (Throwable $e) {
    error_log("[Cadastro de marca] " . $e->getMessage());
    mensagem("Erro", "Não foi possível cadastrar a marca. Verifique o banco e as permissões da pasta de imagens.", "error", "cadastrarMarca");
}
?>