<?php

/* Verifica se o usuário está logado. */
if (!isset($_SESSION["fogoEsangue"])) {
    header("Location: login");
    exit;
}

/* Verifica se o carrinho possui produtos. */
if (!isset($_SESSION["carrinho"]) || count($_SESSION["carrinho"]) === 0) {
    header("Location: carrinho/itens");
    exit;
}

/* Pega os produtos armazenados no carrinho. */
$carrinho = $_SESSION["carrinho"];
$produtosCompra = [];
$totalCompra = 0;
$ids = array_keys($carrinho);
$idsValidos = [];

/* Verifica quais IDs são válidos. */
foreach ($ids as $id) {
    if (filter_var($id, FILTER_VALIDATE_INT) !== false) {
        $idsValidos[] = (int)$id;
    }
}

if (count($idsValidos) === 0) {
    header("Location: carrinho/itens");
    exit;
}

/* Cria os espaços para os IDs da consulta. */
$placeholders = implode(",", array_fill(0, count($idsValidos), "?"));

/* Busca os produtos atualizados no banco. */
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
    $consulta->bindValue($indice + 1, $id, PDO::PARAM_INT);
}

/* Executa a consulta dos produtos. */
$consulta->execute();

/* Guarda os produtos encontrados. */
$produtosBanco = $consulta->fetchAll(PDO::FETCH_ASSOC);

/* Organiza os produtos para a finalização da compra. */
foreach ($produtosBanco as $produto) {
    $idProduto = (int)$produto["ID_PRODUTO"];
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

    $produto["QUANTIDADE"] = $quantidade;
    $produto["SUBTOTAL"] = $subtotal;

    /* Adiciona o produto à compra. */
    $produtosCompra[] = $produto;

    /* Soma o subtotal ao valor da compra. */
    $totalCompra += $subtotal;
}

if (count($produtosCompra) === 0) {
    header("Location: carrinho/itens");
    exit;
}
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Finalizar compra</h1>
                <p>Confira os produtos e calcule o frete</p>
            </div>

            <div class="card-body p-4">
                <h2 class="mb-4">Resumo da compra</h2>

                <?php foreach ($produtosCompra as $produto): ?>
                    <div class="carrinhoProduto">
                        <div class="carrinhoImagem">

                            <!-- Mostra a imagem do produto. -->
                            <?php if (!empty($produto["DS_IMAGEM"])): ?>
                                <img
                                    src="<?= htmlspecialchars($produto["DS_IMAGEM"]) ?>"
                                    alt="<?= htmlspecialchars($produto["NM_PRODUTO"]) ?>">
                            <?php else: ?>
                                <span>Nenhuma imagem cadastrada.</span>
                            <?php endif; ?>

                        </div>

                        <div class="carrinhoInformacoes">
                            <h3><?= htmlspecialchars($produto["NM_PRODUTO"]) ?></h3>

                            <p>
                                Quantidade:
                                <?= (int)$produto["QUANTIDADE"] ?>
                            </p>

                            <p>
                                Preço unitário:
                                R$ <?= number_format((float)$produto["VL_PRODUTO"], 2, ",", ".") ?>
                            </p>

                            <p class="carrinhoSubtotal">
                                Subtotal:
                                R$ <?= number_format((float)$produto["SUBTOTAL"], 2, ",", ".") ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-4">
                    <h2>Endereço de entrega</h2>

                    <div class="mb-3">
                        <div class="tituloCampoMarca">CEP:</div>

                        <div class="input-group">
                            <input
                                type="text"
                                id="cepFrete"
                                class="form-control"
                                placeholder="00000-000"
                                maxlength="9">

                            <!-- Solicita o cálculo do frete. -->
                            <button
                                type="button"
                                class="btn botaoCovil"
                                id="calcularFrete">
                                Calcular frete
                            </button>
                        </div>

                        <div class="form-text">
                            Informe seu CEP para calcular o valor do frete.
                        </div>
                    </div>

                    <div id="enderecoFrete" class="mb-3"></div>
                    <div id="mensagemFrete" class="mb-3"></div>
                </div>

                <div class="carrinhoResumo mt-4">
                    <p>
                        Produtos:
                        <strong id="valorProdutos">
                            R$ <?= number_format($totalCompra, 2, ",", ".") ?>
                        </strong>
                    </p>

                    <p>
                        Frete:
                        <strong id="valorFrete">R$ 0,00</strong>
                    </p>

                    <h2>
                        Total:
                        <strong id="valorTotal">
                            R$ <?= number_format($totalCompra, 2, ",", ".") ?>
                        </strong>
                    </h2>
                </div>

                <div class="cadastroMarcaBotoes mt-4">

                    <!-- Volta para o carrinho. -->
                    <a href="carrinho/itens" class="btn botaoCovil">
                        Voltar para o carrinho
                    </a>

                    <!-- Envia os dados para finalizar a compra. -->
                    <form
                        method="post"
                        action="index.php?param=salvar/finalizarCompra"
                        id="formularioCompra">

                        <input type="hidden" name="cep" id="cepCompra">
                        <input type="hidden" name="cidade" id="cidadeCompra">
                        <input type="hidden" name="uf" id="ufCompra">
                        <input type="hidden" name="frete" id="freteCompra">

                        <button
                            type="submit"
                            class="btn botaoCovil"
                            id="confirmarCompra"
                            disabled>
                            Confirmar compra
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /* Pega os elementos da página. */
    const campoCep = document.getElementById("cepFrete");
    const botaoFrete = document.getElementById("calcularFrete");
    const mensagemFrete = document.getElementById("mensagemFrete");
    const enderecoFrete = document.getElementById("enderecoFrete");
    const valorFrete = document.getElementById("valorFrete");
    const valorTotal = document.getElementById("valorTotal");
    const confirmarCompra = document.getElementById("confirmarCompra");
    const cepCompra = document.getElementById("cepCompra");
    const cidadeCompra = document.getElementById("cidadeCompra");
    const ufCompra = document.getElementById("ufCompra");
    const freteCompra = document.getElementById("freteCompra");
    const totalProdutos = <?= json_encode($totalCompra) ?>;

    /* Formata os valores para reais. */
    function formatarMoeda(valor) {
        return valor.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });
    }

    /* Limpa os dados do frete quando o CEP muda. */
    function limparFrete() {
        confirmarCompra.disabled = true;
        cepCompra.value = "";
        cidadeCompra.value = "";
        ufCompra.value = "";
        freteCompra.value = "";
        mensagemFrete.textContent = "";
        enderecoFrete.textContent = "";
        valorFrete.textContent = "R$ 0,00";
        valorTotal.textContent = formatarMoeda(totalProdutos);
    }

    /* Aplica a máscara do CEP enquanto o usuário digita. */
    campoCep.addEventListener("input", function() {
        let valor = this.value.replace(/\D/g, "").slice(0, 8);

        if (valor.length > 5) {
            valor = valor.substring(0, 5) + "-" + valor.substring(5);
        }

        this.value = valor;
        limparFrete();
    });

    /* Solicita o cálculo do frete. */
    botaoFrete.addEventListener("click", async function() {
        const cep = campoCep.value.replace(/\D/g, "");

        /* Verifica se o CEP possui 8 números. */
        if (cep.length !== 8) {
            mensagemFrete.textContent = "Informe um CEP válido.";
            enderecoFrete.textContent = "";
            confirmarCompra.disabled = true;
            return;
        }

        botaoFrete.disabled = true;
        confirmarCompra.disabled = true;
        mensagemFrete.textContent = "Calculando frete...";
        enderecoFrete.textContent = "";

        /* Monta os dados enviados para a API de frete. */
        const dados = new FormData();
        dados.append("cep", cep);

        try {
            /* Envia o CEP para o processamento do frete. */
            const resposta = await fetch("<?= $baseUrl ?>/index.php?param=salvar/frete", {
                method: "POST",
                body: dados
            });

            /* Converte a resposta para JSON. */
            const resultado = await resposta.json();

            /* Verifica se o cálculo do frete retornou erro. */
            if (!resultado.sucesso) {
                mensagemFrete.textContent = resultado.mensagem;
                valorFrete.textContent = "R$ 0,00";
                valorTotal.textContent = formatarMoeda(totalProdutos);
                confirmarCompra.disabled = true;
                return;
            }

            /* Calcula o valor total com o frete. */
            const frete = Number(resultado.frete);
            const total = totalProdutos + frete;

            mensagemFrete.textContent = "Frete calculado com sucesso.";

            /* Mostra o endereço retornado pelo cálculo. */
            enderecoFrete.textContent =
                `${resultado.cidade} - ${resultado.uf} | Região: ${resultado.regiao}`;

            /* Atualiza os valores da compra. */
            valorFrete.textContent = formatarMoeda(frete);
            valorTotal.textContent = formatarMoeda(total);

            /* Guarda os dados para enviar na confirmação da compra. */
            cepCompra.value = resultado.cep.replace(/\D/g, "");
            cidadeCompra.value = resultado.cidade;
            ufCompra.value = resultado.uf;
            freteCompra.value = frete;

            /* Libera a confirmação da compra. */
            confirmarCompra.disabled = false;

        } catch (erro) {
            console.error("Erro ao calcular frete:", erro);
            mensagemFrete.textContent = "Erro ao calcular o frete. Veja o console.";
            valorFrete.textContent = "R$ 0,00";
            valorTotal.textContent = formatarMoeda(totalProdutos);
            confirmarCompra.disabled = true;
        } finally {
            /* Libera o botão para tentar calcular novamente. */
            botaoFrete.disabled = false;
        }
    });
</script>