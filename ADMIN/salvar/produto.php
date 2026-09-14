<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastrarProduto");
    exit;
}

/* Pega os dados enviados pelo formulário. */
$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");
$preco = $_POST["preco"] ?? "";
$estoque = $_POST["estoque"] ?? "";
$marca = filter_input(INPUT_POST, "marca", FILTER_VALIDATE_INT);
$categoria = filter_input(INPUT_POST, "categoria", FILTER_VALIDATE_INT);

/* Valida o nome do produto. */
if ($nome === "" || mb_strlen($nome) > 150) {
    header("Location: cadastrarProduto");
    exit;
}

/* Valida o tamanho da descrição. */
if (mb_strlen($descricao) > 500) {
    header("Location: cadastrarProduto");
    exit;
}

/* Valida o preço informado. */
if ($preco === "" || !is_numeric($preco) || (float)$preco < 0) {
    header("Location: cadastrarProduto");
    exit;
}

/* Valida a quantidade em estoque. */
if ($estoque === "" || filter_var($estoque, FILTER_VALIDATE_INT) === false || (int)$estoque < 0) {
    header("Location: cadastrarProduto");
    exit;
}

$preco = (float)$preco;
$estoque = (int)$estoque;

/* Verifica se a marca e a categoria foram selecionadas. */
if (!$marca || !$categoria) {
    header("Location: cadastrarProduto");
    exit;
}

/* Verifica se a marca está ativa. */
$sql = "SELECT ID_MARCA
        FROM marca
        WHERE ID_MARCA = :id
        AND FL_ATIVO = 1
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $marca, PDO::PARAM_INT);
$consulta->execute();

if (!$consulta->fetch()) {
    header("Location: cadastrarProduto");
    exit;
}

/* Verifica se a categoria está ativa. */
$sql = "SELECT ID_CATEGORIA
        FROM categoria
        WHERE ID_CATEGORIA = :id
        AND FL_ATIVO = 1
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $categoria, PDO::PARAM_INT);
$consulta->execute();

if (!$consulta->fetch()) {
    header("Location: cadastrarProduto");
    exit;
}

/* Verifica se as imagens foram enviadas. */
if (!isset($_FILES["imagens"]) || !is_array($_FILES["imagens"]["name"])) {
    header("Location: cadastrarProduto");
    exit;
}

/* Verifica a quantidade de imagens. */
$quantidadeImagens = count($_FILES["imagens"]["name"]);

if ($quantidadeImagens < 1 || $quantidadeImagens > 3) {
    header("Location: cadastrarProduto");
    exit;
}

/* Define os formatos e o tamanho máximo das imagens. */
$extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];
$tamanhoMaximo = 5 * 1024 * 1024;
$arquivosSalvos = [];

/* Valida todas as imagens antes de iniciar o cadastro. */
foreach ($_FILES["imagens"]["tmp_name"] as $indice => $arquivoTemporario) {
    $erro = $_FILES["imagens"]["error"][$indice];
    $tamanho = $_FILES["imagens"]["size"][$indice];
    $nomeOriginal = $_FILES["imagens"]["name"][$indice];

    /* Verifica se a imagem foi recebida corretamente. */
    if ($erro !== UPLOAD_ERR_OK) {
        header("Location: cadastrarProduto");
        exit;
    }

    /* Verifica o tamanho da imagem. */
    if ($tamanho <= 0 || $tamanho > $tamanhoMaximo) {
        header("Location: cadastrarProduto");
        exit;
    }

    /* Verifica a extensão da imagem. */
    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas, true)) {
        header("Location: cadastrarProduto");
        exit;
    }

    /* Verifica o tipo real da imagem. */
    $tipoImagem = mime_content_type($arquivoTemporario);
    $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

    if (!in_array($tipoImagem, $tiposPermitidos, true)) {
        header("Location: cadastrarProduto");
        exit;
    }

    /* Verifica se o arquivo realmente é uma imagem. */
    $arquivoImagem = @getimagesize($arquivoTemporario);

    if ($arquivoImagem === false) {
        header("Location: cadastrarProduto");
        exit;
    }
}

/* Define a pasta onde as imagens serão salvas. */
$pastaImagens = __DIR__ . "/../../IMG/produtos/";

if (!is_dir($pastaImagens)) {
    mkdir($pastaImagens, 0777, true);
}

try {
    /* Inicia a transação do cadastro. */
    $pdo->beginTransaction();

    /* Cadastra o produto no banco de dados. */
    $sql = "INSERT INTO produto (
                NM_PRODUTO,
                DS_PRODUTO,
                VL_PRODUTO,
                QT_ESTOQUE,
                FL_ATIVO,
                ID_MARCA,
                ID_CATEGORIA
            ) VALUES (
                :nome,
                :descricao,
                :preco,
                :estoque,
                1,
                :marca,
                :categoria
            )";

    $consulta = $pdo->prepare($sql);
    $consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
    $consulta->bindValue(":descricao", $descricao, PDO::PARAM_STR);
    $consulta->bindValue(":preco", $preco);
    $consulta->bindValue(":estoque", $estoque, PDO::PARAM_INT);
    $consulta->bindValue(":marca", $marca, PDO::PARAM_INT);
    $consulta->bindValue(":categoria", $categoria, PDO::PARAM_INT);
    $consulta->execute();

    /* Pega o ID do produto cadastrado. */
    $idProduto = (int)$pdo->lastInsertId();

    /* Salva as imagens e relaciona cada uma ao produto. */
    foreach ($_FILES["imagens"]["tmp_name"] as $indice => $arquivoTemporario) {
        $nomeOriginal = $_FILES["imagens"]["name"][$indice];
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $nomeArquivo = "produto_" . $idProduto . "_" . uniqid("", true) . "." . $extensao;
        $caminhoCompleto = $pastaImagens . $nomeArquivo;

        /* Salva a imagem na pasta de produtos. */
        if (!move_uploaded_file($arquivoTemporario, $caminhoCompleto)) {
            throw new Exception("Não foi possível salvar uma das imagens.");
        }

        $caminhoBanco = "IMG/produtos/" . $nomeArquivo;

        /* Cadastra a imagem no banco de dados. */
        $sql = "INSERT INTO produto_imagem (
                    ID_PRODUTO,
                    DS_IMAGEM,
                    FL_PRINCIPAL,
                    NR_ORDEM
                ) VALUES (
                    :produto,
                    :imagem,
                    :principal,
                    :ordem
                )";

        $consulta = $pdo->prepare($sql);
        $consulta->bindValue(":produto", $idProduto, PDO::PARAM_INT);
        $consulta->bindValue(":imagem", $caminhoBanco, PDO::PARAM_STR);
        $consulta->bindValue(":principal", $indice === 0 ? 1 : 0, PDO::PARAM_INT);
        $consulta->bindValue(":ordem", $indice + 1, PDO::PARAM_INT);
        $consulta->execute();

        /* Guarda o caminho dos arquivos criados. */
        $arquivosSalvos[] = $caminhoCompleto;
    }

    /* Confirma o cadastro do produto e das imagens. */
    $pdo->commit();

    /* Volta para a lista de produtos. */
    header("Location: produtos");
    exit;

} catch (Throwable $e) {
    /* Desfaz o cadastro caso aconteça algum erro. */
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    /* Remove as imagens criadas caso o cadastro falhe. */
    foreach ($arquivosSalvos as $arquivo) {
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }
    }

    header("Location: cadastrarProduto");
    exit;
}
?>