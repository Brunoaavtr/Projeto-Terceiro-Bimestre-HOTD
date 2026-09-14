<?php

/* Verifica se a conexão com o banco está disponível. */
if (!isset($pdo)) {
    exit;
}

/* Busca os estados cadastrados. */
$sqlEstados = "SELECT ID_ESTADO, NM_ESTADO, SG_ESTADO
               FROM estado
               ORDER BY NM_ESTADO";

/* Executa a consulta dos estados. */
$consultaEstados = $pdo->query($sqlEstados);

/* Guarda os estados encontrados. */
$estados = $consultaEstados->fetchAll(PDO::FETCH_ASSOC);

/* Busca as cidades cadastradas. */
$sqlCidades = "SELECT ID_CIDADE, NM_CIDADE, ID_ESTADO
               FROM cidade
               ORDER BY NM_CIDADE";

/* Executa a consulta das cidades. */
$consultaCidades = $pdo->query($sqlCidades);

/* Guarda as cidades encontradas. */
$cidades = $consultaCidades->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="cadastro">
        <div class="card shadow">
            <div class="card-header text-center">
                <img src="IMG/logo.png" alt="Covil do Dragão">
                <h1>CRIAR CONTA</h1>
                <p>Preencha seus dados para entrar no Covil.</p>
            </div>

            <div class="card-body p-4">
                <!-- Formulário para cadastrar o usuário. -->
                <form name="formCadastro" method="post" action="index.php?param=salvar/cadastro" data-parsley-validate>
                    <h3>Dados pessoais</h3>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome">Nome:</label>
                            <input type="text" name="nome" id="nome" class="form-control" maxlength="100" required autocomplete="name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email">E-mail:</label>
                            <input type="email" name="email" id="email" class="form-control" maxlength="150" required autocomplete="email">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="senha">Senha:</label>
                            <input type="password" name="senha" id="senha" class="form-control" required autocomplete="new-password">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="senha2">Confirmar senha:</label>
                            <input type="password" name="senha2" id="senha2" class="form-control" required data-parsley-equalto="#senha" autocomplete="new-password">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cpf">CPF:</label>
                            <input type="text" name="cpf" id="cpf" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="datanascimento">Data de nascimento:</label>
                            <input type="text" name="datanascimento" id="datanascimento" class="form-control" placeholder="dd/mm/aaaa" required>
                        </div>
                    </div>

                    <h3 class="mt-4">Endereço</h3>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estado">Estado:</label>
                            <select name="estado" id="estado" class="form-select" required>
                                <option value="">Selecione o estado</option>

                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= $estado["ID_ESTADO"] ?>">
                                        <?= htmlspecialchars($estado["NM_ESTADO"]) ?> - <?= htmlspecialchars($estado["SG_ESTADO"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cidade">Cidade:</label>
                            <select name="cidade" id="cidade" class="form-select" required>
                                <option value="">Selecione primeiro o estado</option>

                                <?php foreach ($cidades as $cidade): ?>
                                    <option value="<?= $cidade["ID_CIDADE"] ?>" data-estado="<?= $cidade["ID_ESTADO"] ?>">
                                        <?= htmlspecialchars($cidade["NM_CIDADE"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="logradouro">Logradouro:</label>
                            <input type="text" name="logradouro" id="logradouro" class="form-control" maxlength="150" placeholder="Rua, Avenida, etc." required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="numero">Número:</label>
                            <input type="text" name="numero" id="numero" class="form-control" maxlength="20" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="bairro">Bairro:</label>
                            <input type="text" name="bairro" id="bairro" class="form-control" maxlength="100" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="complemento">Complemento:</label>
                            <input type="text" name="complemento" id="complemento" class="form-control" maxlength="100" placeholder="Apartamento, bloco, casa...">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cep">CEP:</label>
                            <input type="text" name="cep" id="cep" class="form-control" placeholder="00000-000" required>
                        </div>
                    </div>

                    <h3 class="mt-4">Contato</h3>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefone">Telefone:</label>
                            <input type="text" name="telefone" id="telefone" class="form-control" placeholder="(00) 00000-0000" required>
                        </div>
                    </div>

                    <div class="btnLogin mt-4">
                        <!-- Volta para a página de login. -->
                        <a href="login" class="btn">Voltar</a>

                        <!-- Envia os dados para cadastro. -->
                        <button type="submit" class="btn">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    /* Aplica a máscara no CPF. */
    $("#cpf").inputmask("999.999.999-99");

    /* Aplica a máscara na data de nascimento. */
    $("#datanascimento").inputmask("99/99/9999");

    /* Aplica a máscara no CEP. */
    $("#cep").inputmask("99999-999");

    /* Aplica a máscara no telefone. */
    $("#telefone").inputmask({
        mask: ["(99) 99999-9999", "(99) 9999-9999"],
        keepStatic: true
    });

    /* Guarda todas as cidades para filtrar depois. */
    const todasCidades = $("#cidade option").clone();

    /* Filtra as cidades de acordo com o estado escolhido. */
    $("#estado").on("change", function() {
        const estadoSelecionado = $(this).val();

        /* Limpa as cidades atuais. */
        $("#cidade").empty();

        /* Mostra a mensagem inicial quando nenhum estado foi escolhido. */
        if (estadoSelecionado === "") {
            $("#cidade").append('<option value="">Selecione primeiro o estado</option>');
            return;
        }

        /* Adiciona a opção para selecionar a cidade. */
        $("#cidade").append('<option value="">Selecione a cidade</option>');

        /* Percorre as cidades e mostra apenas as do estado escolhido. */
        todasCidades.each(function() {
            const cidade = $(this);
            const estadoCidade = cidade.data("estado");

            if (estadoCidade == estadoSelecionado) {
                $("#cidade").append(cidade.clone());
            }
        });
    });
});
</script>