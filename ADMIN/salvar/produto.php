<?php
/*
    Processa o cadastro de produtos do Covil do Dragão.

    O fluxo valida todos os campos, confirma se marca e categoria estão ativas,
    verifica de 1 a 3 imagens reais nos formatos JPG, PNG ou WEBP e usa uma
    transação para cadastrar produto e imagens juntos. Se qualquer etapa falhar,
    o banco é revertido e os arquivos já criados são removidos.

    Diferente da versão anterior, nenhuma validação falha em silêncio: o usuário
    recebe uma mensagem dizendo qual dado ou upload precisa ser corrigido.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: " . $baseUrl . "/home");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . $baseUrl . "/cadastrarProduto");
    exit;
}

$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");
$precoRecebido = trim((string)($_POST["preco"] ?? ""));
$estoqueRecebido = trim((string)($_POST["estoque"] ?? ""));
$marca = filter_input(INPUT_POST, "marca", FILTER_VALIDATE_INT);
$categoria = filter_input(INPUT_POST, "categoria", FILTER_VALIDATE_INT);

if ($nome === "" || mb_strlen($nome) > 150) {
    mensagem("Dados inválidos", "Informe um nome de produto com até 150 caracteres.", "error", "cadastrarProduto");
}

if (mb_strlen($descricao) > 500) {
    mensagem("Dados inválidos", "A descrição pode ter no máximo 500 caracteres.", "error", "cadastrarProduto");
}

$precoNormalizado = str_replace(",", ".", $precoRecebido);

if ($precoNormalizado === "" || !is_numeric($precoNormalizado) || (float)$precoNormalizado < 0) {
    mensagem("Dados inválidos", "Informe um preço válido e maior ou igual a zero.", "error", "cadastrarProduto");
}

if ($estoqueRecebido === "" || filter_var($estoqueRecebido, FILTER_VALIDATE_INT) === false || (int)$estoqueRecebido < 0) {
    mensagem("Dados inválidos", "Informe uma quantidade de estoque inteira e maior ou igual a zero.", "error", "cadastrarProduto");
}

if (!$marca || !$categoria) {
    mensagem("Dados inválidos", "Selecione uma marca e uma categoria.", "error", "cadastrarProduto");
}

$preco = number_format((float)$precoNormalizado, 2, ".", "");
$estoque = (int)$estoqueRecebido;

try {
    $sql = "SELECT ID_MARCA FROM marca WHERE ID_MARCA = :id AND FL_ATIVO = 1 LIMIT 1";
    $consulta = $pdo->prepare($sql);
    $consulta->bindValue(":id", $marca, PDO::PARAM_INT);
    $consulta->execute();

    if (!$consulta->fetch()) {
        mensagem("Marca inválida", "A marca selecionada não existe ou está desativada.", "error", "cadastrarProduto");
    }

    $sql = "SELECT ID_CATEGORIA FROM categoria WHERE ID_CATEGORIA = :id AND FL_ATIVO = 1 LIMIT 1";
    $consulta = $pdo->prepare($sql);
    $consulta->bindValue(":id", $categoria, PDO::PARAM_INT);
    $consulta->execute();

    if (!$consulta->fetch()) {
        mensagem("Categoria inválida", "A categoria selecionada não existe ou está desativada.", "error", "cadastrarProduto");
    }
} catch (Throwable $e) {
    error_log("[Cadastro de produto - validação] " . $e->getMessage());
    mensagem("Erro", "Não foi possível validar marca e categoria no banco de dados.", "error", "cadastrarProduto");
}

if (!isset($_FILES["imagens"]) || !is_array($_FILES["imagens"]["name"] ?? null)) {
    mensagem("Imagens obrigatórias", "Selecione de 1 a 3 imagens para o produto.", "error", "cadastrarProduto");
}

$quantidadeImagens = count(array_filter($_FILES["imagens"]["name"], fn($nomeArquivo) => $nomeArquivo !== ""));

if ($quantidadeImagens < 1 || $quantidadeImagens > 3) {
    mensagem("Quantidade de imagens inválida", "Selecione de 1 a 3 imagens.", "error", "cadastrarProduto");
}

$extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];
$tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];
$tamanhoMaximo = 5 * 1024 * 1024;
$arquivosSalvos = [];
$tiposDetectados = [];

$mensagensUpload = [
    UPLOAD_ERR_INI_SIZE => "Uma das imagens ultrapassa o limite de upload configurado no PHP. No XAMPP, confira upload_max_filesize no php.ini.",
    UPLOAD_ERR_FORM_SIZE => "Uma das imagens ultrapassa o tamanho permitido pelo formulário.",
    UPLOAD_ERR_PARTIAL => "Uma das imagens foi enviada apenas parcialmente. Tente novamente.",
    UPLOAD_ERR_NO_FILE => "Selecione pelo menos uma imagem.",
    UPLOAD_ERR_NO_TMP_DIR => "A pasta temporária de upload do PHP não está disponível.",
    UPLOAD_ERR_CANT_WRITE => "O servidor não conseguiu gravar uma das imagens.",
    UPLOAD_ERR_EXTENSION => "Uma extensão do PHP interrompeu o upload de uma das imagens."
];

foreach ($_FILES["imagens"]["tmp_name"] as $indice => $arquivoTemporario) {
    if (($_FILES["imagens"]["name"][$indice] ?? "") === "") {
        continue;
    }

    $erro = (int)$_FILES["imagens"]["error"][$indice];
    $tamanho = (int)$_FILES["imagens"]["size"][$indice];
    $nomeOriginal = $_FILES["imagens"]["name"][$indice];

    if ($erro !== UPLOAD_ERR_OK) {
        $texto = $mensagensUpload[$erro] ?? "Não foi possível receber uma das imagens.";
        mensagem("Erro no upload", $texto, "error", "cadastrarProduto");
    }

    if ($tamanho <= 0 || $tamanho > $tamanhoMaximo) {
        mensagem("Imagem inválida", "Cada imagem deve ter no máximo 5 MB.", "error", "cadastrarProduto");
    }

    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas, true)) {
        mensagem("Formato inválido", "Use somente imagens JPG, JPEG, PNG ou WEBP.", "error", "cadastrarProduto");
    }

    $dadosImagem = @getimagesize($arquivoTemporario);

    if ($dadosImagem === false || !in_array($dadosImagem["mime"] ?? "", $tiposPermitidos, true)) {
        mensagem("Imagem inválida", "Um dos arquivos selecionados não é uma imagem válida.", "error", "cadastrarProduto");
    }

    $tiposDetectados[$indice] = $dadosImagem["mime"];
}

$pastaImagens = __DIR__ . "/../../IMG/produtos/";

if (!is_dir($pastaImagens) && !mkdir($pastaImagens, 0777, true)) {
    mensagem("Erro", "Não foi possível criar a pasta IMG/produtos.", "error", "cadastrarProduto");
}

if (!is_writable($pastaImagens)) {
    mensagem("Erro", "A pasta IMG/produtos não possui permissão de escrita.", "error", "cadastrarProduto");
}

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO produto (
                NM_PRODUTO, DS_PRODUTO, VL_PRODUTO, QT_ESTOQUE,
                FL_ATIVO, ID_MARCA, ID_CATEGORIA
            ) VALUES (
                :nome, :descricao, :preco, :estoque,
                1, :marca, :categoria
            )";

    $consulta = $pdo->prepare($sql);
    $consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
    $consulta->bindValue(":descricao", $descricao !== "" ? $descricao : null, $descricao !== "" ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $consulta->bindValue(":preco", $preco, PDO::PARAM_STR);
    $consulta->bindValue(":estoque", $estoque, PDO::PARAM_INT);
    $consulta->bindValue(":marca", $marca, PDO::PARAM_INT);
    $consulta->bindValue(":categoria", $categoria, PDO::PARAM_INT);
    $consulta->execute();

    $idProduto = (int)$pdo->lastInsertId();

    if ($idProduto <= 0) {
        throw new RuntimeException("O banco não retornou o ID do produto cadastrado.");
    }

    $ordem = 1;

    foreach ($_FILES["imagens"]["tmp_name"] as $indice => $arquivoTemporario) {
        $nomeOriginal = $_FILES["imagens"]["name"][$indice] ?? "";

        if ($nomeOriginal === "") {
            continue;
        }

        $mimeDetectado = $tiposDetectados[$indice] ?? "";
        $extensaoFinal = match ($mimeDetectado) {
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp",
            default => throw new RuntimeException("Formato de imagem não reconhecido no momento de salvar.")
        };

        $nomeArquivo = "produto_" . $idProduto . "_" . bin2hex(random_bytes(8)) . "." . $extensaoFinal;
        $caminhoCompleto = $pastaImagens . $nomeArquivo;

        if (!is_uploaded_file($arquivoTemporario)) {
            throw new RuntimeException("O arquivo temporário da imagem não é um upload válido do PHP.");
        }

        if (!move_uploaded_file($arquivoTemporario, $caminhoCompleto)) {
            throw new RuntimeException("Não foi possível mover uma das imagens para IMG/produtos.");
        }

        clearstatcache(true, $caminhoCompleto);

        if (!is_file($caminhoCompleto) || filesize($caminhoCompleto) <= 0 || @getimagesize($caminhoCompleto) === false) {
            throw new RuntimeException("A imagem foi recebida, mas não pôde ser confirmada após a gravação.");
        }

        @chmod($caminhoCompleto, 0644);
        $arquivosSalvos[] = $caminhoCompleto;
        $caminhoBanco = "IMG/produtos/" . $nomeArquivo;

        $sqlImagem = "INSERT INTO produto_imagem (
                          ID_PRODUTO, DS_IMAGEM, FL_PRINCIPAL, NR_ORDEM
                      ) VALUES (
                          :produto, :imagem, :principal, :ordem
                      )";

        $consultaImagem = $pdo->prepare($sqlImagem);
        $consultaImagem->bindValue(":produto", $idProduto, PDO::PARAM_INT);
        $consultaImagem->bindValue(":imagem", $caminhoBanco, PDO::PARAM_STR);
        $consultaImagem->bindValue(":principal", $ordem === 1 ? 1 : 0, PDO::PARAM_INT);
        $consultaImagem->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $consultaImagem->execute();
        $ordem++;
    }

    $pdo->commit();
    mensagem("Produto cadastrado!", "O produto e suas imagens foram cadastrados com sucesso.", "success", "produtos");
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    foreach ($arquivosSalvos as $arquivo) {
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }
    }

    error_log("[Cadastro de produto] " . $e->getMessage());
    mensagem("Erro ao cadastrar", "Não foi possível cadastrar o produto. Verifique os dados, as imagens e a conexão com o banco.", "error", "cadastrarProduto");
}
?>