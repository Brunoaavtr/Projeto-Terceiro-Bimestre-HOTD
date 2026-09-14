<?php
/*
    Funções auxiliares usadas no projeto Covil do Dragão.

    A função mensagem() agora não imprime JavaScript durante uma rota de
    processamento. Em vez disso, ela guarda a mensagem na sessão e redireciona
    o usuário para uma página normal do sistema. O index.php lê essa mensagem
    depois que o SweetAlert2 já foi carregado e então exibe o aviso.

    Isso evita a tela em branco que acontecia em rotas como salvar/marca,
    porque antes Swal.fire() era executado antes do carregamento da biblioteca.
*/

function redimensionarImagem($origem, $larguraMax, $alturaMax, $qualidade = 100)
{
    $destino = $origem;

    if (!file_exists($origem)) {
        return false;
    }

    [$larguraOriginal, $alturaOriginal, $tipo] = getimagesize($origem);
    $proporcao = $larguraOriginal / $alturaOriginal;

    if ($larguraMax / $alturaMax > $proporcao) {
        $novaLargura = $alturaMax * $proporcao;
        $novaAltura = $alturaMax;
    } else {
        $novaLargura = $larguraMax;
        $novaAltura = $larguraMax / $proporcao;
    }

    $novaImagem = imagecreatetruecolor((int)$novaLargura, (int)$novaAltura);

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagem = imagecreatefromjpeg($origem);
            break;

        case IMAGETYPE_PNG:
            $imagem = imagecreatefrompng($origem);
            imagealphablending($novaImagem, false);
            imagesavealpha($novaImagem, true);
            break;

        default:
            return false;
    }

    imagecopyresampled(
        $novaImagem,
        $imagem,
        0,
        0,
        0,
        0,
        (int)$novaLargura,
        (int)$novaAltura,
        $larguraOriginal,
        $alturaOriginal
    );

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            imagejpeg($novaImagem, $destino, $qualidade);
            break;

        case IMAGETYPE_PNG:
            imagepng($novaImagem, $destino);
            break;
    }

    imagedestroy($imagem);
    imagedestroy($novaImagem);

    return true;
}

function normalizarCaminhoImagemProduto($caminho)
{
    $caminho = trim((string)$caminho);

    if ($caminho === "") {
        return "";
    }

    $caminho = str_replace("\\", "/", $caminho);

    if (preg_match('#^https?://#i', $caminho)) {
        return $caminho;
    }

    if (strpos($caminho, "/") === false) {
        $caminho = "IMG/produtos/" . $caminho;
    }

    return ltrim($caminho, "/");
}

function urlImagemProduto($caminho)
{
    global $baseUrl;

    $caminho = normalizarCaminhoImagemProduto($caminho);

    if ($caminho === "") {
        return "";
    }

    if (preg_match('#^https?://#i', $caminho)) {
        return $caminho;
    }

    return rtrim($baseUrl, "/") . "/" . $caminho;
}

function caminhoFisicoImagemProduto($caminho)
{
    global $baseUrl;

    $caminho = trim((string)$caminho);

    if ($caminho === "" || preg_match('#^https?://#i', $caminho)) {
        return null;
    }

    $caminho = str_replace("\\", "/", $caminho);
    $base = trim((string)$baseUrl, "/");

    if ($base !== "" && str_starts_with(ltrim($caminho, "/"), $base . "/")) {
        $caminho = substr(ltrim($caminho, "/"), strlen($base) + 1);
    }

    $caminho = normalizarCaminhoImagemProduto($caminho);

    if ($caminho === "") {
        return null;
    }

    return dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $caminho);
}

function imagemProdutoExiste($caminho)
{
    $arquivo = caminhoFisicoImagemProduto($caminho);
    return $arquivo !== null && is_file($arquivo);
}

function versaoImagemProduto($caminho)
{
    $arquivo = caminhoFisicoImagemProduto($caminho);

    if ($arquivo !== null && is_file($arquivo)) {
        return (string)filemtime($arquivo);
    }

    return "";
}

function validarCPF($cpf)
{
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11) {
        return false;
    }

    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    $soma = 0;

    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }

    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    if ((int)$cpf[9] !== $digito1) {
        return false;
    }

    $soma = 0;

    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }

    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    return (int)$cpf[10] === $digito2;
}

function mensagem($titulo, $texto, $icone, $destino = null)
{
    global $baseUrl;

    $_SESSION["mensagemFlash"] = [
        "titulo" => $titulo,
        "texto" => $texto,
        "icone" => $icone
    ];

    if ($destino === null || trim($destino) === "") {
        $destino = "cadastro";
    }

    $url = rtrim($baseUrl, "/") . "/" . ltrim($destino, "/");
    header("Location: " . $url);
    exit;
}
?>