<?php
/* Verifica a saída do usuário */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sair"])) {
    session_unset();
    session_destroy();
    header("Location: {$baseUrl}/login");
    exit;
}
?>

<section class="paginaSair">
    <div class="sairCard">
        <i class="fa-solid fa-door-open iconeSaida"></i>
        <h1>Deseja deixar o Covil?</h1>
        <p>Tem certeza que deseja sair da sua conta?</p>
        <div class="botoesSair">
            <a href="javascript:history.back()" class="botaoVoltar">Voltar</a>
            <form method="post">
                <button type="submit" name="sair" class="botaoSair">Sair</button>
            </form>
        </div>
    </div>
</section>