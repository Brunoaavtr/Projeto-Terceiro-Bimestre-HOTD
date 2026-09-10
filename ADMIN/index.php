<?php
session_start();

require(__DIR__ . "/../../config.php");

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Covil do Dragão</title>
    <link rel="icon" href="IMG/logo.png">

    <!--Botstrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="../../CSS/style.css">
    <!-- CSS do AOS-->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Google fonte -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Pliant:ital,wght@0,100..900;1,100..900&family=Rowdies:wght@300;400;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">



</head>

<body>
    <?php
    //Verificando se esta logado e se esta sendo enviado dados
    //Verificando se esta logado - mostro a tela de login
    //se esta logado - mostrar tela inicial
    if ((!isset($_SESSION["fogoEsangue"])) && ($_POST)) {
        //Verifica se o usuario e senha são validos

        //recuperar as variaves email e senha
        $email = trim($_POST["emial"] ?? NULL);
        $senha = trim($_POST["senha"] ?? NULL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        }
    } else if (!isset($_SESSION["fogoEsangue"])) {
        //Mostrar tela de login
        require(__DIR__ . "/login.php");
    } else {
        //Mostrar tela do sistema
    }
    ?>

    <!--Chama o URL(nome da pagina) amigavel ou a pagina err -->
    <main>
        <?php
        // recuperar o param enviado pelo htaccess
        $param = $_GET["param"] ?? "pages/home";

        // cadastrar/categoria - listar/categoria
        $param = explode("/", $param);

        $pasta = $param[0] ?? NULL;
        $pagina = $param[1] ?? NULL;
        $id = $param[2] ?? NULL;

        // montar o nome do arquivo a ser carregado
        $pagina = "{$pasta}/{$pagina}.php";

        // verificar se o arquivo existe
        if (file_exists($pagina)) {

            include $pagina;
        } else {

            include "pages/erro.php";
        }
        ?>
    </main>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="logoCaixa" data-aos="fade-down" data-aos-easing="linear" data-aos-duration="1500">
                <img class="logoNavBar" src="../../IMG/logo.png" alt="home">
            </div>

            <!--Botão NAVBAR celular-->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="community">Community</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop">Shop</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Loja
                        </a>
                        <ul class="dropdown-menu" style="background-color: #121212;">
                            <li><a class="dropdown-item" href="cranios">Cranios</a></li>
                            <li><a class="dropdown-item" href="filhotes">Filhotes</a></li>
                            <li><a class="dropdown-item" href="estatuas">Estatuas</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="loginCompra">
            <i class="fa-solid fa-cart-shopping"></i>
            <i class="fa-solid fa-circle-user"></i>
        </div>
    </nav>


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

    <!-- JS do AOS -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>