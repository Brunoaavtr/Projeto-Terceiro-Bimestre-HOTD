<?php
/*
    Esta página lista as marcas ativas cadastradas no banco.
    O administrador pode cadastrar, editar ou desativar uma marca.

    As marcas desativadas continuam no banco, mas não aparecem nesta lista.
    A imagem da marca vem da coluna DS_IMAGEM.
    Somente administradores podem acessar esta página.
*/

if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}

$sql = "SELECT ID_MARCA, NM_MARCA, DS_IMAGEM
        FROM marca
        WHERE FL_ATIVO = 1
        ORDER BY NM_MARCA";

$consulta = $pdo->query($sql);
$marcas = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="colab">
        <div class="card shadow">
            <div class="card-header text-center">
                <h1>Marcas</h1>
                <p>Marcas parceiras do Covil do Dragão</p>
            </div>

            <div class="card-body p-4">
                <div class="colabTopo mb-4">
                    <h2>Marcas cadastradas</h2>
                    <a href="cadastrarMarca" class="btn botaoCovil botaoCadastrarMarca">Cadastrar</a>
                </div>

                <div class="row g-4 listaMarcas">
                    <?php if (count($marcas) > 0): ?>
                        <?php foreach ($marcas as $marca): ?>
                            <div class="col-12 col-md-6 col-lg-4 marcaColuna">
                                <div class="marcaCard">
                                    <h3><?= htmlspecialchars($marca["NM_MARCA"]) ?></h3>

                                    <div class="marcaImagem">
                                        <?php if (!empty($marca["DS_IMAGEM"])): ?>
                                            <img src="<?= htmlspecialchars($marca["DS_IMAGEM"]) ?>" alt="Logo da marca <?= htmlspecialchars($marca["NM_MARCA"]) ?>">
                                        <?php else: ?>
                                            <span>Nenhuma imagem cadastrada.</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="marcaListaBotoes">
                                        <a href="editarMarca?id=<?= (int)$marca["ID_MARCA"] ?>" class="btn botaoCovil">Editar</a>

                                        <form method="post" action="index.php?param=salvar/desativarMarca" onsubmit="return confirm('Deseja realmente desativar esta marca?');">
                                            <input type="hidden" name="id" value="<?= (int)$marca["ID_MARCA"] ?>">
                                            <button type="submit" class="btn botaoCovil">Desativar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="marcaVazia">
                                <h3>Nenhuma marca cadastrada</h3>
                                <p>Ainda não existem marcas ativas cadastradas no sistema.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>