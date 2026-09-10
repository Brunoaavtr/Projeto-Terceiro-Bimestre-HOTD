<?php

if (!isset($pagina)) {
    exit;
}

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "admin") {
    include __DIR__ . "/err.php";
    exit;
}

$sql = "SELECT id, nome, email, cpf, datanascimento, ativo, tipo FROM usuarios ORDER BY nome";

$consulta = $pdo->prepare($sql);
$consulta->execute();

$dadosUsuarios = $consulta->fetchAll(PDO::FETCH_OBJ);

?>

<div class="card shadow m-5">

    <div class="card-header">

        <div class="float-start">
            <h2>Listagem de Usuários</h2>
        </div>

        <div class="float-end">

            <a href="index.php?param=cadastrar/usuario" class="btn btn-success">
                Novo Registro
            </a>

            <a href="index.php?param=usuarios" class="btn btn-primary">
                Listar Registros
            </a>

        </div>

    </div>

    <div class="card-body">

        <table class="table table-bordered table-striped">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Nome do Usuário</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Data de Nascimento</th>
                    <th>Ativo</th>
                    <th>Tipo</th>
                    <th>Opções</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($dadosUsuarios as $dados) { ?>

                    <tr>

                        <td>
                            <?= $dados->id ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($dados->nome) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($dados->email) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($dados->cpf) ?>
                        </td>

                        <td>
                            <?= !empty($dados->datanascimento) ? date("d/m/Y", strtotime($dados->datanascimento)) : "" ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($dados->ativo) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($dados->tipo) ?>
                        </td>

                        <td>

                            <a href="index.php?param=cadastrar/usuario/<?= $dados->id ?>" class="btn btn-success btn-sm">
                                Editar
                            </a>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>