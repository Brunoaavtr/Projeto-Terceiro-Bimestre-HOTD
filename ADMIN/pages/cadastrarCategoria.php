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
                <h1>Cadastrar categoria</h1>
                <p>Cadastre uma nova categoria para os produtos do Covil do Dragão</p>
            </div>

            <div class="card-body p-4">
                <!-- Formulário para cadastrar a categoria. -->
                <form method="post" action="index.php?param=salvar/categoria">
                    <div class="mb-4">
                        <div class="tituloCampoMarca">Nome da categoria:</div>
                        <input type="text" name="nome" id="nome" class="form-control" placeholder="Digite o nome da categoria" maxlength="100" required>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Descrição da categoria:</div>
                        <textarea name="descricao" id="descricao" class="form-control campoDescricaoCategoria" placeholder="Digite uma descrição para a categoria" maxlength="255" rows="5"></textarea>
                        <div class="form-text">A descrição é opcional e pode ter até 255 caracteres.</div>
                    </div>

                    <div class="cadastroMarcaBotoes">
                        <!-- Volta para a lista de categorias. -->
                        <a href="categorias" class="btn botaoCovil">Cancelar</a>

                        <!-- Envia os dados para cadastro. -->
                        <button type="submit" class="btn botaoCovil">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>