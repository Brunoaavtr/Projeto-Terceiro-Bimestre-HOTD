<?php
/*
    Arquivo principal do projeto Covil do Dragão.
    Aqui ficam o controle das rotas, sessão, login e carregamento das páginas.

    As telas visuais ficam em ADMIN/pages, as listagens em ADMIN/listar e os
    arquivos responsáveis por alterações no banco ficam em ADMIN/salvar e
    ADMIN/desativar.

    O menu Loja mostra automaticamente as categorias ativas cadastradas.
    O carrinho possui uma página própria para visualizar os produtos e os
    pedidos já realizados.

    A variável $baseUrl representa a pasta principal do projeto e é usada nos
    links, imagens, CSS e JavaScript para evitar problemas com caminhos relativos.
*/

ob_start();
session_start();

require(__DIR__ . "/config.php");
require_once(__DIR__ . "/ADMIN/functions.php");

$baseUrl = "/FaculdadeXAMP/Projeto-Terceiro-Bimestre-HOTD";

$param = trim($_GET["param"] ?? "home", "/");
$partes = explode("/", $param);

$rota = $partes[0] ?? "home";
$subrota = $partes[1] ?? null;

$categorias = [];

try {
    $sqlCategorias = "SELECT ID_CATEGORIA, NM_CATEGORIA
                      FROM categoria
                      WHERE FL_ATIVO = 1
                      ORDER BY NM_CATEGORIA";

    $consultaCategorias = $pdo->query($sqlCategorias);
    $categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
}

$erroLogin = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["entrar"])) {
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");

    if ($email === "" || $senha === "") {
        $erroLogin = "Preencha o e-mail e a senha.";
    } else {
        $sql = "SELECT ID_USUARIO, DS_EMAIL, DS_SENHA, FL_ATIVO, ID_TIPO_USUARIO
                FROM usuario
                WHERE DS_EMAIL = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([":email" => $email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario["DS_SENHA"])) {
            if ((int)$usuario["FL_ATIVO"] !== 1) {
                $erroLogin = "Este usuário está inativo.";
            } else {
                $_SESSION["fogoEsangue"] = $usuario["ID_USUARIO"];
                $_SESSION["tipo"] = (int)$usuario["ID_TIPO_USUARIO"];

                header("Location: " . $baseUrl . "/home");
                exit;
            }
        } else {
            $erroLogin = "E-mail ou senha incorretos.";
        }
    }
}

/* Aqui ficam as rotas de processamento */
if ($rota === "salvar" && $subrota === "cadastro") {
    require(__DIR__ . "/ADMIN/salvar/cadastro.php");
    exit;
}

if ($rota === "salvar" && $subrota === "marca") {
    require(__DIR__ . "/ADMIN/salvar/marca.php");
    exit;
}

if ($rota === "salvar" && $subrota === "editarMarca") {
    require(__DIR__ . "/ADMIN/salvar/editarMarca.php");
    exit;
}

if ($rota === "salvar" && $subrota === "categoria") {
    require(__DIR__ . "/ADMIN/salvar/categoria.php");
    exit;
}

if ($rota === "salvar" && $subrota === "editarCategoria") {
    require(__DIR__ . "/ADMIN/salvar/editarCategoria.php");
    exit;
}

if ($rota === "salvar" && $subrota === "produto") {
    require(__DIR__ . "/ADMIN/salvar/produto.php");
    exit;
}

if ($rota === "salvar" && $subrota === "editarProduto") {
    require(__DIR__ . "/ADMIN/salvar/editarProduto.php");
    exit;
}

if ($rota === "salvar" && $subrota === "editarPedido") {
    require(__DIR__ . "/ADMIN/salvar/editarPedido.php");
    exit;
}

if ($rota === "salvar" && $subrota === "desativarMarca") {
    require(__DIR__ . "/ADMIN/desativar/desativarMarca.php");
    exit;
}

if ($rota === "salvar" && $subrota === "desativarCategoria") {
    require(__DIR__ . "/ADMIN/desativar/desativarCategoria.php");
    exit;
}

if ($rota === "salvar" && $subrota === "desativarProduto") {
    require(__DIR__ . "/ADMIN/desativar/desativarProduto.php");
    exit;
}

if ($rota === "salvar" && $subrota === "frete") {
    require(__DIR__ . "/ADMIN/salvar/frete.php");
    exit;
}

if ($rota === "salvar" && $subrota === "finalizarCompra") {
    require(__DIR__ . "/ADMIN/salvar/finalizarCompra.php");
    exit;
}

if ($rota === "desativar" && $subrota === "marca") {
    require(__DIR__ . "/ADMIN/desativar/desativarMarca.php");
    exit;
}

if ($rota === "desativar" && $subrota === "categoria") {
    require(__DIR__ . "/ADMIN/desativar/desativarCategoria.php");
    exit;
}

if ($rota === "desativar" && $subrota === "produto") {
    require(__DIR__ . "/ADMIN/desativar/desativarProduto.php");
    exit;
}

/* Aqui ficam as ações do carrinho */
if ($rota === "carrinho" && $subrota !== null && $subrota !== "itens") {
    require(__DIR__ . "/ADMIN/salvar/carrinho.php");
    exit;
}
?>

<!DOCTYPE html>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Covil do Dragão</title>

  
    <link rel="icon" href="<?= $baseUrl ?>/IMG/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?>/CSS/style.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Pliant:ital,wght@0,100..900;1,100..900&family=Rowdies:wght@300;400;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.2/dist/parsley.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

</head>

<body>
    <?php
    if ($rota === "login") {
        require(__DIR__ . "/ADMIN/pages/login.php");
    } elseif ($rota === "cadastro") {
        require(__DIR__ . "/ADMIN/pages/cadastro.php");
    } elseif (!isset($_SESSION["fogoEsangue"])) {
        require(__DIR__ . "/ADMIN/pages/login.php");
    } else {
    ?>

        
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container navbarConteudo">
                <div class="logoCaixa" data-aos="fade-down" data-aos-easing="linear" data-aos-duration="1500">
                    <a href="<?= $baseUrl ?>/home">
                        <img class="logoNavBar" src="<?= $baseUrl ?>/IMG/logo.png" alt="Covil do Dragão">
                    </a>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNavbar" aria-controls="menuNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="menuNavbar">
                    <div class="menuEsquerdo">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $baseUrl ?>/home">Home</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= $baseUrl ?>/community">Community</a>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="<?= $baseUrl ?>/loja" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Loja
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?= $baseUrl ?>/loja">
                                            Todos os produtos
                                        </a>
                                    </li>

                                    <?php foreach ($categorias as $categoria): ?>
                                        <li>
                                            <a class="dropdown-item" href="<?= $baseUrl ?>/loja?categoria=<?= (int)$categoria["ID_CATEGORIA"] ?>">
                                                <?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <div class="menuDireito ms-auto">
                        <?php if (isset($_SESSION["tipo"]) && (int)$_SESSION["tipo"] === 2): ?>
                            <ul class="navbar-nav mb-2 mb-lg-0 menuAdmin">
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= $baseUrl ?>/colab">Admin</a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Área do carrinho, perfil e saída -->
                <div class="loginCompra">
                    <a href="<?= $baseUrl ?>/carrinho">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a>

                    <a href="<?= $baseUrl ?>/perfil">
                        <i class="fa-solid fa-dragon fotoPerfilNav"></i>
                    </a>

                    <a href="<?= $baseUrl ?>/sair">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </nav>

        <main>
            <?php
            if ($rota === "marcas") {
                $arquivo = __DIR__ . "/ADMIN/listar/marcas.php";
            } elseif ($rota === "categorias") {
                $arquivo = __DIR__ . "/ADMIN/listar/categorias.php";
            } elseif ($rota === "produtos") {
                $arquivo = __DIR__ . "/ADMIN/listar/produtos.php";
            } elseif ($rota === "pedidos") {
                if (isset($_SESSION["tipo"]) && (int)$_SESSION["tipo"] === 2) {
                    $arquivo = __DIR__ . "/ADMIN/listar/pedidos.php";
                } else {
                    $arquivo = __DIR__ . "/ADMIN/pages/pedidos.php";
                }
            } elseif ($rota === "carrinho") {
                $arquivo = __DIR__ . "/ADMIN/pages/carrinho.php";
            } elseif ($rota === "home") {
                $arquivo = __DIR__ . "/ADMIN/pages/home.php";
            } else {
                $arquivo = __DIR__ . "/ADMIN/pages/{$rota}.php";
            }

            if (file_exists($arquivo)) {
                include $arquivo;
            } else {
                include __DIR__ . "/ADMIN/pages/err.php";
            }
            ?>
        </main>

        <!-- Footer -->
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

    <?php if ($rota === "dashboard"): ?>

        
        <script type="module" src="<?= $baseUrl ?>/TS/dist/dashboard.js"></script>
        

    <?php endif; ?>

</body>

</html>

<?php
ob_end_flush();
?>