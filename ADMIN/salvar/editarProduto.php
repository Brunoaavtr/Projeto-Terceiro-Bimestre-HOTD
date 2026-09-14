<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: produtos");
    exit;
}

/* Pega os dados enviados pelo formulário. */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");
$preco = $_POST["preco"] ?? "";
$estoque = $_POST["estoque"] ?? "";
$marca = filter_input(INPUT_POST, "marca", FILTER_VALIDATE_INT);
$categoria = filter_input(INPUT_POST, "categoria", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: produtos");
    exit;
}

/* Valida o nome do produto. */
if ($nome === "" || mb_strlen($nome) > 150) {
    header("Location: editarProduto?id=" . $id);
    exit;
}

/* Valida o tamanho da descrição. */
if (mb_strlen($descricao) > 500) {
    header("Location: editarProduto?id=" . $id);
    exit;
}

/* Valida o preço informado. */
if ($preco === "" || !is_numeric($preco) || (float)$preco < 0) {
    header("Location: editarProduto?id=" . $id);
    exit;
}

/* Valida a quantidade em estoque. */
if ($estoque === "" || filter_var($estoque, FILTER_VALIDATE_INT) === false || (int)$estoque < 0) {
    header("Location: editarProduto?id=" . $id);
    exit;
}

/* Verifica a marca e a categoria selecionadas. */
if (!$marca || !$categoria) {
    header("Location: editarProduto?id=" . $id);
    exit;
}

$preco = (float)$preco;
$estoque = (int)$estoque;

/* Verifica se o produto existe. */
$sql = "SELECT ID_PRODUTO
        FROM produto
        WHERE ID_PRODUTO = :id
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

if (!$consulta->fetch()) {
    header("Location: produtos");
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
    header("Location: editarProduto?id=" . $id);
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
    header("Location: editarProduto?id=" . $id);
    exit;
}

/* Verifica se novas imagens foram enviadas. */
$temNovasImagens = isset($_FILES["imagens"]) && is_array($_FILES["imagens"]["name"]);
$novasImagens = [];

if ($temNovasImagens) {
    foreach ($_FILES["imagens"]["name"] as $indice => $nomeOriginal) {
        if ($_FILES["imagens"]["error"][$indice] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $novasImagens[] = $indice;
    }

    /* Limita o cadastro a 3 imagens. */
    if (count($novasImagens) > 3) {
        header("Location: editarProduto?id=" . $id);
        exit;
    }
}

/* Define as extensões e o tamanho máximo das imagens. */
$extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];
$tamanhoMaximo = 5 * 1024 * 1024;
$pastaImagens = __DIR__ . "/../../IMG/produtos/";

if (!is_dir($pastaImagens)) {
    mkdir($pastaImagens, 0777, true);
}

$arquivosNovosSalvos = [];

/* Valida as novas imagens antes de alterar o banco. */
if (count($novasImagens) > 0) {
    foreach ($novasImagens as $indice) {
        $arquivoTemporario = $_FILES["imagens"]["tmp_name"][$indice];
        $nomeOriginal = $_FILES["imagens"]["name"][$indice];
        $erro = $_FILES["imagens"]["error"][$indice];
        $tamanho = $_FILES["imagens"]["size"][$indice];

        /* Verifica se a imagem foi recebida corretamente. */
        if ($erro !== UPLOAD_ERR_OK) {
            header("Location: editarProduto?id=" . $id);
            exit;
        }

        /* Verifica o tamanho da imagem. */
        if ($tamanho <= 0 || $tamanho > $tamanhoMaximo) {
            header("Location: editarProduto?id=" . $id);
            exit;
        }

        /* Verifica a extensão da imagem. */
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        if (!in_array($extensao, $extensoesPermitidas, true)) {
            header("Location: editarProduto?id=" . $id);
            exit;
        }

        /* Verifica o tipo real da imagem. */
        $tipoImagem = mime_content_type($arquivoTemporario);
        $tiposPermitidos = ["image/jpeg", "image/png", "image/webp"];

        if (!in_array($tipoImagem, $tiposPermitidos, true)) {
            header("Location: editarProduto?id=" . $id);
            exit;
        }

        /* Verifica se o arquivo realmente é uma imagem. */
        if (@getimagesize($arquivoTemporario) === false) {
            header("Location: editarProduto?id=" . $id);
            exit;
        }
    }
}

try {
    /* Inicia a transação para atualizar o produto. */
    $pdo->beginTransaction();

    /* Atualiza os dados do produto. */
    $sql = "UPDATE produto
            SET NM_PRODUTO = :nome,
                DS_PRODUTO = :descricao,
                VL_PRODUTO = :preco,
                QT_ESTOQUE = :estoque,
                ID_MARCA = :marca,
                ID_CATEGORIA = :categoria
            WHERE ID_PRODUTO = :id";

    $consulta = $pdo->prepare($sql);
    $consulta->bindValue(":nome", $nome, PDO::PARAM_STR);
    $consulta->bindValue(":descricao", $descricao, PDO::PARAM_STR);
    $consulta->bindValue(":preco", $preco);
    $consulta->bindValue(":estoque", $estoque, PDO::PARAM_INT);
    $consulta->bindValue(":marca", $marca, PDO::PARAM_INT);
    $consulta->bindValue(":categoria", $categoria, PDO::PARAM_INT);
    $consulta->bindValue(":id", $id, PDO::PARAM_INT);
    $consulta->execute();

    /* Substitui as imagens atuais caso novas imagens tenham sido enviadas. */
    if (count($novasImagens) > 0) {
        /* Busca os caminhos das imagens antigas. */
        $sql = "SELECT DS_IMAGEM
                FROM produto_imagem
                WHERE ID_PRODUTO = :id";

        $consulta = $pdo->prepare($sql);
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        $imagensAntigas = $consulta->fetchAll(PDO::FETCH_COLUMN);

        /* Remove as imagens antigas do banco. */
        $sql = "DELETE FROM produto_imagem
                WHERE ID_PRODUTO = :id";

        $consulta = $pdo->prepare($sql);
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        /* Salva as novas imagens e cadastra seus dados no banco. */
        foreach ($novasImagens as $ordem => $indice) {
            $nomeOriginal = $_FILES["imagens"]["name"][$indice];
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            $nomeArquivo = "produto_" . $id . "_" . uniqid("", true) . "." . $extensao;
            $caminhoCompleto = $pastaImagens . $nomeArquivo;

            /* Salva a nova imagem na pasta de produtos. */
            if (!move_uploaded_file($_FILES["imagens"]["tmp_name"][$indice], $caminhoCompleto)) {
                throw new Exception("Não foi possível salvar uma das novas imagens.");
            }

            $caminhoBanco = "IMG/produtos/" . $nomeArquivo;

            /* Cadastra a nova imagem no banco. */
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
            $consulta->bindValue(":produto", $id, PDO::PARAM_INT);
            $consulta->bindValue(":imagem", $caminhoBanco, PDO::PARAM_STR);
            $consulta->bindValue(":principal", $ordem === 0 ? 1 : 0, PDO::PARAM_INT);
            $consulta->bindValue(":ordem", $ordem + 1, PDO::PARAM_INT);
            $consulta->execute();

            $arquivosNovosSalvos[] = $caminhoCompleto;
        }

        /* Remove as imagens antigas da pasta de produtos. */
        foreach ($imagensAntigas as $imagemAntiga) {
            $caminhoAntigo = __DIR__ . "/../../" . $imagemAntiga;

            if (file_exists($caminhoAntigo)) {
                unlink($caminhoAntigo);
            }
        }
    }

    /* Confirma todas as alterações. */
    $pdo->commit();

    header("Location: produtos");
    exit;

} catch (Throwable $e) {
    /* Desfaz as alterações caso aconteça algum erro. */
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    /* Remove as novas imagens que já foram salvas caso o cadastro falhe. */
    foreach ($arquivosNovosSalvos as $arquivo) {
        if (file_exists($arquivo)) {
            unlink($arquivo);
        }
    }

    header("Location: editarProduto?id=" . $id);
    exit;
}
?>