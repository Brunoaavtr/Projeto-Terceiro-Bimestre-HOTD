<?php

/* Verifica se a página foi acessada pelo sistema. */
if (!isset($pagina)) {
    exit;
}

/* Carrega as funções utilizadas no cadastro. */
require_once(__DIR__ . "/../functions.php");

/* Verifica se o formulário foi enviado por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    mensagem("Erro", "Requisição inválida.", "error");
    exit;
}

/* Pega os dados enviados pelo formulário. */
$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";
$senha2 = $_POST["senha2"] ?? "";
$cpf = trim($_POST["cpf"] ?? "");
$datanascimento = trim($_POST["datanascimento"] ?? "");
$cep = trim($_POST["cep"] ?? "");
$estado = trim($_POST["estado"] ?? "");
$cidade = trim($_POST["cidade"] ?? "");
$logradouro = trim($_POST["logradouro"] ?? "");
$numero = trim($_POST["numero"] ?? "");
$bairro = trim($_POST["bairro"] ?? "");
$complemento = trim($_POST["complemento"] ?? "");
$telefone = trim($_POST["telefone"] ?? "");

/* Valida o nome informado. */
if ($nome === "") {
    mensagem("Erro", "Preencha o nome.", "error");
    exit;
}

/* Valida o e-mail informado. */
if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    mensagem("Erro", "Digite um e-mail válido.", "error");
    exit;
}

/* Verifica a senha. */
if ($senha === "") {
    mensagem("Erro", "Digite uma senha.", "error");
    exit;
}

/* Verifica a confirmação da senha. */
if ($senha2 === "") {
    mensagem("Erro", "Confirme a senha.", "error");
    exit;
}

/* Compara as duas senhas. */
if ($senha !== $senha2) {
    mensagem("Erro", "As senhas não são iguais.", "error");
    exit;
}

/* Valida o CPF informado. */
if ($cpf === "") {
    mensagem("Erro", "Preencha o CPF.", "error");
    exit;
}

$cpfLimpo = preg_replace("/\D/", "", $cpf);

if (!validarCPF($cpfLimpo)) {
    mensagem("Erro", "CPF inválido.", "error");
    exit;
}

/* Valida a data de nascimento. */
if ($datanascimento === "") {
    mensagem("Erro", "Preencha a data de nascimento.", "error");
    exit;
}

$data = DateTime::createFromFormat("d/m/Y", $datanascimento);
$errosData = DateTime::getLastErrors();

if (
    !$data ||
    (
        $errosData !== false &&
        (
            $errosData["warning_count"] > 0 ||
            $errosData["error_count"] > 0
        )
    ) ||
    $data->format("d/m/Y") !== $datanascimento
) {
    mensagem("Erro", "Digite uma data de nascimento válida.", "error");
    exit;
}

$hoje = new DateTime();

if ($data > $hoje) {
    mensagem("Erro", "A data de nascimento não pode ser futura.", "error");
    exit;
}

$dataBanco = $data->format("Y-m-d");

/* Valida o CEP informado. */
if ($cep === "") {
    mensagem("Erro", "Preencha o CEP.", "error");
    exit;
}

$cepLimpo = preg_replace("/\D/", "", $cep);

if (strlen($cepLimpo) !== 8) {
    mensagem("Erro", "CEP inválido.", "error");
    exit;
}

/* Verifica o estado selecionado. */
if ($estado === "" || !filter_var($estado, FILTER_VALIDATE_INT)) {
    mensagem("Erro", "Selecione um estado.", "error");
    exit;
}

/* Verifica a cidade selecionada. */
if ($cidade === "" || !filter_var($cidade, FILTER_VALIDATE_INT)) {
    mensagem("Erro", "Selecione uma cidade.", "error");
    exit;
}

/* Valida o endereço informado. */
if ($logradouro === "") {
    mensagem("Erro", "Preencha o endereço.", "error");
    exit;
}

if ($numero === "") {
    mensagem("Erro", "Preencha o número.", "error");
    exit;
}

if ($bairro === "") {
    mensagem("Erro", "Preencha o bairro.", "error");
    exit;
}

/* Valida o telefone informado. */
if ($telefone === "") {
    mensagem("Erro", "Preencha o telefone.", "error");
    exit;
}

$telefoneLimpo = preg_replace("/\D/", "", $telefone);

if (strlen($telefoneLimpo) !== 10 && strlen($telefoneLimpo) !== 11) {
    mensagem("Erro", "Telefone inválido.", "error");
    exit;
}

/* Criptografa a senha antes de salvar. */
$senhaHash = password_hash($senha, PASSWORD_BCRYPT);

try {
    /* Inicia a transação do cadastro. */
    $pdo->beginTransaction();

    /* Verifica se o e-mail já está cadastrado. */
    $sqlEmail = "SELECT ID_USUARIO
                 FROM usuario
                 WHERE DS_EMAIL = :email
                 LIMIT 1";

    $consultaEmail = $pdo->prepare($sqlEmail);
    $consultaEmail->execute([":email" => $email]);

    if ($consultaEmail->fetch()) {
        $pdo->rollBack();
        mensagem("Erro", "Este e-mail já está cadastrado.", "error");
        exit;
    }

    /* Verifica se o CPF já está cadastrado. */
    $sqlCpf = "SELECT ID_USUARIO
               FROM usuario
               WHERE NR_CPF = :cpf
               LIMIT 1";

    $consultaCpf = $pdo->prepare($sqlCpf);
    $consultaCpf->execute([":cpf" => $cpfLimpo]);

    if ($consultaCpf->fetch()) {
        $pdo->rollBack();
        mensagem("Erro", "Este CPF já está cadastrado.", "error");
        exit;
    }

    /* Verifica se a cidade pertence ao estado selecionado. */
    $sqlCidade = "SELECT ID_CIDADE
                  FROM cidade
                  WHERE ID_CIDADE = :cidade
                  AND ID_ESTADO = :estado
                  LIMIT 1";

    $consultaCidade = $pdo->prepare($sqlCidade);
    $consultaCidade->execute([
        ":cidade" => $cidade,
        ":estado" => $estado
    ]);

    if (!$consultaCidade->fetch()) {
        $pdo->rollBack();
        mensagem("Erro", "A cidade selecionada não pertence ao estado escolhido.", "error");
        exit;
    }

    /* Cadastra os dados do usuário. */
    $sqlUsuario = "INSERT INTO usuario (
                       NM_USUARIO,
                       DS_EMAIL,
                       DS_SENHA,
                       NR_CPF,
                       DT_NASCIMENTO,
                       FL_ATIVO,
                       ID_TIPO_USUARIO
                   ) VALUES (
                       :nome,
                       :email,
                       :senha,
                       :cpf,
                       :datanascimento,
                       1,
                       1
                   )";

    $consultaUsuario = $pdo->prepare($sqlUsuario);
    $consultaUsuario->execute([
        ":nome" => $nome,
        ":email" => $email,
        ":senha" => $senhaHash,
        ":cpf" => $cpfLimpo,
        ":datanascimento" => $dataBanco
    ]);

    /* Pega o ID do usuário recém-cadastrado. */
    $idUsuario = $pdo->lastInsertId();

    /* Cadastra o endereço do usuário. */
    $sqlEndereco = "INSERT INTO endereco (
                        ID_USUARIO,
                        ID_CIDADE,
                        DS_LOGRADOURO,
                        NR_ENDERECO,
                        DS_BAIRRO,
                        DS_COMPLEMENTO,
                        NR_CEP
                    ) VALUES (
                        :idusuario,
                        :cidade,
                        :logradouro,
                        :numero,
                        :bairro,
                        :complemento,
                        :cep
                    )";

    $consultaEndereco = $pdo->prepare($sqlEndereco);
    $consultaEndereco->execute([
        ":idusuario" => $idUsuario,
        ":cidade" => $cidade,
        ":logradouro" => $logradouro,
        ":numero" => $numero,
        ":bairro" => $bairro,
        ":complemento" => $complemento,
        ":cep" => $cepLimpo
    ]);

    /* Cadastra o telefone do usuário. */
    $sqlTelefone = "INSERT INTO telefone (
                        ID_USUARIO,
                        NR_TELEFONE
                    ) VALUES (
                        :idusuario,
                        :telefone
                    )";

    $consultaTelefone = $pdo->prepare($sqlTelefone);
    $consultaTelefone->execute([
        ":idusuario" => $idUsuario,
        ":telefone" => $telefoneLimpo
    ]);

    /* Confirma todos os dados cadastrados. */
    $pdo->commit();
?>

    <script>
        /* Mostra a confirmação do cadastro. */
        Swal.fire({
            title: "Sucesso",
            text: "Cadastro realizado com sucesso! Agora você pode fazer login.",
            icon: "success"
        }).then(() => {
            window.location.href = "login";
        });
    </script>

<?php

} catch (PDOException $e) {
    /* Desfaz o cadastro caso aconteça algum erro. */
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    mensagem("Erro", "Não foi possível realizar o cadastro.", "error");
    exit;
}

?>