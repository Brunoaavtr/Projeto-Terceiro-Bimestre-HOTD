<?php
/*
    Exibe os detalhes de um produto ativo da loja.

    A página recebe o ID pela URL, busca produto, marca, categoria e até três
    imagens cadastradas. Antes de montar o carrossel, o código confirma se cada
    imagem realmente existe dentro do projeto. A URL da imagem é montada com o
    caminho base do sistema para funcionar corretamente também com as URLs
    amigáveis, evitando o problema de procurar IMG/produtos dentro da rota atual.

    Se o produto não existir ou estiver desativado, o usuário volta para a loja.
*/

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: " . $baseUrl . "/loja");
    exit;
}

$sql = "SELECT
            p.ID_PRODUTO,
            p.NM_PRODUTO,
            p.DS_PRODUTO,
            p.VL_PRODUTO,
            p.QT_ESTOQUE,
            m.NM_MARCA,
            c.NM_CATEGORIA
        FROM produto p
        INNER JOIN marca m ON m.ID_MARCA = p.ID_MARCA
        INNER JOIN categoria c ON c.ID_CATEGORIA = p.ID_CATEGORIA
        WHERE p.ID_PRODUTO = :id
        AND p.FL_ATIVO = 1
        AND m.FL_ATIVO = 1
        AND c.FL_ATIVO = 1
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$produto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: " . $baseUrl . "/loja");
    exit;
}

$sqlImagens = "SELECT DS_IMAGEM, FL_PRINCIPAL, NR_ORDEM
               FROM produto_imagem
               WHERE ID_PRODUTO = :id
               ORDER BY FL_PRINCIPAL DESC, NR_ORDEM ASC
               LIMIT 3";

$consultaImagens = $pdo->prepare($sqlImagens);
$consultaImagens->bindValue(":id", $id, PDO::PARAM_INT);
$consultaImagens->execute();
$imagens = $consultaImagens->fetchAll(PDO::FETCH_ASSOC);
$imagens = array_values(array_filter($imagens, fn($imagem) => imagemProdutoExiste($imagem["DS_IMAGEM"] ?? "")));
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-body p-4">
                <div class="row g-5">
                    <div class="col-12 col-lg-7">
                        <?php if (count($imagens) > 0): ?>
                            <div id="carrosselProduto" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner produtoCarrossel">
                                    <?php foreach ($imagens as $indice => $imagem): ?>
                                        <?php
                                        $urlImagem = urlImagemProduto($imagem["DS_IMAGEM"]);
                                        $versaoImagem = versaoImagemProduto($imagem["DS_IMAGEM"]);
                                        ?>
                                        <div class="carousel-item <?= $indice === 0 ? "active" : "" ?>">
                                            <img
                                                src="<?= htmlspecialchars($urlImagem) ?><?= $versaoImagem !== "" ? "?v=" . urlencode($versaoImagem) : "" ?>"
                                                class="d-block w-100 produtoImagemGrande"
                                                alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (count($imagens) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carrosselProduto" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>

                                    <button class="carousel-control-next" type="button" data-bs-target="#carrosselProduto" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                        <span class="visually-hidden">Próximo</span>
                                    </button>

                                    <div class="carousel-indicators">
                                        <?php foreach ($imagens as $indice => $imagem): ?>
                                            <button
                                                type="button"
                                                data-bs-target="#carrosselProduto"
                                                data-bs-slide-to="<?= $indice ?>"
                                                class="<?= $indice === 0 ? "active" : "" ?>"
                                                <?= $indice === 0 ? 'aria-current="true"' : "" ?>
                                                aria-label="Imagem <?= $indice + 1 ?>">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="produtoSemImagem">
                                <p>Nenhuma imagem disponível para este produto.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="produtoDetalhes">
                            <h1><?= htmlspecialchars($produto["NM_PRODUTO"]) ?></h1>
                            <p class="produtoMarca">Marca: <?= htmlspecialchars($produto["NM_MARCA"]) ?></p>
                            <p class="produtoCategoria">Categoria: <?= htmlspecialchars($produto["NM_CATEGORIA"]) ?></p>

                            <?php if (!empty($produto["DS_PRODUTO"])): ?>
                                <div class="produtoDescricao">
                                    <h3>Descrição</h3>
                                    <p><?= nl2br(htmlspecialchars($produto["DS_PRODUTO"])) ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="produtoPreco">
                                R$ <?= number_format((float)$produto["VL_PRODUTO"], 2, ",", ".") ?>
                            </div>

                            <div class="produtoEstoque">
                                <?php if ((int)$produto["QT_ESTOQUE"] > 0): ?>
                                    <p>Em estoque: <?= (int)$produto["QT_ESTOQUE"] ?> unidade(s)</p>
                                <?php else: ?>
                                    <p>Produto esgotado</p>
                                <?php endif; ?>
                            </div>

                            <div class="produtoBotoes">
                                <?php if ((int)$produto["QT_ESTOQUE"] > 0): ?>
                                    <a href="<?= $baseUrl ?>/carrinho/adicionar?id=<?= (int)$produto["ID_PRODUTO"] ?>" class="btn botaoCovil">
                                        Adicionar ao carrinho
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn botaoCovil" disabled>
                                        Produto esgotado
                                    </button>
                                <?php endif; ?>

                                <a href="<?= $baseUrl ?>/loja" class="btn botaoCovil">
                                    Voltar para a loja
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
