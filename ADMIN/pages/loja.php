<?php

/* Verifica a categoria selecionada pela URL. */
$categoriaSelecionada = filter_input(INPUT_GET, "categoria", FILTER_VALIDATE_INT);

$produtos = [];
$nomeCategoria = "Todos os produtos";

/* Busca o nome da categoria selecionada. */
if ($categoriaSelecionada) {
    $sqlCategoria = "SELECT NM_CATEGORIA
                     FROM categoria
                     WHERE ID_CATEGORIA = :id
                     AND FL_ATIVO = 1
                     LIMIT 1";

    /* Executa a consulta da categoria. */
    $consultaCategoria = $pdo->prepare($sqlCategoria);
    $consultaCategoria->bindValue(":id", $categoriaSelecionada, PDO::PARAM_INT);
    $consultaCategoria->execute();

    /* Guarda os dados da categoria. */
    $categoria = $consultaCategoria->fetch(PDO::FETCH_ASSOC);

    if ($categoria) {
        $nomeCategoria = $categoria["NM_CATEGORIA"];
    } else {
        $categoriaSelecionada = null;
    }
}

/* Busca os produtos ativos disponíveis na loja. */
$sql = "SELECT
            p.ID_PRODUTO,
            p.NM_PRODUTO,
            p.DS_PRODUTO,
            p.VL_PRODUTO,
            p.QT_ESTOQUE,
            m.NM_MARCA,
            c.NM_CATEGORIA,
            (
                SELECT pi.DS_IMAGEM
                FROM produto_imagem pi
                WHERE pi.ID_PRODUTO = p.ID_PRODUTO
                ORDER BY pi.FL_PRINCIPAL DESC, pi.NR_ORDEM ASC
                LIMIT 1
            ) AS DS_IMAGEM
        FROM produto p
        INNER JOIN marca m ON m.ID_MARCA = p.ID_MARCA
        INNER JOIN categoria c ON c.ID_CATEGORIA = p.ID_CATEGORIA
        WHERE p.FL_ATIVO = 1
        AND m.FL_ATIVO = 1
        AND c.FL_ATIVO = 1";

/* Aplica o filtro de categoria quando necessário. */
if ($categoriaSelecionada) {
    $sql .= " AND p.ID_CATEGORIA = :categoria";
}

$sql .= " ORDER BY p.NM_PRODUTO";

/* Executa a consulta dos produtos. */
$consulta = $pdo->prepare($sql);

if ($categoriaSelecionada) {
    $consulta->bindValue(":categoria", $categoriaSelecionada, PDO::PARAM_INT);
}

$consulta->execute();

/* Guarda os produtos encontrados. */
$produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Loja</h1>
                <p><?= htmlspecialchars($nomeCategoria) ?></p>
            </div>

            <div class="card-body p-4">
                <?php if (count($produtos) > 0): ?>
                    <div class="row g-4 listaProdutos">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="col-12 col-md-6 col-lg-4 produtoColuna">
                                <div class="produtoCard">
                                    <div class="produtoImagem">

                                        <!-- Mostra a imagem principal do produto. -->
                                        <?php if (!empty($produto["DS_IMAGEM"])): ?>
                                            <img
                                                src="<?= htmlspecialchars($produto["DS_IMAGEM"]) ?>"
                                                alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                                        <?php else: ?>
                                            <span>Nenhuma imagem cadastrada.</span>
                                        <?php endif; ?>

                                    </div>

                                    <div class="produtoInformacoes">
                                        <h3>
                                            <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>
                                        </h3>

                                        <p class="produtoMarca">
                                            <?= htmlspecialchars($produto["NM_MARCA"]) ?>
                                        </p>

                                        <p class="produtoCategoria">
                                            <?= htmlspecialchars($produto["NM_CATEGORIA"]) ?>
                                        </p>

                                        <?php if (!empty($produto["DS_PRODUTO"])): ?>
                                            <p class="produtoDescricao">
                                                <?= htmlspecialchars($produto["DS_PRODUTO"]) ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="produtoPreco">
                                            R$ <?= number_format((float)$produto["VL_PRODUTO"], 2, ",", ".") ?>
                                        </div>

                                        <?php if ((int)$produto["QT_ESTOQUE"] > 0): ?>
                                            <p class="produtoDisponivel">
                                                Em estoque
                                            </p>
                                        <?php else: ?>
                                            <p class="produtoEsgotado">
                                                Produto esgotado
                                            </p>
                                        <?php endif; ?>

                                        <div class="produtoListaBotoes">

                                            <!-- Acessa os detalhes do produto. -->
                                            <a
                                                href="produto?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                                class="btn botaoCovil">
                                                Ver produto
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="produtoVazio">
                        <h3>Nenhum produto encontrado</h3>
                        <p>
                            Não existem produtos disponíveis nesta categoria no momento.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>