<?php
/*
Este arquivo representa a página de erro do Covil do Dragão.

Ela é exibida quando o usuário tenta acessar uma página ou rota que não existe
dentro do projeto.

A página utiliza a mesma identidade visual das demais áreas do site, mostrando
a logo do Covil, o código de erro 404, uma mensagem explicativa e um botão para
retornar à página inicial.

O endereço da Home utiliza a variável $baseUrl definida no index.php, evitando
caminhos fixos ou incorretos caso o projeto esteja dentro de uma pasta no XAMPP.
*/
?>

<section class="paginaErro">
    <div class="erroPainel">

        <div class="erroLogo">
        </div>

        <div class="erroConteudo">
            <span class="erroCodigo">404</span>

            <h1>Este caminho não leva ao Covil</h1>

            <p>
                A página que você tentou acessar não existe,
                foi removida ou está em um endereço diferente.
            </p>

            <a href="<?= $baseUrl ?>/home" class="botaoCovil">
                <i class="fa-solid fa-dragon"></i>
                Voltar ao Covil
            </a>
        </div>

    </div>
</section>