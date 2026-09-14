<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

/* Pega o ID da categoria pela URL. */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: categorias");
    exit;
}

/* Busca os dados da categoria. */
$sql = "SELECT ID_CATEGORIA, NM_CATEGORIA, DS_CATEGORIA
        FROM categoria
        WHERE ID_CATEGORIA = :id
        LIMIT 1";

/* Executa a consulta da categoria. */
$consulta = $pdo->prepare($sql);
$consulta->bindValue(":id", $id, PDO::PARAM_INT);
$consulta->execute();

/* Guarda os dados encontrados. */
$categoria = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    header("Location: categorias");
    exit;
}
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Editar categoria</h1>
                <p>Altere as informações da categoria cadastrada</p>
            </div>

            <div class="card-body p-4">
                <!-- Formulário para editar a categoria. -->
                <form method="post" action="index.php?param=salvar/editarCategoria">
                    <input type="hidden" name="id" value="<?= (int)$categoria["ID_CATEGORIA"] ?>">

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Nome da categoria:</div>
                        <input
                            type="text"
                            name="nome"
                            id="nome"
                            class="form-control"
                            value="<?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>"
                            placeholder="Digite o nome da categoria"
                            maxlength="100"
                            required>
                    </div>

                    <div class="mb-4">
                        <div class="tituloCampoMarca">Descrição da categoria:</div>
                        <textarea
                            name="descricao"
                            id="descricao"
                            class="form-control campoDescricaoCategoria"
                            placeholder="Digite uma descrição para a categoria"
                            maxlength="255"
                            rows="5"><?= htmlspecialchars($categoria["DS_CATEGORIA"] ?? "") ?></textarea>

                        <div class="form-text">
                            A descrição é opcional e pode ter até 255 caracteres.
                        </div>
                    </div>

                    <div class="cadastroMarcaBotoes">
                        <!-- Volta para a lista de categorias. -->
                        <a href="categorias" class="btn botaoCovil">Cancelar</a>

                        <!-- Salva as alterações da categoria. -->
                        <button type="submit" class="btn botaoCovil">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>