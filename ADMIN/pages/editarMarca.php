<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Pega o ID da marca pela URL. */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: marcas");
    exit;
}

/* Busca os dados da marca. */
$sql = "SELECT ID_MARCA, NM_MARCA, DS_IMAGEM
        FROM marca
        WHERE ID_MARCA = :id
        LIMIT 1";

/* Executa a consulta da marca. */
$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

/* Guarda os dados encontrados. */
$marca = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$marca) {
    header("Location: marcas");
    exit;
}
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Editar marca</h1>
                <p>Altere as informações da marca cadastrada</p>
            </div>

            <div class="card-body p-4">
                <!-- Formulário para editar a marca. -->
                <form method="post" action="index.php?param=salvar/editarMarca" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= (int)$marca["ID_MARCA"] ?>">

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Nome da marca:</div>
                        <input
                            type="text"
                            name="nome"
                            id="nome"
                            class="form-control"
                            value="<?= htmlspecialchars($marca["NM_MARCA"]) ?>"
                            placeholder="Digite o nome da marca"
                            required>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Imagem da marca:</div>

                        <?php if (!empty($marca["DS_IMAGEM"])): ?>
                            <div class="marcaImagem mb-3">
                                <img
                                    src="<?= htmlspecialchars($marca["DS_IMAGEM"]) ?>"
                                    alt="Logo da marca <?= htmlspecialchars($marca["NM_MARCA"]) ?>">
                            </div>
                        <?php else: ?>
                            <div class="marcaImagem mb-3">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        <?php endif; ?>

                        <div class="campoArquivo">
                            <label for="imagem" class="botaoArquivo">
                                <i class="fa-solid fa-image"></i>
                                Escolher imagem
                            </label>

                            <span class="nomeArquivo" id="nomeArquivo">
                                Nenhuma imagem escolhida
                            </span>

                            <input
                                type="file"
                                name="imagem"
                                id="imagem"
                                accept=".jpg,.jpeg,.png,.webp">
                        </div>

                        <div class="form-text">
                            Escolha uma nova imagem somente se quiser substituir a imagem atual.
                            Formatos permitidos: JPG, JPEG, PNG e WEBP. Tamanho máximo: 5 MB.
                        </div>
                    </div>

                    <div class="cadastroMarcaBotoes">
                        <!-- Volta para a lista de marcas. -->
                        <a href="marcas" class="btn botaoCovil">Cancelar</a>

                        <!-- Salva as alterações da marca. -->
                        <button type="submit" class="btn botaoCovil">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    /* Pega os elementos do formulário. */
    const campoImagem = document.getElementById("imagem");
    const nomeArquivo = document.getElementById("nomeArquivo");

    /* Mostra o nome da imagem escolhida. */
    campoImagem.addEventListener("change", function() {
        if (this.files.length > 0) {
            nomeArquivo.textContent = this.files[0].name;
        } else {
            nomeArquivo.textContent = "Nenhuma imagem escolhida";
        }
    });
</script>