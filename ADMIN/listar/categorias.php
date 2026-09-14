<?php
/*
    Esta página lista as categorias ativas cadastradas no banco.
    O administrador pode cadastrar, editar ou desativar uma categoria.

    As categorias desativadas continuam no banco, mas não aparecem nesta lista.
    Somente administradores podem acessar esta página.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

$sql = "SELECT ID_CATEGORIA, NM_CATEGORIA, DS_CATEGORIA
        FROM categoria
        WHERE FL_ATIVO = 1
        ORDER BY NM_CATEGORIA";

$consulta = $pdo->query($sql);
$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Categorias</h1>
                <p>Gerencie as categorias cadastradas no Covil do Dragão</p>
            </div>

            <div class="card-body p-4">
                <div class="colabTopo mb-4">
                    <h2>Categorias cadastradas</h2>
                    <a href="cadastrarCategoria" class="btn botaoCovil botaoCadastrarMarca">Cadastrar</a>
                </div>

                <div class="row g-4 listaCategorias">
                    <?php if (count($categorias) > 0): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <div class="col-12 col-md-6 col-lg-4 categoriaColuna">
                                <div class="categoriaCard">
                                    <h3><?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?></h3>

                                    <div class="categoriaDescricao">
                                        <?php if (!empty($categoria["DS_CATEGORIA"])): ?>
                                            <?= nl2br(htmlspecialchars($categoria["DS_CATEGORIA"])) ?>
                                        <?php else: ?>
                                            <span>Nenhuma descrição cadastrada.</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="categoriaListaBotoes">
                                        <a href="editarCategoria?id=<?= (int)$categoria["ID_CATEGORIA"] ?>" class="btn botaoCovil">Editar</a>

                                        <form method="post" action="index.php?param=salvar/desativarCategoria" onsubmit="return confirm('Deseja realmente desativar esta categoria?');">
                                            <input type="hidden" name="id" value="<?= (int)$categoria["ID_CATEGORIA"] ?>">
                                            <button type="submit" class="btn botaoCovil">Desativar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="categoriaVazia">
                                <h3>Nenhuma categoria cadastrada</h3>
                                <p>Ainda não existem categorias ativas cadastradas no sistema.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>