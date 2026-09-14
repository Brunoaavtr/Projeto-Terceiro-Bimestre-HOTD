<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Cadastrar marca</h1>
                <p>Cadastre uma nova marca parceira do Covil do Dragão</p>
            </div>

            <div class="card-body p-4">
                <!-- Formulário para cadastrar a marca. -->
                <form method="post" action="index.php?param=salvar/marca" enctype="multipart/form-data">
                    <div class="mb-4">
                        <div class="tituloCampoMarca">Nome da marca:</div>
                        <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome da marca" required>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Imagem da marca:</div>

                        <div class="campoArquivo">
                            <!-- Abre a seleção de arquivos. -->
                            <label for="imagem" class="botaoArquivo">
                                <i class="fa-solid fa-image"></i>
                                Escolher imagem
                            </label>

                            <span class="nomeArquivo" id="nomeArquivo">
                                Nenhuma imagem escolhida
                            </span>

                            <input type="file" name="imagem" id="imagem" accept=".jpg,.jpeg,.png,.webp" required>
                        </div>

                        <div class="form-text">
                            Formatos permitidos: JPG, JPEG, PNG e WEBP. Tamanho máximo: 5 MB.
                        </div>
                    </div>

                    <div class="cadastroMarcaBotoes">
                        <!-- Volta para a lista de marcas. -->
                        <a href="marcas" class="btn botaoCovil">Cancelar</a>

                        <!-- Envia os dados para cadastro. -->
                        <button type="submit" class="btn botaoCovil">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
/* Mostra o nome da imagem escolhida. */
const campoImagem = document.getElementById("imagem");
const nomeArquivo = document.getElementById("nomeArquivo");

/* Verifica quando uma imagem é selecionada. */
campoImagem.addEventListener("change", function() {
    if (this.files.length > 0) {
        nomeArquivo.textContent = this.files[0].name;
    } else {
        nomeArquivo.textContent = "Nenhuma imagem escolhida";
    }
});
</script>