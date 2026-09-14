<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Busca as marcas ativas. */
$sqlMarcas = "SELECT ID_MARCA, NM_MARCA
              FROM marca
              WHERE FL_ATIVO = 1
              ORDER BY NM_MARCA";

/* Executa a consulta das marcas. */
$consultaMarcas = $pdo->query($sqlMarcas);

/* Guarda as marcas encontradas. */
$marcas = $consultaMarcas->fetchAll(PDO::FETCH_ASSOC);

/* Busca as categorias ativas. */
$sqlCategorias = "SELECT ID_CATEGORIA, NM_CATEGORIA
                  FROM categoria
                  WHERE FL_ATIVO = 1
                  ORDER BY NM_CATEGORIA";

/* Executa a consulta das categorias. */
$consultaCategorias = $pdo->query($sqlCategorias);

/* Guarda as categorias encontradas. */
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Cadastrar produto</h1>
                <p>Cadastre um novo produto no Covil do Dragão</p>
            </div>

            <div class="card-body p-4">
                <!-- Formulário para cadastrar o produto. -->
                <form method="post" action="<?= $baseUrl ?>/index.php?param=salvar/produto" enctype="multipart/form-data" id="formularioProduto">
                    <div class="mb-4">
                        <div class="tituloCampoMarca">Nome do produto:</div>
                        <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome do produto" maxlength="150" required>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Descrição do produto:</div>
                        <textarea name="descricao" id="descricao" class="form-control campoDescricaoCategoria" placeholder="Digite uma descrição para o produto" maxlength="500" rows="5"></textarea>
                        <div class="form-text">A descrição é opcional e pode ter até 500 caracteres.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="tituloCampoMarca">Preço:</div>
                            <input type="number" name="preco" id="preco" class="form-control" placeholder="0,00" min="0" step="0.01" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="tituloCampoMarca">Estoque:</div>
                            <input type="number" name="estoque" id="estoque" class="form-control" placeholder="Digite a quantidade em estoque" min="0" step="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="tituloCampoMarca">Marca:</div>
                            <select name="marca" id="marca" class="form-select" required>
                                <option value="">Selecione uma marca</option>

                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?= (int)$marca["ID_MARCA"] ?>">
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
                                    <option value="<?= (int)$categoria["ID_CATEGORIA"] ?>">
                                        <?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Imagens do produto:</div>

                        <div class="campoArquivo">
                            <!-- Abre a seleção de imagens. -->
                            <label for="imagens" class="botaoArquivo">
                                Escolher imagens
                            </label>

                            <span class="nomeArquivo" id="nomeArquivo">
                                Nenhuma imagem escolhida
                            </span>

                            <input type="file" name="imagens[]" id="imagens" accept=".jpg,.jpeg,.png,.webp" multiple required>
                        </div>

                        <div class="form-text">
                            Escolha de 1 a 3 imagens. Formatos permitidos: JPG, JPEG, PNG e WEBP. Tamanho máximo de 5 MB por imagem.
                        </div>

                        <div id="avisoImagens" class="form-text"></div>
                    </div>

                    <div class="cadastroMarcaBotoes">
                        <!-- Volta para a lista de produtos. -->
                        <a href="produtos" class="btn botaoCovil">Cancelar</a>

                        <!-- Envia os dados para cadastro. -->
                        <button type="submit" class="btn botaoCovil">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
/* Pega os elementos usados na seleção das imagens. */
const campoImagens = document.getElementById("imagens");
const nomeArquivo = document.getElementById("nomeArquivo");
const avisoImagens = document.getElementById("avisoImagens");
const formularioProduto = document.getElementById("formularioProduto");

/* Verifica quando as imagens são selecionadas. */
campoImagens.addEventListener("change", function () {
    const quantidade = this.files.length;

    /* Limpa as mensagens quando nenhuma imagem foi escolhida. */
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

    /* Pega o nome das imagens selecionadas. */
    const nomes = Array.from(this.files).map(arquivo => arquivo.name);

    /* Mostra os nomes das imagens. */
    nomeArquivo.textContent = nomes.join(", ");

    /* Mostra a quantidade de imagens selecionadas. */
    avisoImagens.textContent = `${quantidade} imagem(ns) selecionada(s).`;
});

/* Verifica a quantidade de imagens antes de enviar. */
formularioProduto.addEventListener("submit", function (evento) {
    const quantidade = campoImagens.files.length;

    /* Impede o envio sem nenhuma imagem. */
    if (quantidade < 1) {
        evento.preventDefault();
        avisoImagens.textContent = "É obrigatório selecionar pelo menos 1 imagem.";
        return;
    }

    /* Impede o envio com mais de 3 imagens. */
    if (quantidade > 3) {
        evento.preventDefault();
        avisoImagens.textContent = "Você pode selecionar no máximo 3 imagens.";
    }
});
</script>