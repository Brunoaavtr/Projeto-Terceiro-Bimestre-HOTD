<?php

/* Verifica se a página foi chamada pelo sistema */
if (!isset($pagina)) {
    exit;
}

/* Permite acesso somente para administradores */
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "admin") {
    include __DIR__ . "/../pages/err.php";
    exit;
}

/* Verifica se estamos editando um usuário */
if (!empty($id)) {

    /* Busca os dados do usuário */
    $sql = "SELECT *,
                   DATE_FORMAT(datanascimento, '%d/%m/%Y') AS datanascimento
            FROM usuarios
            WHERE id = :id
            LIMIT 1";

    $consulta = $pdo->prepare($sql);
    $consulta->bindParam(":id", $id);
    $consulta->execute();

    $dadosUsuario = $consulta->fetch(PDO::FETCH_OBJ);
}

/* Recupera os dados */
$id = $dadosUsuario->id ?? NULL;
$nome = $dadosUsuario->nome ?? NULL;
$email = $dadosUsuario->email ?? NULL;
$datanascimento = $dadosUsuario->datanascimento ?? NULL;
$ativo = $dadosUsuario->ativo ?? NULL;
$cpf = $dadosUsuario->cpf ?? NULL;

/* Define se a senha será obrigatória */
$senhaObrigatoria = empty($id) ? "required" : "";

?>

<div class="card shadow m-5">

    <div class="card-header">

        <div class="float-start">
            <h2>Cadastro de Usuário</h2>
        </div>

        <div class="float-end">

            <!-- Abre um novo cadastro -->
            <a href="index.php?param=cadastrar/usuario" class="btn btn-success">
                Novo Registro
            </a>

            <!-- Volta para a lista -->
            <a href="index.php?param=usuarios" class="btn btn-primary">
                Listar Registros
            </a>

        </div>

    </div>

    <div class="card-body">

        <!-- Formulário -->
        <form name="formCadastro"
              method="post"
              action="index.php?param=salvar/usuario"
              data-parsley-validate>

            <div class="row">

                <!-- Nome -->
                <div class="col-12 col-md-6">

                    <label for="nome">Nome do Usuário:</label>

                    <input type="text"
                           name="nome"
                           id="nome"
                           class="form-control"
                           value="<?= htmlspecialchars($nome ?? "") ?>"
                           required
                           data-parsley-required-message="Preencha o nome">

                </div>

                <!-- E-mail -->
                <div class="col-12 col-md-6">

                    <label for="email">Digite o e-mail:</label>

                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control"
                           value="<?= htmlspecialchars($email ?? "") ?>"
                           required
                           data-parsley-required-message="Preencha o e-mail"
                           data-parsley-type-message="Digite um e-mail válido">

                </div>

                <!-- Senha -->
                <div class="col-12 col-md-6">

                    <label for="senha">Digite uma Senha:</label>

                    <input type="password"
                           name="senha"
                           id="senha"
                           class="form-control"
                           <?= $senhaObrigatoria ?>
                           data-parsley-required-message="Digite uma senha">

                </div>

                <!-- Confirmação da senha -->
                <div class="col-12 col-md-6">

                    <label for="senha2">Redigite a Senha:</label>

                    <input type="password"
                           name="senha2"
                           id="senha2"
                           class="form-control"
                           <?= $senhaObrigatoria ?>
                           data-parsley-equalto="#senha"
                           data-parsley-equalto-message="As senhas não são iguais">

                </div>

                <!-- CPF -->
                <div class="col-12 col-md-6">

                    <label for="cpf">CPF:</label>

                    <input type="text"
                           name="cpf"
                           id="cpf"
                           class="form-control"
                           value="<?= htmlspecialchars($cpf ?? "") ?>"
                           maxlength="14"
                           inputmode="numeric"
                           required
                           data-parsley-required-message="Preencha o CPF">

                </div>

                <!-- Data de nascimento -->
                <div class="col-12 col-md-6">

                    <label for="datanascimento">Data de Nascimento:</label>

                    <input type="text"
                           name="datanascimento"
                           id="datanascimento"
                           class="form-control"
                           value="<?= htmlspecialchars($datanascimento ?? "") ?>"
                           maxlength="10"
                           inputmode="numeric"
                           required
                           data-parsley-required-message="Preencha a data de nascimento">

                </div>

                <!-- Ativo -->
                <div class="col-12 col-md-6">

                    <label for="ativo">Ativo:</label>

                    <select name="ativo"
                            id="ativo"
                            class="form-control"
                            required
                            data-parsley-required-message="Selecione uma opção">

                        <option value=""></option>
                        <option value="Sim">Sim</option>
                        <option value="Não">Não</option>

                    </select>

                </div>

            </div>

            <br>

            <!-- Botão salvar -->
            <button type="submit" class="btn btn-success float-end">
                Gravar Dados
            </button>

        </form>

    </div>

</div>

<script>

$(document).ready(function() {

    /* Mantém o valor do campo Ativo */
    $("#ativo").val("<?= $ativo ?>");


    /* ==============================
       MÁSCARA DO CPF
       ============================== */

    $("#cpf").on("input", function() {

        let valor = $(this).val().replace(/\D/g, "");

        valor = valor.substring(0, 11);

        if (valor.length > 9) {

            valor = valor.replace(
                /^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/,
                "$1.$2.$3-$4"
            );

        } else if (valor.length > 6) {

            valor = valor.replace(
                /^(\d{3})(\d{3})(\d{0,3}).*/,
                "$1.$2.$3"
            );

        } else if (valor.length > 3) {

            valor = valor.replace(
                /^(\d{3})(\d{0,3}).*/,
                "$1.$2"
            );
        }

        $(this).val(valor);

    });


    /* ==============================
       MÁSCARA DA DATA
       ============================== */

    $("#datanascimento").on("input", function() {

        let valor = $(this).val().replace(/\D/g, "");

        valor = valor.substring(0, 8);

        if (valor.length > 4) {

            valor = valor.replace(
                /^(\d{2})(\d{2})(\d{0,4}).*/,
                "$1/$2/$3"
            );

        } else if (valor.length > 2) {

            valor = valor.replace(
                /^(\d{2})(\d{0,2}).*/,
                "$1/$2"
            );
        }

        $(this).val(valor);

    });

});

</script>