<?php

ob_start();

session_start();

require(__DIR__ . "/config.php");

$erroLogin = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");

    if ($email === "" || $senha === "") {

        $erroLogin = "Preencha o e-mail e a senha.";

    } else {

        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":email" => $email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario["senha"])) {

            $_SESSION["fogoEsangue"] = $usuario["id"];
            $_SESSION["tipo"] = $usuario["tipo"];

            header("Location: index.php");
            exit;

        } else {

            $erroLogin = "E-mail ou senha incorretos.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <!-- Define os caracteres utilizados na página -->
    <meta charset="UTF-8">

    <!-- Faz a página se adaptar a diferentes tamanhos de tela -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Define o título da página -->
    <title>Covil do Dragão</title>

    <!-- Define o ícone da aba do navegador -->
    <link rel="icon" href="IMG/logo.png">

    <!-- Abre o Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Abre o seu CSS -->
    <link rel="stylesheet" href="CSS/style.css">

    <!-- Abre o CSS do AOS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">

    <!-- Abre o Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Abre a fonte Cinzel -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Permite conexão antecipada com o Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <!-- Permite conexão com o servidor das fontes -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Abre as outras fontes utilizadas -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Pliant:ital,wght@0,100..900;1,100..900&family=Rowdies:wght@300;400;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- Abre o jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Abre o Parsley para validação dos formulários -->
    <script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.2/dist/parsley.min.js"></script>

    <!-- Abre o Inputmask para máscaras -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>

    <!-- Abre o SweetAlert2 para mensagens -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<?php

if (!isset($_SESSION["fogoEsangue"])) {

    require(__DIR__ . "/ADMIN/pages/login.php");

} else {

?>

<nav class="navbar navbar-expand-lg">

    <div class="container navbarConteudo">

        <!-- LOGO CENTRAL -->

        <div class="logoCaixa"
             data-aos="fade-down"
             data-aos-easing="linear"
             data-aos-duration="1500">

            <a href="home">
                <img class="logoNavBar" src="IMG/logo.png" alt="Covil do Dragão">
            </a>

        </div>

        <!-- BOTÃO MOBILE -->

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuNavbar"
                aria-controls="menuNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="menuNavbar">

            <!-- MENU ESQUERDO -->

            <div class="menuEsquerdo">

                <ul class="navbar-nav mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="home">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="community">Community</a>
                    </li>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                           href="#"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">

                            Loja

                        </a>

                        <ul class="dropdown-menu">

                            <li>
                                <a class="dropdown-item" href="cranios">
                                    Funko Pop
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="filhotes">
                                    Filhotes
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="estatuas">
                                    Estatuas
                                </a>
                            </li>

                        </ul>

                    </li>

                </ul>

            </div>

            <!-- MENU DIREITO -->

            <div class="menuDireito ms-auto">

                <?php if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] === "admin") { ?>

                    <ul class="navbar-nav mb-2 mb-lg-0 menuAdmin">

                        <li class="nav-item">
                            <a class="nav-link" href="colab">
                                Colab
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="produtos">
                                Produtos
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="usuarios">
                                Usuários
                            </a>
                        </li>

                    </ul>

                <?php } ?>

            </div>

        </div>

        <!-- ÍCONES -->

        <div class="loginCompra">

            <i class="fa-solid fa-cart-shopping"></i>

            <a href="sair.php">
                <i class="fa-solid fa-circle-user"></i>
            </a>

        </div>

    </div>

</nav>

<main>

<?php

$param = trim($_GET["param"] ?? "home", "/");

$partes = explode("/", $param);

$rota = $partes[0] ?? "home";
$subrota = $partes[1] ?? null;
$id = $partes[2] ?? null;

/*
 * Define a variável $pagina.
 * Os arquivos internos utilizam essa variável
 * para verificar se foram chamados pelo sistema.
 */

$pagina = $rota;

/*
 * Define as rotas que somente administradores podem acessar.
 */

$rotasAdmin = [
    "usuarios",
    "cadastrar",
    "listar",
    "salvar"
];

/*
 * Verifica se a rota exige administrador.
 */

if (in_array($rota, $rotasAdmin) && $_SESSION["tipo"] !== "admin") {

    include __DIR__ . "/ADMIN/pages/err.php";

} else {

    /*
     * Página principal de usuários.
     */

    if ($rota === "usuarios") {

        $arquivo = __DIR__ . "/ADMIN/pages/usuario.php";

    /*
     * Cadastro de usuário.
     */

    } elseif ($rota === "cadastrar" && $subrota === "usuario") {

        $arquivo = __DIR__ . "/ADMIN/cadastrar/usuario.php";

    /*
     * Listagem de usuário.
     */

    } elseif ($rota === "listar" && $subrota === "usuario") {

        $arquivo = __DIR__ . "/ADMIN/listar/usuario.php";

    /*
     * Salvamento de usuário.
     */

    } elseif ($rota === "salvar" && $subrota === "usuario") {

        $arquivo = __DIR__ . "/ADMIN/salvar/usuario.php";

    /*
     * Outras páginas do sistema.
     */

    } else {

        $arquivo = __DIR__ . "/ADMIN/pages/{$rota}.php";
    }

    /*
     * Verifica se o arquivo existe.
     */

    if (file_exists($arquivo)) {

        include $arquivo;

    } else {

        include __DIR__ . "/ADMIN/pages/err.php";
    }
}

?>

</main>

<footer class="footer">

    <div class="redes-sociais">

        <a href="https://www.instagram.com/hdutra.arts/" target="_blank">
            <i class="fa-brands fa-instagram icone-social"></i>
        </a>

        <a href="https://www.facebook.com/suapagina" target="_blank">
            <i class="fa-brands fa-tiktok"></i>
        </a>

        <a href="https://www.instagram.com/brunoaavt/" target="_blank">
            <i class="fa-solid fa-computer icone-social"></i>
        </a>

    </div>

</footer>

<?php

}

?>

<!-- Abre o JavaScript do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Abre o JavaScript do AOS -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

<!-- Inicializa o AOS -->
<script>
    AOS.init();
</script>

</body>
</html>

<?php

ob_end_flush();

?>