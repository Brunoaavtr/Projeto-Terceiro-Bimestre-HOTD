<?php if ($erroLogin !== ""): ?>

    <div class="alert alert-danger text-center">
        <?= $erroLogin ?>
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

                <label for="email">
                    E-mail:
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    required>

                <br>

                <label for="senha">
                    Senha:
                </label>

                <input
                    type="password"
                    name="senha"
                    id="senha"
                    class="form-control"
                    required>

                <br>

                <div class="btnLogin">

                    <button
                        type="submit"
                        class="btn">

                        Efetuar Login

                    </button>

                    <button
                        type="submit"
                        class="btn">

                        Cadastrar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>