<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Pega o ID do produto pela URL. */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: produtos");
    exit;
}

/* Busca os dados do produto. */
$sql = "SELECT
            ID_PRODUTO,
            NM_PRODUTO,
            DS_PRODUTO,
            VL_PRODUTO,
            QT_ESTOQUE,
            ID_MARCA,
            ID_CATEGORIA
        FROM produto
        WHERE ID_PRODUTO = :id
        LIMIT 1";

/* Executa a consulta do produto. */
$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

/* Guarda os dados encontrados. */
$produto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: produtos");
    exit;
}

/* Busca as marcas ativas. */
$sqlMarcas = "SELECT
                  ID_MARCA,
                  NM_MARCA
              FROM marca
              WHERE FL_ATIVO = 1
              ORDER BY NM_MARCA";

/* Executa a consulta das marcas. */
$consultaMarcas = $pdo->query($sqlMarcas);

/* Guarda as marcas encontradas. */
$marcas = $consultaMarcas->fetchAll(PDO::FETCH_ASSOC);

/* Busca as categorias ativas. */
$sqlCategorias = "SELECT
                      ID_CATEGORIA,
                      NM_CATEGORIA
                  FROM categoria
                  WHERE FL_ATIVO = 1
                  ORDER BY NM_CATEGORIA";

/* Executa a consulta das categorias. */
$consultaCategorias = $pdo->query($sqlCategorias);

/* Guarda as categorias encontradas. */
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);

/* Busca as imagens atuais do produto. */
$sqlImagens = "SELECT
                   ID_PRODUTO_IMAGEM,
                   DS_IMAGEM,
                   FL_PRINCIPAL,
                   NR_ORDEM
               FROM produto_imagem
               WHERE ID_PRODUTO = :id
               ORDER BY FL_PRINCIPAL DESC, NR_ORDEM ASC";

/* Executa a consulta das imagens. */
$consultaImagens = $pdo->prepare($sqlImagens);
$consultaImagens->bindValue(":id", $id, PDO::PARAM_INT);
$consultaImagens->execute();

/* Guarda as imagens encontradas. */
$imagens = $consultaImagens->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Editar produto</h1>
                <p>Altere as informações do produto cadastrado</p>
            </div>

            <div class="card-body p-4">
                <!-- Formulário para editar o produto. -->
                <form method="post" action="index.php?param=salvar/editarProduto" enctype="multipart/form-data" id="formularioProduto">
                    <input type="hidden" name="id" value="<?= (int)$produto["ID_PRODUTO"] ?>">

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Nome do produto:</div>
                        <input
                            type="text"
                            name="nome"
                            id="nome"
                            class="form-control"
                            value="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>"
                            placeholder="Digite o nome do produto"
                            maxlength="150"
                            required>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Descrição do produto:</div>
                        <textarea
                            name="descricao"
                            id="descricao"
                            class="form-control campoDescricaoCategoria"
                            placeholder="Digite uma descrição para o produto"
                            maxlength="500"
                            rows="5"><?= htmlspecialchars($produto["DS_PRODUTO"] ?? "") ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="tituloCampoMarca">Preço:</div>
                            <input
                                type="number"
                                name="preco"
                                id="preco"
                                class="form-control"
                                value="<?= htmlspecialchars($produto["VL_PRODUTO"]) ?>"
                                min="0"
                                step="0.01"
                                required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="tituloCampoMarca">Estoque:</div>
                            <input
                                type="number"
                                name="estoque"
                                id="estoque"
                                class="form-control"
                                value="<?= (int)$produto["QT_ESTOQUE"] ?>"
                                min="0"
                                step="1"
                                required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="tituloCampoMarca">Marca:</div>

                            <select name="marca" id="marca" class="form-select" required>
                                <option value="">Selecione uma marca</option>

                                <?php foreach ($marcas as $marca): ?>
                                    <option
                                        value="<?= (int)$marca["ID_MARCA"] ?>"
                                        <?= (int)$marca["ID_MARCA"] === (int)$produto["ID_MARCA"] ? "selected" : "" ?>>
                                        <?= htmlspecialchars($marca["NM_MARCA"]) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="tituloCampoMarca">Categoria:</div>

                            <select name="categoria" id="categoria" class="form-select" required>
                                <option value="">Selecione uma categoria</option>

                                <?php foreach ($categorias as $categoria): ?>
                                    <option
                                        value="<?= (int)$categoria["ID_CATEGORIA"] ?>"
                                        <?= (int)$categoria["ID_CATEGORIA"] === (int)$produto["ID_CATEGORIA"] ? "selected" : "" ?>>
                                        <?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Imagens atuais:</div>

                        <div class="row g-3">
                            <?php foreach ($imagens as $imagem): ?>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded p-2 text-center">
                                        <img
                                            src="<?= htmlspecialchars($imagem["DS_IMAGEM"]) ?>"
                                            alt="Imagem do produto"
                                            class="img-fluid">

                                        <?php if ((int)$imagem["FL_PRINCIPAL"] === 1): ?>
                                            <div class="mt-2">Imagem principal</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Novas imagens:</div>

                        <div class="campoArquivo">
                            <label for="imagens" class="botaoArquivo">
                                Escolher imagens
                            </label>

                            <span class="nomeArquivo" id="nomeArquivo">
                                Nenhuma imagem escolhida
                            </span>

                            <input
                                type="file"
                                name="imagens[]"
                                id="imagens"
                                accept=".jpg,.jpeg,.png,.webp"
                                multiple>
                        </div>

                        <div class="form-text">
                            Escolha até 3 novas imagens. Caso envie novas imagens, elas substituirão as imagens atuais.
                        </div>

                        <div id="avisoImagens" class="form-text"></div>
                    </div>

                    <div class="cadastroMarcaBotoes">
                        <!-- Volta para a lista de produtos. -->
                        <a href="produtos" class="btn botaoCovil">Cancelar</a>

                        <!-- Salva as alterações do produto. -->
                        <button type="submit" class="btn botaoCovil">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    /* Pega os elementos do formulário. */
    const campoImagens = document.getElementById("imagens");
    const nomeArquivo = document.getElementById("nomeArquivo");
    const avisoImagens = document.getElementById("avisoImagens");
    const formularioProduto = document.getElementById("formularioProduto");

    /* Verifica quando as imagens são selecionadas. */
    campoImagens.addEventListener("change", function() {
        const quantidade = this.files.length;

        /* Mostra mensagem quando nenhuma imagem foi selecionada. */
        if (quantidade === 0) {
            nomeArquivo.textContent = "Nenhuma imagem escolhida";
            avisoImagens.textContent = "";
            return;
        }

        /* Impede a seleção de mais de 3 imagens. */
        if (quantidade > 3) {
            nomeArquivo.textContent = "Quantidade de imagens inválida";
            avisoImagens.textContent = "Você pode selecionar no máximo 3 imagens.";
            return;
        }

        /* Pega o nome de cada imagem. */
        const nomes = Array.from(this.files).map(arquivo => arquivo.name);

        /* Mostra os nomes das imagens selecionadas. */
        nomeArquivo.textContent = nomes.join(", ");

        /* Mostra a quantidade de imagens selecionadas. */
        avisoImagens.textContent = `${quantidade} imagem(ns) selecionada(s).`;
    });

    /* Verifica a quantidade de imagens antes do envio. */
    formularioProduto.addEventListener("submit", function(evento) {
        const quantidade = campoImagens.files.length;

        /* Impede o envio com mais de 3 imagens. */
        if (quantidade > 3) {
            evento.preventDefault();
            avisoImagens.textContent = "Você pode selecionar no máximo 3 imagens.";
        }
    });
</script>