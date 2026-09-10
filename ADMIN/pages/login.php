<?php if (!empty($erroLogin)): ?>

    <div class="alert alert-danger text-center">
        <?= htmlspecialchars($erroLogin) ?>
    </div>

<?php endif; ?>

<div class="login">

    <div class="card shadow">

        <div class="card-header text-center">
            <img src="IMG/logo.png" alt="Covil do Dragão">
            <h1>Covil do Dragão</h1>
        </div>

        <div class="card-body">

            <form name="formLogin" method="post">

                <label for="email">E-mail:</label>

                <input type="email" name="email" id="email" class="form-control" required autocomplete="email">

                <br>

                <label for="senha">Senha:</label>

                <input type="password" name="senha" id="senha" class="form-control" required autocomplete="current-password">

                <br>

                <div class="btnLogin">

                    <button type="submit" name="entrar" value="login" class="btn">
                        Efetuar Login
                    </button>

                    <a href="cadastrar/usuario" class="btn">
                        Cadastrar
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>