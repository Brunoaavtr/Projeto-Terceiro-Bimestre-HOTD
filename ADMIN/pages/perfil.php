<?php

/* Verifica se o usuário está logado. */
if (!isset($_SESSION["fogoEsangue"])) {
    header("Location: login");
    exit;
}

/* Pega o ID do usuário logado. */
$idUsuario = $_SESSION["fogoEsangue"];

$erroFoto = "";
$sucessoFoto = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === UPLOAD_ERR_OK) {
        $foto = $_FILES["foto"];

        /* Define os tipos de imagem permitidos. */
        $tiposPermitidos = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        /* Verifica o tipo da imagem enviada. */
        $tipo = mime_content_type($foto["tmp_name"]);

        if (!isset($tiposPermitidos[$tipo])) {
            $erroFoto = "Escolha uma imagem JPG, PNG ou WEBP.";
        } elseif ($foto["size"] > 5 * 1024 * 1024) {
            $erroFoto = "A imagem pode ter no máximo 5 MB.";
        } else {
            /* Define a pasta onde a foto será salva. */
            $pastaFotos = __DIR__ . "/../../IMG/usuarios/";

            if (!is_dir($pastaFotos)) {
                mkdir($pastaFotos, 0777, true);
            }

            /* Cria o nome da nova foto. */
            $extensao = $tiposPermitidos[$tipo];
            $nomeFoto = "usuario_" . $idUsuario . "_" . time() . "." . $extensao;
            $caminhoCompleto = $pastaFotos . $nomeFoto;
            $caminhoBanco = "IMG/usuarios/" . $nomeFoto;

            /* Salva a nova foto na pasta de usuários. */
            if (move_uploaded_file($foto["tmp_name"], $caminhoCompleto)) {
                /* Busca o caminho da foto antiga. */
                $sqlFoto = "SELECT DS_FOTO
                            FROM usuario
                            WHERE ID_USUARIO = :id";

                $consultaFoto = $pdo->prepare($sqlFoto);
                $consultaFoto->execute([":id" => $idUsuario]);
                $fotoAntiga = $consultaFoto->fetchColumn();

                /* Atualiza a foto do usuário no banco. */
                $sqlAtualizar = "UPDATE usuario
                                 SET DS_FOTO = :foto
                                 WHERE ID_USUARIO = :id";

                $consultaAtualizar = $pdo->prepare($sqlAtualizar);
                $consultaAtualizar->execute([
                    ":foto" => $caminhoBanco,
                    ":id" => $idUsuario
                ]);

                /* Remove a foto antiga da pasta. */
                if (!empty($fotoAntiga) && file_exists(__DIR__ . "/../../" . $fotoAntiga)) {
                    unlink(__DIR__ . "/../../" . $fotoAntiga);
                }

                $sucessoFoto = "Foto de perfil atualizada!";
            } else {
                $erroFoto = "Não foi possível salvar a imagem.";
            }
        }
    }
}

/* Busca os dados do usuário logado. */
$sql = "SELECT
            u.NM_USUARIO,
            u.DS_EMAIL,
            u.NR_CPF,
            u.DT_NASCIMENTO,
            u.DS_FOTO,
            e.DS_LOGRADOURO,
            e.NR_ENDERECO,
            e.DS_BAIRRO,
            e.DS_COMPLEMENTO,
            e.NR_CEP,
            c.NM_CIDADE,
            es.NM_ESTADO,
            es.SG_ESTADO,
            t.NR_TELEFONE
        FROM usuario u
        LEFT JOIN endereco e ON e.ID_USUARIO = u.ID_USUARIO
        LEFT JOIN cidade c ON c.ID_CIDADE = e.ID_CIDADE
        LEFT JOIN estado es ON es.ID_ESTADO = c.ID_ESTADO
        LEFT JOIN telefone t ON t.ID_USUARIO = u.ID_USUARIO
        WHERE u.ID_USUARIO = :id
        LIMIT 1";

$consulta = $pdo->prepare($sql);
$consulta->execute([":id" => $idUsuario]);

/* Guarda os dados encontrados. */
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "<p>Não foi possível encontrar o usuário.</p>";
    exit;
}
?>

<div class="container py-5">
    <div class="perfil">
        <div class="card shadow">
            <div class="card-header text-center perfilCabecalho">
                <form method="post" enctype="multipart/form-data" id="formPerfil">
                    <label for="foto" class="fotoEscolha">
                        <?php if (!empty($usuario["DS_FOTO"])): ?>
                            <img src="<?= htmlspecialchars($usuario["DS_FOTO"]) ?>" class="fotoPerfil" alt="Foto de perfil">
                        <?php else: ?>
                            <div class="fotoPerfil">
                                <i class="fa-solid fa-dragon"></i>
                            </div>
                        <?php endif; ?>

                        <span class="iconeCamera">
                            <i class="fa-solid fa-camera"></i>
                        </span>
                    </label>

                    <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp" hidden>
                </form>

                <h1 class="mt-3">Cavaleiro de Dragão</h1>
                <p>Dados gravados no fogo e no sangue</p>
            </div>

            <div class="card-body p-4">
                <?php if (!empty($sucessoFoto)): ?>
                    <div class="sucessoPerfil">
                        <?= htmlspecialchars($sucessoFoto) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($erroFoto)): ?>
                    <div class="erroPerfil">
                        <?= htmlspecialchars($erroFoto) ?>
                    </div>
                <?php endif; ?>

                <h3 class="tituloSecaoPerfil">
                    <i class="fa-solid fa-dragon"></i>
                    Dados do Cavaleiro
                </h3>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nome:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["NM_USUARIO"]) ?></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>E-mail:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["DS_EMAIL"]) ?></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>CPF:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["NR_CPF"]) ?></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Data de nascimento:</label>
                        <div class="infoPerfil"><?= date("d/m/Y", strtotime($usuario["DT_NASCIMENTO"])) ?></div>
                    </div>
                </div>

                <h3 class="tituloSecaoPerfil">
                    <i class="fa-solid fa-landmark"></i>
                    Terras do Cavaleiro
                </h3>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label>Logradouro:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["DS_LOGRADOURO"] ?? "Não cadastrado") ?></div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Número:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["NR_ENDERECO"] ?? "Não cadastrado") ?></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Bairro:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["DS_BAIRRO"] ?? "Não cadastrado") ?></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Complemento:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["DS_COMPLEMENTO"] ?? "Não cadastrado") ?></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>CEP:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["NR_CEP"] ?? "Não cadastrado") ?></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Cidade:</label>
                        <div class="infoPerfil">
                            <?php if (!empty($usuario["NM_CIDADE"])): ?>
                                <?= htmlspecialchars($usuario["NM_CIDADE"]) ?> - <?= htmlspecialchars($usuario["SG_ESTADO"]) ?>
                            <?php else: ?>
                                Não cadastrado
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <h3 class="tituloSecaoPerfil">
                    <i class="fa-solid fa-phone"></i>
                    Meios de Contato
                </h3>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Telefone:</label>
                        <div class="infoPerfil"><?= htmlspecialchars($usuario["NR_TELEFONE"] ?? "Não cadastrado") ?></div>
                    </div>
                </div>

                <div class="perfilBotoes">
                    <a href="home" class="btn botaoCovil">Voltar</a>
                    <button type="submit" form="formPerfil" class="btn botaoCovil">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /* Pega o campo de foto. */
    const campoFoto = document.getElementById("foto");

    /* Mostra a prévia da nova foto. */
    campoFoto.addEventListener("change", function() {
        const arquivo = this.files[0];

        if (!arquivo) {
            return;
        }

        /* Cria a prévia da nova imagem. */
        const imagemAtual = document.querySelector(".fotoPerfil");
        const novaImagem = document.createElement("img");

        novaImagem.src = URL.createObjectURL(arquivo);
        novaImagem.className = "fotoPerfil";
        novaImagem.alt = "Foto de perfil";

        /* Substitui a imagem ou o ícone atual pela nova foto. */
        if (imagemAtual.tagName === "IMG") {
            imagemAtual.src = novaImagem.src;
        } else {
            imagemAtual.replaceWith(novaImagem);
        }
    });
</script>