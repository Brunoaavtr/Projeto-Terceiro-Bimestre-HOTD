<?php

/* Verifica se o usuário está logado. */
if (!isset($_SESSION["fogoEsangue"])) {
    header("Location: " . $baseUrl . "/login");
    exit;
}

/* Pega o ID do usuário logado. */
$idUsuario = $_SESSION["fogoEsangue"];

/* Busca a foto do usuário. */
$sqlUsuario = "SELECT
                   DS_FOTO
               FROM usuario
               WHERE ID_USUARIO = :id
               AND FL_ATIVO = 1";

/* Executa a consulta do usuário. */
$consultaUsuario = $pdo->prepare($sqlUsuario);
$consultaUsuario->execute([
    ":id" => $idUsuario
]);

/* Guarda os dados do usuário. */
$usuario = $consultaUsuario->fetch(PDO::FETCH_ASSOC);

if ($subrota === null) {
?>

    <div class="container py-5">
        <div class="areaCompras">
            <div class="comprasCabecalho text-center">
                <!-- Mostra a foto ou o ícone do usuário. -->
                <label for="foto" class="fotoEscolha">
                    <?php if (!empty($usuario["DS_FOTO"])): ?>
                        <img
                            src="<?= $baseUrl . "/" . ltrim(htmlspecialchars($usuario["DS_FOTO"]), "/") ?>"
                            class="fotoPerfil"
                            alt="Foto de perfil">
                    <?php else: ?>
                        <div class="fotoPerfil">
                            <i class="fa-solid fa-dragon"></i>
                        </div>
                    <?php endif; ?>
                </label>

                <h1>Minha área de compras</h1>
                <p>
                    Escolha uma opção para continuar sua jornada no Covil do Dragão
                </p>
            </div>

            <div class="opcoesCompras">
                <!-- Acessa o carrinho do usuário. -->
                <a
                    href="<?= $baseUrl ?>/carrinho/itens"
                    class="botaoCompras carrinhoOpcao">
                    <div class="comprasIconeCard">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>

                    <div class="comprasTexto">
                        <h2>Meu carrinho</h2>
                        <p>
                            Confira os produtos que você escolheu
                            e finalize sua compra.
                        </p>
                    </div>

                    <span class="comprasSeta">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                <!-- Acessa os pedidos realizados pelo usuário. -->
                <a
                    href="<?= $baseUrl ?>/pedidos"
                    class="botaoCompras pedidosOpcao">
                    <div class="comprasIconeCard">
                        <i class="fa-solid fa-box"></i>
                    </div>

                    <div class="comprasTexto">
                        <h2>Meus pedidos</h2>
                        <p>
                            Consulte os pedidos que você já realizou
                            no Covil do Dragão.
                        </p>
                    </div>

                    <span class="comprasSeta">
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>

<?php

} elseif ($subrota === "itens") {

    /* Cria o carrinho na sessão caso ele ainda não exista. */
    if (!isset($_SESSION["carrinho"])) {
        $_SESSION["carrinho"] = [];
    }

    /* Pega os produtos armazenados no carrinho. */
    $carrinho = $_SESSION["carrinho"];

    /* Cria a lista de produtos do carrinho. */
    $produtosCarrinho = [];

    /* Inicia o valor total do carrinho. */
    $totalCarrinho = 0;

    if (count($carrinho) > 0) {
        /* Pega os IDs dos produtos armazenados na sessão. */
        $ids = array_keys($carrinho);

        /* Cria uma lista apenas com IDs válidos. */
        $idsValidos = [];

        foreach ($ids as $id) {
            if (filter_var($id, FILTER_VALIDATE_INT) !== false) {
                $idsValidos[] = (int)$id;
            }
        }

        if (count($idsValidos) > 0) {
            /* Cria os espaços para os IDs da consulta SQL. */
            $placeholders = implode(
                ",",
                array_fill(0, count($idsValidos), "?")
            );

            /* Busca os produtos do carrinho no banco. */
            $sql = "SELECT
                        p.ID_PRODUTO,
                        p.NM_PRODUTO,
                        p.VL_PRODUTO,
                        p.QT_ESTOQUE,
                        (
                            SELECT pi.DS_IMAGEM
                            FROM produto_imagem pi
                            WHERE pi.ID_PRODUTO = p.ID_PRODUTO
                            ORDER BY pi.FL_PRINCIPAL DESC, pi.NR_ORDEM ASC
                            LIMIT 1
                        ) AS DS_IMAGEM
                    FROM produto p
                    WHERE p.ID_PRODUTO IN ($placeholders)
                    AND p.FL_ATIVO = 1";

            /* Prepara a consulta dos produtos. */
            $consulta = $pdo->prepare($sql);

            /* Adiciona os IDs dos produtos na consulta. */
            foreach ($idsValidos as $indice => $id) {
                $consulta->bindValue(
                    $indice + 1,
                    $id,
                    PDO::PARAM_INT
                );
            }

            /* Executa a consulta dos produtos. */
            $consulta->execute();

            /* Guarda os produtos encontrados. */
            $produtosBanco = $consulta->fetchAll(PDO::FETCH_ASSOC);

            /* Organiza os produtos encontrados no carrinho. */
            foreach ($produtosBanco as $produto) {
                /* Pega o ID do produto. */
                $idProduto = (int)$produto["ID_PRODUTO"];

                /* Pega a quantidade escolhida pelo usuário. */
                $quantidade = (int)($carrinho[$idProduto] ?? 0);

                /* Remove produtos com quantidade inválida. */
                if ($quantidade < 1) {
                    unset($_SESSION["carrinho"][$idProduto]);
                    continue;
                }

                /* Ajusta a quantidade para o estoque disponível. */
                if ($quantidade > (int)$produto["QT_ESTOQUE"]) {
                    $quantidade = (int)$produto["QT_ESTOQUE"];
                    $_SESSION["carrinho"][$idProduto] = $quantidade;
                }

                /* Remove o produto caso não exista estoque. */
                if ($quantidade === 0) {
                    unset($_SESSION["carrinho"][$idProduto]);
                    continue;
                }

                /* Calcula o subtotal do produto. */
                $subtotal = (float)$produto["VL_PRODUTO"] * $quantidade;

                /* Adiciona a quantidade ao produto. */
                $produto["QUANTIDADE"] = $quantidade;

                /* Adiciona o subtotal ao produto. */
                $produto["SUBTOTAL"] = $subtotal;

                /* Adiciona o produto à lista do carrinho. */
                $produtosCarrinho[] = $produto;

                /* Soma o subtotal ao total da compra. */
                $totalCarrinho += $subtotal;
            }
        }
    }
?>

    <div class="container py-5">
        <div class="carrinhoPainel">
            <div class="carrinhoCabecalho text-center">
                <h1>Meu carrinho</h1>
                <p>
                    Confira os produtos escolhidos antes de finalizar sua compra
                </p>
            </div>

            <div class="carrinhoConteudo">
                <?php if (count($produtosCarrinho) > 0): ?>
                    <div class="listaCarrinho">
                        <?php foreach ($produtosCarrinho as $produto): ?>
                            <div class="carrinhoProduto">
                                <div class="carrinhoImagem">
                                    <!-- Mostra a imagem principal do produto. -->
                                    <?php if (!empty($produto["DS_IMAGEM"])): ?>
                                        <img
                                            src="<?= $baseUrl . "/" . ltrim(htmlspecialchars($produto["DS_IMAGEM"]), "/") ?>"
                                            alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                                    <?php else: ?>
                                        <i class="fa-solid fa-dragon"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="carrinhoInformacoes">
                                    <h3>
                                        <?= htmlspecialchars($produto["NM_PRODUTO"]) ?>
                                    </h3>

                                    <p class="carrinhoPreco">
                                        <span>Preço unitário</span>
                                        R$
                                        <?= number_format(
                                            (float)$produto["VL_PRODUTO"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>
                                    </p>

                                    <div class="carrinhoQuantidade">
                                        <span>Quantidade</span>

                                        <div class="quantidadeControles">
                                            <!-- Diminui a quantidade do produto. -->
                                            <a
                                                href="<?= $baseUrl ?>/carrinho/menos?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                                class="quantidadeBotao">
                                                -
                                            </a>

                                            <strong>
                                                <?= (int)$produto["QUANTIDADE"] ?>
                                            </strong>

                                            <!-- Aumenta a quantidade do produto. -->
                                            <a
                                                href="<?= $baseUrl ?>/carrinho/mais?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                                class="quantidadeBotao">
                                                +
                                            </a>
                                        </div>
                                    </div>

                                    <div class="carrinhoFinal">
                                        <p class="carrinhoSubtotal">
                                            Subtotal:
                                            <strong>
                                                R$
                                                <?= number_format(
                                                    (float)$produto["SUBTOTAL"],
                                                    2,
                                                    ",",
                                                    "."
                                                ) ?>
                                            </strong>
                                        </p>

                                        <!-- Remove o produto do carrinho. -->
                                        <a
                                            href="<?= $baseUrl ?>/carrinho/remover?id=<?= (int)$produto["ID_PRODUTO"] ?>"
                                            class="removerProduto">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="carrinhoResumo">
                        <div class="resumoTexto">
                            <span>Total da compra</span>
                            <strong>
                                R$
                                <?= number_format(
                                    $totalCarrinho,
                                    2,
                                    ",",
                                    "."
                                ) ?>
                            </strong>
                        </div>

                        <div class="carrinhoResumoBotoes">
                            <!-- Volta para a loja. -->
                            <a
                                href="<?= $baseUrl ?>/loja"
                                class="btn botaoCovil">
                                loja
                            </a>

                            <!-- Vai para a finalização da compra. -->
                            <a
                                href="<?= $baseUrl ?>/finalizarCompra"
                                class="btn botaoCovil">
                                Finalizar
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="carrinhoVazio">
                        <div class="carrinhoVazioIcone">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>

                        <h3>Seu carrinho está vazio</h3>
                        <p>
                            Ainda não existem produtos aguardando pela sua compra.
                        </p>

                        <!-- Leva o usuário para a loja. -->
                        <a
                            href="<?= $baseUrl ?>/loja"
                            class="btn botaoCovil">
                            <i class="fa-solid fa-store"></i>
                            Ir para a loja
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php

} else {

    /* Mostra a página de erro para uma subrota inválida. */
    include __DIR__ . "/err.php";
}

?>