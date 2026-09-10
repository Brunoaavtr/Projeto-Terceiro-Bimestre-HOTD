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

            // Mantém transparência no PNG
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

    // Verifica se tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Elimina CPFs inválidos conhecidos (todos iguais)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Validação do 1º dígito verificador
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;

        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }

        $digito = ((10 * $soma) % 11) % 10;

        if ($cpf[$t] != $digito) {
            return "CPF inválido";
        }
    }

    return true;
}

function mensagem($titulo, $mensagem, $icone) {
    ?>
    <script>
        Swal.fire({
            title: "<?= $titulo ?>",
            text: "<?= $mensagem ?>",
            icon: "<?= $icone ?>"
        }).then((result) => {
           history.back(); 
        });
    </script>
    <?php
}