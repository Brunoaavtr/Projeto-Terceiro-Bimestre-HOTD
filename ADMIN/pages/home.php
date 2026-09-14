<?php
/*
    Home principal do projeto Covil do Dragão.

    Esta página funciona como uma vitrine da loja. Ela busca produtos ativos,
    produtos mais vendidos e categorias diretamente no banco de dados para que
    o conteúdo da Home acompanhe automaticamente os cadastros feitos no sistema.

    A estrutura visual foi organizada em blocos: apresentação principal,
    novidades, destaque editorial, mais vendidos, categorias, trailer oficial,
    vídeos do Covil e benefícios da loja.

    As imagens já existentes no projeto continuam sendo utilizadas para manter
    a identidade visual do Covil do Dragão. A logo também recebe um destaque
    próprio na abertura da página, aproveitando o efeito visual usado no projeto.
*/

$produtosDestaque = [];
$maisVendidos = [];
$categoriasHome = [];

try {
    $sqlDestaques = "SELECT
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
                    AND c.FL_ATIVO = 1
                    ORDER BY p.ID_PRODUTO DESC
                    LIMIT 4";

    $consultaDestaques = $pdo->query($sqlDestaques);
    $produtosDestaque = $consultaDestaques->fetchAll(PDO::FETCH_ASSOC);

    $sqlMaisVendidos = "SELECT
                            p.ID_PRODUTO,
                            p.NM_PRODUTO,
                            p.VL_PRODUTO,
                            p.QT_ESTOQUE,
                            m.NM_MARCA,
                            c.NM_CATEGORIA,
                            SUM(pi.QT_PRODUTO) AS QT_VENDIDA,
                            (
                                SELECT pim.DS_IMAGEM
                                FROM produto_imagem pim
                                WHERE pim.ID_PRODUTO = p.ID_PRODUTO
                                ORDER BY pim.FL_PRINCIPAL DESC, pim.NR_ORDEM ASC
                                LIMIT 1
                            ) AS DS_IMAGEM
                        FROM pedido_item pi
                        INNER JOIN pedido pe ON pe.ID_PEDIDO = pi.ID_PEDIDO
                        INNER JOIN produto p ON p.ID_PRODUTO = pi.ID_PRODUTO
                        INNER JOIN marca m ON m.ID_MARCA = p.ID_MARCA
                        INNER JOIN categoria c ON c.ID_CATEGORIA = p.ID_CATEGORIA
                        WHERE pe.DS_STATUS = 'Concluído'
                        AND p.FL_ATIVO = 1
                        AND m.FL_ATIVO = 1
                        AND c.FL_ATIVO = 1
                        GROUP BY
                            p.ID_PRODUTO,
                            p.NM_PRODUTO,
                            p.VL_PRODUTO,
                            p.QT_ESTOQUE,
                            m.NM_MARCA,
                            c.NM_CATEGORIA
                        ORDER BY QT_VENDIDA DESC, p.NM_PRODUTO ASC
                        LIMIT 4";

    $consultaMaisVendidos = $pdo->query($sqlMaisVendidos);
    $maisVendidos = $consultaMaisVendidos->fetchAll(PDO::FETCH_ASSOC);

    if (count($maisVendidos) === 0) {
        $maisVendidos = $produtosDestaque;
    }

    $sqlCategoriasHome = "SELECT ID_CATEGORIA, NM_CATEGORIA, DS_CATEGORIA
                          FROM categoria
                          WHERE FL_ATIVO = 1
                          ORDER BY ID_CATEGORIA
                          LIMIT 4";

    $consultaCategoriasHome = $pdo->query($sqlCategoriasHome);
    $categoriasHome = $consultaCategoriasHome->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtosDestaque = [];
    $maisVendidos = [];
    $categoriasHome = [];
}

$imagensCategorias = [
    1 => "sun.webp",
    2 => "meleys1.webp",
    3 => "marcatexto.webp",
    4 => "craniocarax.webp"
];
?>

<div class="homePagina">
    <section class="homeHero">
        <img
            class="homeHeroFundo"
            src="<?= $baseUrl ?>/IMG/backgraund.jpg"
            alt="Universo do Covil do Dragão">

        <div class="homeHeroSombra"></div>

        <div class="homeHeroConteudo">

            <p class="homeHeroEtiqueta" data-aos="fade-up" data-aos-delay="150">
                COLECIONÁVEIS • DRAGÕES • WESTEROS
            </p>

            <h1 data-aos="fade-up" data-aos-delay="250">
                ENTRE NO COVIL.<br>
                ESCOLHA SEU DRAGÃO.
            </h1>

            <p class="homeHeroTexto" data-aos="fade-up" data-aos-delay="350">
                Peças colecionáveis inspiradas em House of the Dragon e em mundos
                onde fogo, sangue e lendas ganham forma.
            </p>

            <div class="homeHeroBotoes" data-aos="fade-up" data-aos-delay="450">
                <a href="<?= $baseUrl ?>/loja" class="homeBotao homeBotaoPrincipal">
                    Explorar a loja
                </a>

                <a href="#novidades" class="homeBotao homeBotaoSecundario">
                    Ver novidades
                </a>
            </div>
        </div>

        <a href="#novidades" class="homeHeroSeta" aria-label="Ir para novidades">
            <i class="fa-solid fa-chevron-down"></i>
        </a>
    </section>

    <section class="homeSecao" id="novidades">
        <div class="homeSecaoCabecalho">
            <div>
                <span class="homeSecaoEtiqueta">RECÉM-CHEGADOS AO COVIL</span>
                <h2>Novos e destaques</h2>
            </div>

            <a href="<?= $baseUrl ?>/loja" class="homeLinkSecao">
                Ver todos <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <?php if (count($produtosDestaque) > 0): ?>
            <div class="homeProdutosGrade">
                <?php foreach ($produtosDestaque as $produto): ?>
                    <article class="homeProdutoCard" data-aos="fade-up">
                        <a
                            href="<?= $baseUrl ?>/produto?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                            class="homeProdutoImagem">

                            <?php if (!empty($produto["DS_IMAGEM"]) && imagemProdutoExiste($produto["DS_IMAGEM"])): ?>
                                <?php
                                $urlImagem = urlImagemProduto($produto["DS_IMAGEM"]);
                                $versaoImagem = versaoImagemProduto($produto["DS_IMAGEM"]);
                                ?>
                                <img
                                    src="<?= htmlspecialchars($urlImagem) ?><?= $versaoImagem !== "" ? "?v=" . urlencode($versaoImagem) : "" ?>"
                                    alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                            <?php else: ?>
                                <div class="homeProdutoSemImagem">
                                    <i class="fa-solid fa-dragon"></i>
                                </div>
                            <?php endif; ?>

                            <span class="homeProdutoSelo">Destaque</span>
                        </a>

                        <div class="homeProdutoConteudo">
                            <p class="homeProdutoCategoria">
                                <?= htmlspecialchars($produto["NM_CATEGORIA"]) ?>
                            </p>

                            <h3>
                                <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>
                            </h3>

                            <div class="homeProdutoRodape">
                                <strong>
                                    R$ <?= number_format((float)$produto["VL_PRODUTO"], 2, ",", ".") ?>
                                </strong>

                                <a
                                    href="<?= $baseUrl ?>/produto?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                    aria-label="Ver <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="homeMensagemVazia">
                <p>Nenhum produto em destaque está disponível no momento.</p>
            </div>
        <?php endif; ?>
    </section>

    <section class="homeEditorial" data-aos="fade-up">
        <div class="homeEditorialImagem">
            <img
                src="<?= $baseUrl ?>/IMG/meleys1.webp"
                alt="Estatueta de dragão do Covil">
        </div>

        <div class="homeEditorialConteudo">
            <span class="homeSecaoEtiqueta">CRIADOS PARA COLECIONADORES</span>
            <h2>Do fogo à sua coleção</h2>

            <p>
                O Covil reúne dragões, personagens e relíquias inspiradas nos
                grandes símbolos de Westeros. Cada peça foi escolhida para tornar
                a coleção mais marcante, seja pelo nível de detalhe, pela presença
                ou pela história que representa.
            </p>

            <p>
                Explore o catálogo, descubra novas peças e encontre aquela que
                merece ocupar o centro da sua coleção.
            </p>

            <a href="<?= $baseUrl ?>/loja" class="homeBotao homeBotaoPrincipal">
                Conhecer a coleção
            </a>
        </div>
    </section>

    <section class="homeSecao">
        <div class="homeSecaoCabecalho">
            <div>
                <span class="homeSecaoEtiqueta">OS FAVORITOS DO REINO</span>
                <h2>Mais vendidos</h2>
            </div>

            <a href="<?= $baseUrl ?>/loja" class="homeLinkSecao">
                Navegar pela loja <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <?php if (count($maisVendidos) > 0): ?>
            <div class="homeProdutosGrade">
                <?php foreach ($maisVendidos as $produto): ?>
                    <article class="homeProdutoCard" data-aos="fade-up">
                        <a
                            href="<?= $baseUrl ?>/produto?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                            class="homeProdutoImagem">

                            <?php if (!empty($produto["DS_IMAGEM"]) && imagemProdutoExiste($produto["DS_IMAGEM"])): ?>
                                <?php
                                $urlImagem = urlImagemProduto($produto["DS_IMAGEM"]);
                                $versaoImagem = versaoImagemProduto($produto["DS_IMAGEM"]);
                                ?>
                                <img
                                    src="<?= htmlspecialchars($urlImagem) ?><?= $versaoImagem !== "" ? "?v=" . urlencode($versaoImagem) : "" ?>"
                                    alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                            <?php else: ?>
                                <div class="homeProdutoSemImagem">
                                    <i class="fa-solid fa-dragon"></i>
                                </div>
                            <?php endif; ?>

                            <span class="homeProdutoSelo homeProdutoSeloVenda">
                                Mais vendido
                            </span>
                        </a>

                        <div class="homeProdutoConteudo">
                            <p class="homeProdutoCategoria">
                                <?= htmlspecialchars($produto["NM_MARCA"]) ?>
                            </p>

                            <h3>
                                <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>
                            </h3>

                            <div class="homeProdutoRodape">
                                <strong>
                                    R$ <?= number_format((float)$produto["VL_PRODUTO"], 2, ",", ".") ?>
                                </strong>

                                <a
                                    href="<?= $baseUrl ?>/produto?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                    aria-label="Ver <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="homeSecao homeCategoriasSecao">
        <div class="homeSecaoCabecalho">
            <div>
                <span class="homeSecaoEtiqueta">ESCOLHA SEU CAMINHO</span>
                <h2>Compre por categoria</h2>
            </div>
        </div>

        <div class="homeCategoriasGrade">
            <?php foreach ($categoriasHome as $categoria): ?>
                <?php
                $idCategoria = (int)$categoria["ID_CATEGORIA"];
                $imagemCategoria = $imagensCategorias[$idCategoria] ?? "drogon.webp";
                ?>

                <a
                    href="<?= $baseUrl ?>/loja?categoria=<?= $idCategoria ?>"
                    class="homeCategoriaCard"
                    data-aos="fade-up">

                    <img
                        src="<?= $baseUrl ?>/IMG/<?= htmlspecialchars($imagemCategoria) ?>"
                        alt="<?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>">

                    <div class="homeCategoriaSombra"></div>

                    <div class="homeCategoriaConteudo">
                        <span>Explorar</span>
                        <h3><?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?></h3>

                        <?php if (!empty($categoria["DS_CATEGORIA"])): ?>
                            <p><?= htmlspecialchars($categoria["DS_CATEGORIA"]) ?></p>
                        <?php endif; ?>

                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="homeTrailer" data-aos="fade-up">
        <div class="homeTrailerVideo">
            <iframe
                src="https://www.youtube-nocookie.com/embed/0JlMjgqduVw"
                title="House of the Dragon Season 3 - Trailer oficial"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen>
            </iframe>
        </div>

        <div class="homeTrailerConteudo">
            <span class="homeSecaoEtiqueta">HOUSE OF THE DRAGON</span>
            <h2>O fogo continua</h2>

            <p>
                Entre novamente em Westeros com o trailer oficial da terceira
                temporada e relembre o universo que inspira o Covil do Dragão.
            </p>

            <a
                href="https://www.youtube.com/watch?v=0JlMjgqduVw"
                target="_blank"
                rel="noopener noreferrer"
                class="homeBotao homeBotaoSecundario">
                Abrir no YouTube
            </a>
        </div>
    </section>

    <section class="homeSecao homeClipesSecao">
        <div class="homeSecaoCabecalho">
            <div>
                <span class="homeSecaoEtiqueta">DENTRO DO COVIL</span>
                <h2>Veja os dragões de perto</h2>
            </div>
        </div>

        <div class="homeClipesGrade">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="homeClipeCard" data-aos="fade-up">
                    <video autoplay muted loop playsinline preload="metadata">
                        <source src="<?= $baseUrl ?>/IMG/video<?= $i ?>.mp4" type="video/mp4">
                    </video>

                    <div class="homeClipeSombra"></div>

                    <div class="homeClipeTexto">
                        <span>Covil do Dragão</span>
                        <strong>Conteúdo quente como fogo!</strong>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </section>

    <section class="homeBeneficios">
        <article>
            <img src="<?= $baseUrl ?>/IMG/diamante.png" alt="Colecionáveis selecionados">
            <div>
                <h3>Peças para colecionar</h3>
                <p>Um catálogo criado para quem gosta de fantasia e detalhes.</p>
            </div>
        </article>

        <article>
            <img src="<?= $baseUrl ?>/IMG/pagamento.png" alt="Compra pelo site">
            <div>
                <h3>Compra pelo Covil</h3>
                <p>Escolha seus produtos e finalize tudo dentro da plataforma.</p>
            </div>
        </article>

        <article>
            <img src="<?= $baseUrl ?>/IMG/caminhao.png" alt="Acompanhe seus pedidos">
            <div>
                <h3>Acompanhe seus pedidos</h3>
                <p>Consulte suas compras e o andamento dos pedidos pelo perfil.</p>
            </div>
        </article>
    </section>
</div>
