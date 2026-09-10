<?php

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

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Covil do Dragão</title>

    <link rel="icon" href="IMG/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="CSS/style.css">

    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Pliant:ital,wght@0,100..900;1,100..900&family=Rowdies:wght@300;400;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

</head>

<body>

<?php

if (!isset($_SESSION["fogoEsangue"])) {

    require(__DIR__ . "/ADMIN/pages/login.php");

} else {

?>

<nav class="navbar navbar-expand-lg">

    <div class="container navbarConteudo">

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

                    <a class="nav-link dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Loja
                    </a>

                    <ul class="dropdown-menu">

                        <li>
                            <a class="dropdown-item" href="cranios">Funko Pop</a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="filhotes">Filhotes</a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="estatuas">Estatuas</a>
                        </li>

                    </ul>

                </li>

            </ul>

        </div>


        <!-- LOGO CENTRAL -->
        <div class="logoCaixa"
            data-aos="fade-down"
            data-aos-easing="linear"
            data-aos-duration="1500">

            <img class="logoNavBar" src="IMG/logo.png" alt="Covil do Dragão">

        </div>


        <!-- MENU DIREITO -->
        <div class="menuDireito">

            <?php if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] === "admin") { ?>

                <ul class="navbar-nav mb-2 mb-lg-0 menuAdmin">

                    <li class="nav-item">
                        <a class="nav-link" href="colab">Colab</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="produtos">Produtos</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="usuarios">Usuários</a>
                    </li>

                </ul>

            <?php } ?>

        </div>


        <!-- ÍCONES -->
        <div class="loginCompra">

            <i class="fa-solid fa-cart-shopping"></i>

            <i class="fa-solid fa-circle-user"></i>

        </div>


        <!-- BOTÃO MOBILE -->
        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuNavbar"
            aria-controls="menuNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

    </div>

</nav>

<main>

    <?php

    $param = $_GET["param"] ?? "home";
    $param = explode("/", $param);

    $pagina = $param[0] ?? "home";
    $id = $param[1] ?? null;

    $arquivo = __DIR__ . "/ADMIN/pages/{$pagina}.php";

    if (file_exists($arquivo)) {

        include $arquivo;

    } else {

        include __DIR__ . "/ADMIN/pages/err.php";

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

<script>
    AOS.init();
</script>

</body>
</html>