<?php

/* Verifica se o arquivo foi chamado pelo sistema */
if (!isset($pagina)) {
    exit;
}

/* Carrega as funções do sistema */
require_once(__DIR__ . "/../functions.php");

/* Verifica se o formulário foi enviado */
if ($_POST) {

    /* Recupera os dados enviados pelo formulário */
    $id = trim($_POST["id"] ?? "");
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");
    $cpf = trim($_POST["cpf"] ?? "");
    $datanascimento = trim($_POST["datanascimento"] ?? "");
    $ativo = trim($_POST["ativo"] ?? "");


    /* ================================
       VERIFICAR NOME
       ================================ */

    if (empty($nome)) {

        mensagem("Erro", "Preencha o nome", "error");
        exit;
    }


    /* ================================
       VERIFICAR E-MAIL
       ================================ */

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        mensagem("Erro", "Digite um e-mail válido", "error");
        exit;
    }


    /* ================================
       VERIFICAR CPF
       ================================ */

    if (empty($cpf)) {

        mensagem("Erro", "Preencha o CPF", "error");
        exit;
    }

    /* Remove pontos e traço */
    $cpfLimpo = preg_replace("/\D/", "", $cpf);

    /* Verifica o CPF através da função do functions.php */
    if (!validarCPF($cpfLimpo)) {

        mensagem("Erro", "CPF inválido", "error");
        exit;
    }


    /* ================================
       VERIFICAR DATA DE NASCIMENTO
       ================================ */

    if (empty($datanascimento)) {

        mensagem("Erro", "Preencha a data de nascimento", "error");
        exit;
    }

    /* Converte a data para DateTime */
    $data = DateTime::createFromFormat("d/m/Y", $datanascimento);

    /* Verifica os possíveis erros */
    $erros = DateTime::getLastErrors();

    if (
        !$data ||
        ($erros !== false && (
            $erros["warning_count"] > 0 ||
            $erros["error_count"] > 0
        )) ||
        $data->format("d/m/Y") !== $datanascimento
    ) {

        mensagem("Erro", "Digite uma data de nascimento válida", "error");
        exit;
    }

    /* Verifica se a data não está no futuro */
    $hoje = new DateTime();

    if ($data > $hoje) {

        mensagem("Erro", "A data de nascimento não pode ser futura", "error");
        exit;
    }

    /* Converte para o formato aceito pelo MySQL */
    $datanascimento = $data->format("Y-m-d");


    /* ================================
       VERIFICAR ATIVO
       ================================ */

    if (empty($ativo)) {

        mensagem("Erro", "Selecione se o usuário está ativo", "error");
        exit;
    }


    /* ================================
       NOVO USUÁRIO
       ================================ */

    if (empty($id)) {

        /* Senha obrigatória no cadastro */
        if (empty($senha)) {

            mensagem("Erro", "Digite uma senha", "error");
            exit;
        }

        /* Criptografa a senha */
        $senha = password_hash($senha, PASSWORD_BCRYPT);

        /*
         * Todo usuário criado pelo site
         * será cadastrado como usuario.
         *
         * O tipo não vem do formulário.
         */
        $sql = "INSERT INTO usuarios
                (nome, email, senha, cpf, datanascimento, ativo, tipo)
                VALUES
                (:nome, :email, :senha, :cpf, :datanascimento, :ativo, 'usuario')";

        $consulta = $pdo->prepare($sql);

        $consulta->bindParam(":nome", $nome);
        $consulta->bindParam(":email", $email);
        $consulta->bindParam(":senha", $senha);
        $consulta->bindParam(":cpf", $cpfLimpo);
        $consulta->bindParam(":datanascimento", $datanascimento);
        $consulta->bindParam(":ativo", $ativo);


    } else {

        /* ================================
           EDITAR USUÁRIO
           ================================ */

        /*
         * O tipo NÃO é alterado aqui.
         * Ele continuará exatamente como está no banco.
         */

        if (empty($senha)) {

            /* Edita sem alterar a senha */

            $sql = "UPDATE usuarios SET
                    nome = :nome,
                    email = :email,
                    cpf = :cpf,
                    datanascimento = :datanascimento,
                    ativo = :ativo
                    WHERE id = :id";

            $consulta = $pdo->prepare($sql);

            $consulta->bindParam(":nome", $nome);
            $consulta->bindParam(":email", $email);
            $consulta->bindParam(":cpf", $cpfLimpo);
            $consulta->bindParam(":datanascimento", $datanascimento);
            $consulta->bindParam(":ativo", $ativo);
            $consulta->bindParam(":id", $id);


        } else {

            /* Edita alterando a senha */

            $senha = password_hash($senha, PASSWORD_BCRYPT);

            $sql = "UPDATE usuarios SET
                    nome = :nome,
                    email = :email,
                    senha = :senha,
                    cpf = :cpf,
                    datanascimento = :datanascimento,
                    ativo = :ativo
                    WHERE id = :id";

            $consulta = $pdo->prepare($sql);

            $consulta->bindParam(":nome", $nome);
            $consulta->bindParam(":email", $email);
            $consulta->bindParam(":senha", $senha);
            $consulta->bindParam(":cpf", $cpfLimpo);
            $consulta->bindParam(":datanascimento", $datanascimento);
            $consulta->bindParam(":ativo", $ativo);
            $consulta->bindParam(":id", $id);
        }
    }


    /* ================================
       SALVAR NO BANCO
       ================================ */

    try {

        $consulta->execute();

        /* Volta para a página de usuários */
        header("Location: index.php?param=usuarios");
        exit;

    } catch (PDOException $e) {

        /* Verifica se o e-mail já existe */
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {

            mensagem(
                "Erro",
                "Este e-mail já está cadastrado",
                "error"
            );

        } else {

            mensagem(
                "Erro",
                "Não foi possível salvar o usuário",
                "error"
            );
        }

        exit;
    }

} else {

    /* Caso alguém tente acessar a rota sem enviar o formulário */
    mensagem(
        "Erro",
        "Requisição inválida",
        "error"
    );

    exit;
}

?>