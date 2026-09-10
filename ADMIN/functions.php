<?php

function redimensionarImagem($origem, $larguraMax, $alturaMax, $qualidade = 100) {

    $destino = $origem;

    // Verifica se o arquivo existe
    if (!file_exists($origem)) {
        return false;
    }

    // Pega informações da imagem
    list($larguraOriginal, $alturaOriginal, $tipo) = getimagesize($origem);

    // Calcula proporção
    $proporcao = $larguraOriginal / $alturaOriginal;

    if ($larguraMax / $alturaMax > $proporcao) {
        $novaLargura = $alturaMax * $proporcao;
        $novaAltura = $alturaMax;
    } else {
        $novaLargura = $larguraMax;
        $novaAltura = $larguraMax / $proporcao;
    }

    // Cria nova imagem
    $novaImagem = imagecreatetruecolor($novaLargura, $novaAltura);

    // Cria imagem original conforme tipo
    switch ($tipo) {

        case IMAGETYPE_JPEG:
            $imagem = imagecreatefromjpeg($origem);
            break;

        case IMAGETYPE_PNG:
            $imagem = imagecreatefrompng($origem);

            // Mantém transparência do PNG
            imagealphablending($novaImagem, false);
            imagesavealpha($novaImagem, true);
            break;

        default:
            return false;
    }

    // Redimensiona
    imagecopyresampled(
        $novaImagem,
        $imagem,
        0, 0, 0, 0,
        $novaLargura, $novaAltura,
        $larguraOriginal, $alturaOriginal
    );

    // Salva a imagem
    switch ($tipo) {

        case IMAGETYPE_JPEG:
            imagejpeg($novaImagem, $destino, $qualidade);
            break;

        case IMAGETYPE_PNG:
            imagepng($novaImagem, $destino);
            break;
    }

    // Libera memória
    imagedestroy($imagem);
    imagedestroy($novaImagem);

    return true;
}


function validarCPF($cpf) {

    // Remove tudo que não for número
    $cpf = preg_replace('/\D/', '', $cpf);

    // Verifica se possui 11 dígitos
    if (strlen($cpf) !== 11) {
        return false;
    }

    // Verifica CPFs com todos os números iguais
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Primeiro dígito
    $soma = 0;

    for ($i = 0; $i < 9; $i++) {
        $soma += $cpf[$i] * (10 - $i);
    }

    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    if ((int)$cpf[9] !== $digito1) {
        return false;
    }

    // Segundo dígito
    $soma = 0;

    for ($i = 0; $i < 10; $i++) {
        $soma += $cpf[$i] * (11 - $i);
    }

    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    if ((int)$cpf[10] !== $digito2) {
        return false;
    }

    return true;
}


function mensagem($titulo, $mensagem, $icone) {

    ?>

    <script>

        Swal.fire({
            title: <?= json_encode($titulo) ?>,
            text: <?= json_encode($mensagem) ?>,
            icon: <?= json_encode($icone) ?>
        }).then(() => {
            history.back();
        });

    </script>

    <?php
}