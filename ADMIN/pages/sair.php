<?php

/* Verifica se o usuário confirmou a saída da conta. */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sair"])) {
    session_unset();
    session_destroy();

    /* Redireciona o usuário para o login. */
    header("Location: login");
    exit;
}
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card text-center shadow p-4" style="max-width: 500px; width: 100%;">
        <i class="fa-solid fa-door-open iconeSaida"></i>
        <h2>Deseja deixar o Covil?</h2>
        <p>Tem certeza que deseja sair da sua conta?</p>

        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="javascript:history.back()" class="btn btn-secondary">Voltar</a>

            <form method="post">
                <button type="submit" name="sair" class="btn btn-danger">Sair</button>
            </form>
        </div>
    </div>
</div>