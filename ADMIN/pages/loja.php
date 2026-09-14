<?php
/*
    Página principal da loja do Covil do Dragão.

    A vitrine foi reorganizada para se aproximar da experiência da Home e da
    Glitch Productions Store: produtos em uma grade visual, imagem em destaque,
    categoria, nome, preço, estado do estoque e ação de compra no próprio card.

    A página também permite pesquisar produtos, filtrar por categoria e marca e
    escolher a ordenação sem alterar a estrutura do banco de dados.
*/

$busca = trim($_GET["q"] ?? "");
$categoriaSelecionada = filter_input(INPUT_GET, "categoria", FILTER_VALIDATE_INT);
$marcaSelecionada = filter_input(INPUT_GET, "marca", FILTER_VALIDATE_INT);
$ordenacao = $_GET["ordenar"] ?? "recentes";
$ordenacoesPermitidas = ["recentes", "nome", "precoMenor", "precoMaior"];

if (!in_array($ordenacao, $ordenacoesPermitidas, true)) {
    $ordenacao = "recentes";
}

$categoriasLoja = [];
$marcasLoja = [];
$produtos = [];

try {
    $categoriasLoja = $pdo->query(
        "SELECT ID_CATEGORIA, NM_CATEGORIA
         FROM categoria
         WHERE FL_ATIVO = 1
         ORDER BY NM_CATEGORIA"
    )->fetchAll(PDO::FETCH_ASSOC);

    $marcasLoja = $pdo->query(
        "SELECT ID_MARCA, NM_MARCA
         FROM marca
         WHERE FL_ATIVO = 1
         ORDER BY NM_MARCA"
    )->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT
                p.ID_PRODUTO,
                p.NM_PRODUTO,
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

    $parametros = [];

    if ($busca !== "") {
        $sql .= " AND (p.NM_PRODUTO LIKE :busca OR m.NM_MARCA LIKE :busca OR c.NM_CATEGORIA LIKE :busca)";
        $parametros[":busca"] = "%" . $busca . "%";
    }

    if ($categoriaSelecionada) {
        $sql .= " AND p.ID_CATEGORIA = :categoria";
        $parametros[":categoria"] = $categoriaSelecionada;
    }

    if ($marcaSelecionada) {
        $sql .= " AND p.ID_MARCA = :marca";
        $parametros[":marca"] = $marcaSelecionada;
    }

    $ordemSql = [
        "recentes" => "p.ID_PRODUTO DESC",
        "nome" => "p.NM_PRODUTO ASC",
        "precoMenor" => "p.VL_PRODUTO ASC, p.NM_PRODUTO ASC",
        "precoMaior" => "p.VL_PRODUTO DESC, p.NM_PRODUTO ASC"
    ];

    $sql .= " ORDER BY " . $ordemSql[$ordenacao];

    $consulta = $pdo->prepare($sql);

    foreach ($parametros as $chave => $valor) {
        $tipo = in_array($chave, [":categoria", ":marca"], true) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $consulta->bindValue($chave, $valor, $tipo);
    }

    $consulta->execute();
    $produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("[Loja] " . $e->getMessage());
    $produtos = [];
}

function urlLojaComFiltro($baseUrl, $parametros)
{
    $parametros = array_filter($parametros, fn($valor) => $valor !== null && $valor !== "");
    return $baseUrl . "/loja" . ($parametros ? "?" . http_build_query($parametros) : "");
}
?>

<section class="lojaPaginaNova">
    <header class="lojaCabecalhoNovo">
        <span class="lojaEtiquetaNova">EXPLORE O COVIL</span>
        <h1>Todos os produtos</h1>
        <p>Dragões, personagens e relíquias escolhidos para colecionadores de Westeros.</p>
    </header>

    <div class="lojaCategoriasRapidas">
        <a href="<?= $baseUrl ?>/loja" class="<?= !$categoriaSelecionada ? "ativo" : "" ?>">Todos</a>

        <?php foreach ($categoriasLoja as $categoria): ?>
            <a
                href="<?= htmlspecialchars(urlLojaComFiltro($baseUrl, ["categoria" => (int)$categoria["ID_CATEGORIA"]])) ?>"
                class="<?= $categoriaSelecionada === (int)$categoria["ID_CATEGORIA"] ? "ativo" : "" ?>">
                <?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <form class="lojaFiltrosNovos" method="get" action="<?= $baseUrl ?>/loja">
        <div class="lojaBuscaNova">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
                type="search"
                name="q"
                value="<?= htmlspecialchars($busca) ?>"
                placeholder="Pesquisar produtos...">
        </div>

        <select name="categoria" aria-label="Filtrar por categoria">
            <option value="">Todas as categorias</option>
            <?php foreach ($categoriasLoja as $categoria): ?>
                <option
                    value="<?= (int)$categoria["ID_CATEGORIA"] ?>"
                    <?= $categoriaSelecionada === (int)$categoria["ID_CATEGORIA"] ? "selected" : "" ?>>
                    <?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="marca" aria-label="Filtrar por marca">
            <option value="">Todas as marcas</option>
            <?php foreach ($marcasLoja as $marca): ?>
                <option
                    value="<?= (int)$marca["ID_MARCA"] ?>"
                    <?= $marcaSelecionada === (int)$marca["ID_MARCA"] ? "selected" : "" ?>>
                    <?= htmlspecialchars($marca["NM_MARCA"]) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="ordenar" aria-label="Ordenar produtos">
            <option value="recentes" <?= $ordenacao === "recentes" ? "selected" : "" ?>>Mais recentes</option>
            <option value="nome" <?= $ordenacao === "nome" ? "selected" : "" ?>>Nome A–Z</option>
            <option value="precoMenor" <?= $ordenacao === "precoMenor" ? "selected" : "" ?>>Menor preço</option>
            <option value="precoMaior" <?= $ordenacao === "precoMaior" ? "selected" : "" ?>>Maior preço</option>
        </select>

        <button type="submit" class="lojaBotaoFiltrar">Aplicar</button>

        <?php if ($busca !== "" || $categoriaSelecionada || $marcaSelecionada || $ordenacao !== "recentes"): ?>
            <a href="<?= $baseUrl ?>/loja" class="lojaLimparFiltros">Limpar</a>
        <?php endif; ?>
    </form>

    <div class="lojaResultadoLinha">
        <p><?= count($produtos) ?> produto(s) encontrado(s)</p>
    </div>

    <?php if (count($produtos) > 0): ?>
        <div class="lojaGradeNova">
            <?php foreach ($produtos as $indice => $produto): ?>
                <article class="lojaProdutoCardNovo" data-aos="fade-up" data-aos-delay="<?= min(($indice % 4) * 70, 210) ?>">
                    <a
                        href="<?= $baseUrl ?>/produto?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                        class="lojaProdutoImagemNova">

                        <?php if (!empty($produto["DS_IMAGEM"]) && imagemProdutoExiste($produto["DS_IMAGEM"])): ?>
                            <?php
                            $urlImagem = urlImagemProduto($produto["DS_IMAGEM"]);
                            $versaoImagem = versaoImagemProduto($produto["DS_IMAGEM"]);
                            ?>
                            <img
                                src="<?= htmlspecialchars($urlImagem) ?><?= $versaoImagem !== "" ? "?v=" . urlencode($versaoImagem) : "" ?>"
                                alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>"
                                loading="lazy">
                        <?php else: ?>
                            <div class="lojaProdutoSemImagemNova">
                                <i class="fa-solid fa-dragon"></i>
                            </div>
                        <?php endif; ?>

                        <?php if ((int)$produto["QT_ESTOQUE"] <= 0): ?>
                            <span class="lojaProdutoSeloNovo esgotado">Esgotado</span>
                        <?php elseif ((int)$produto["QT_ESTOQUE"] <= 3): ?>
                            <span class="lojaProdutoSeloNovo ultimas">Últimas unidades</span>
                        <?php else: ?>
                            <span class="lojaProdutoSeloNovo">Disponível</span>
                        <?php endif; ?>
                    </a>

                    <div class="lojaProdutoConteudoNovo">
                        <p class="lojaProdutoCategoriaNova">
                            <?= htmlspecialchars($produto["NM_CATEGORIA"]) ?>
                        </p>

                        <h2>
                            <a href="<?= $baseUrl ?>/produto?id=<?= (int)$produto["ID_PRODUTO"] ?>">
                                <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>
                            </a>
                        </h2>

                        <p class="lojaProdutoMarcaNova">
                            <?= htmlspecialchars($produto["NM_MARCA"]) ?>
                        </p>

                        <div class="lojaProdutoPrecoNova">
                            R$ <?= number_format((float)$produto["VL_PRODUTO"], 2, ",", ".") ?>
                        </div>

                        <div class="lojaProdutoAcoesNovas">
                            <?php if ((int)$produto["QT_ESTOQUE"] > 0): ?>
                                <a
                                    href="<?= $baseUrl ?>/carrinho/adicionar?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                    class="lojaAdicionarCarrinho">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    Adicionar
                                </a>
                            <?php else: ?>
                                <span class="lojaAdicionarCarrinho desativado">Indisponível</span>
                            <?php endif; ?>

                            <a
                                href="<?= $baseUrl ?>/produto?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                class="lojaVerProduto"
                                aria-label="Ver <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="lojaVaziaNova">
            <i class="fa-solid fa-dragon"></i>
            <h2>Nenhum produto encontrado</h2>
            <p>Tente remover os filtros ou pesquisar por outro nome.</p>
            <a href="<?= $baseUrl ?>/loja" class="lojaBotaoFiltrar">Ver todos</a>
        </div>
    <?php endif; ?>
</section>
