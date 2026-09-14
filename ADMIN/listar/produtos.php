<?php
/*
    Esta página lista os produtos ativos cadastrados no banco.
    Somente administradores podem acessar esta página.

    Cada produto mostra nome, marca, categoria, descrição, preço,
    estoque e até 3 imagens cadastradas.

    As imagens vêm da tabela produto_imagem.
    A imagem principal aparece primeiro no carrossel.

    O administrador pode editar ou desativar um produto.
    Produtos desativados continuam no banco, mas não aparecem na lista.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

$sql = "SELECT p.ID_PRODUTO, p.NM_PRODUTO, p.DS_PRODUTO, p.VL_PRODUTO,
               p.QT_ESTOQUE, m.NM_MARCA, c.NM_CATEGORIA
        FROM produto p
        INNER JOIN marca m ON m.ID_MARCA = p.ID_MARCA
        INNER JOIN categoria c ON c.ID_CATEGORIA = p.ID_CATEGORIA
        WHERE p.FL_ATIVO = 1
        ORDER BY p.NM_PRODUTO";

$consulta = $pdo->query($sql);
$produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Produtos</h1>
                <p>Gerencie os produtos cadastrados no Covil do Dragão</p>
            </div>

            <div class="card-body p-4">
                <div class="colabTopo mb-4">
                    <h2>Produtos cadastrados</h2>
                    <a href="cadastrarProduto" class="btn botaoCovil botaoCadastrarMarca">Cadastrar</a>
                </div>

                <?php if (count($produtos) > 0): ?>
                    <div class="listaProdutos">
                        <?php foreach ($produtos as $produto): ?>
                            <?php
                            /* Busca as imagens do produto. */
                            $sqlImagens = "SELECT DS_IMAGEM, FL_PRINCIPAL, NR_ORDEM
                                           FROM produto_imagem
                                           WHERE ID_PRODUTO = :id
                                           ORDER BY FL_PRINCIPAL DESC, NR_ORDEM ASC
                                           LIMIT 3";

                            $consultaImagens = $pdo->prepare($sqlImagens);
                            $consultaImagens->bindValue(":id", (int)$produto["ID_PRODUTO"], PDO::PARAM_INT);
                            $consultaImagens->execute();

                            $imagens = $consultaImagens->fetchAll(PDO::FETCH_ASSOC);
                            $imagens = array_values(array_filter($imagens, fn($imagem) => imagemProdutoExiste($imagem["DS_IMAGEM"] ?? "")));
                            ?>

                            <div class="produtoCard">
                                <div class="produtoImagem">
                                    <?php if (count($imagens) > 0): ?>
                                        <div id="produtoCarrossel<?= (int)$produto["ID_PRODUTO"] ?>" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                <?php foreach ($imagens as $indice => $imagem): ?>
                                                    <div class="carousel-item <?= $indice === 0 ? "active" : "" ?>">
                                                        <?php
                                                        $urlImagem = urlImagemProduto($imagem["DS_IMAGEM"]);
                                                        $versaoImagem = versaoImagemProduto($imagem["DS_IMAGEM"]);
                                                        ?>
                                                        <img src="<?= htmlspecialchars($urlImagem) ?><?= $versaoImagem !== "" ? "?v=" . urlencode($versaoImagem) : "" ?>" alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <?php if (count($imagens) > 1): ?>
                                                <button class="carousel-control-prev" type="button" data-bs-target="#produtoCarrossel<?= (int)$produto["ID_PRODUTO"] ?>" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                    <span class="visually-hidden">Anterior</span>
                                                </button>

                                                <button class="carousel-control-next" type="button" data-bs-target="#produtoCarrossel<?= (int)$produto["ID_PRODUTO"] ?>" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                    <span class="visually-hidden">Próximo</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span>Nenhuma imagem cadastrada.</span>
                                    <?php endif; ?>
                                </div>

                                <div class="produtoInformacoes">
                                    <h3><?= htmlspecialchars($produto["NM_PRODUTO"]) ?></h3>

                                    <p class="produtoMarca">
                                        Marca: <?= htmlspecialchars($produto["NM_MARCA"]) ?>
                                    </p>

                                    <p class="produtoCategoria">
                                        Categoria: <?= htmlspecialchars($produto["NM_CATEGORIA"]) ?>
                                    </p>

                                    <?php if (!empty($produto["DS_PRODUTO"])): ?>
                                        <p class="produtoDescricao">
                                            <?= htmlspecialchars($produto["DS_PRODUTO"]) ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="produtoDados">
                                        <p class="produtoPreco">
                                            R$ <?= number_format((float)$produto["VL_PRODUTO"], 2, ",", ".") ?>
                                        </p>

                                        <p class="produtoEstoque">
                                            Estoque: <?= (int)$produto["QT_ESTOQUE"] ?>
                                        </p>
                                    </div>

                                    <div class="produtoListaBotoes">
                                        <a href="editarProduto?id=<?= (int)$produto["ID_PRODUTO"] ?>" class="btn botaoCovil">Editar</a>

                                        <form method="post" action="index.php?param=salvar/desativarProduto" onsubmit="return confirm('Deseja realmente desativar este produto?');">
                                            <input type="hidden" name="id" value="<?= (int)$produto["ID_PRODUTO"] ?>">
                                            <button type="submit" class="btn botaoCovil">Desativar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="produtoVazio">
                        <h3>Nenhum produto cadastrado</h3>
                        <p>Ainda não existem produtos ativos cadastrados no sistema.</p>
                        <a href="cadastrarProduto" class="btn botaoCovil">Cadastrar produto</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>