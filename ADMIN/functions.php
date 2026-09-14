<?php

/* Redimensiona uma imagem mantendo sua proporção original. */
function redimensionarImagem($origem, $larguraMax, $alturaMax, $qualidade = 100)
{
    $destino = $origem;

    /* Verifica se o arquivo da imagem existe. */
    if (!file_exists($origem)) {
        return false;
    }

    /* Pega as informações da imagem original. */
    [$larguraOriginal, $alturaOriginal, $tipo] = getimagesize($origem);

    /* Calcula a proporção da imagem. */
    $proporcao = $larguraOriginal / $alturaOriginal;

    /* Define as novas dimensões mantendo a proporção. */
    if ($larguraMax / $alturaMax > $proporcao) {
        $novaLargura = $alturaMax * $proporcao;
        $novaAltura = $alturaMax;
    } else {
        $novaLargura = $larguraMax;
        $novaAltura = $larguraMax / $proporcao;
    }

    /* Cria a nova imagem com as dimensões calculadas. */
    $novaImagem = imagecreatetruecolor($novaLargura, $novaAltura);

    /* Abre a imagem de acordo com o seu formato. */
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagem = imagecreatefromjpeg($origem);
            break;

        case IMAGETYPE_PNG:
            $imagem = imagecreatefrompng($origem);

            /* Mantém a transparência das imagens PNG. */
            imagealphablending($novaImagem, false);
            imagesavealpha($novaImagem, true);
            break;

        default:
            return false;
    }

    /* Redimensiona a imagem mantendo a qualidade. */
    imagecopyresampled(
        $novaImagem,
        $imagem,
        0,
        0,
        0,
        0,
        $novaLargura,
        $novaAltura,
        $larguraOriginal,
        $alturaOriginal
    );

    /* Salva a imagem redimensionada no formato original. */
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            imagejpeg($novaImagem, $destino, $qualidade);
            break;

        case IMAGETYPE_PNG:
            imagepng($novaImagem, $destino);
            break;
    }

    /* Libera a memória utilizada pelas imagens. */
    imagedestroy($imagem);
    imagedestroy($novaImagem);

    return true;
}

/* Valida um CPF verificando seus dígitos e os dois dígitos verificadores. */
function validarCPF($cpf)
{
    /* Remove caracteres que não são números. */
    $cpf = preg_replace('/\D/', '', $cpf);

    /* Verifica se o CPF possui 11 dígitos. */
    if (strlen($cpf) !== 11) {
        return false;
    }

    /* Impede CPFs formados apenas pelo mesmo número. */
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    /* Calcula o primeiro dígito verificador. */
    $soma = 0;

    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }

    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    /* Confere o primeiro dígito verificador. */
    if ((int)$cpf[9] !== $digito1) {
        return false;
    }

    /* Calcula o segundo dígito verificador. */
    $soma = 0;

    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }

    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    /* Confere o segundo dígito verificador. */
    if ((int)$cpf[10] !== $digito2) {
        return false;
    }

    return true;
}

/* Mostra uma mensagem com SweetAlert2 e volta para a página anterior. */
function mensagem($titulo, $mensagem, $icone)
{
    ?>
    <script>
    /* Exibe a mensagem para o usuário. */
    Swal.fire({
        title: <?= json_encode($titulo) ?>,
        text: <?= json_encode($mensagem) ?>,
        icon: <?= json_encode($icone) ?>
    }).then(() => {
        /* Volta para a página anterior depois da mensagem. */
        history.back();
    });
    </script>
    <?php
}
?>