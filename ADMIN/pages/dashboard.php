<?php

/* Verifica se o usuário é administrador. */
if (!isset($_SESSION["tipo"]) || (int)$_SESSION["tipo"] !== 2) {
    header("Location: home");
    exit;
}
?>

<div class="container py-5">
    <div class="dashboardTecnico">
        <div class="text-center mb-4">
            <h1>Dashboard de vendas</h1>
            <p>Dados atualizados através da API do Covil do Dragão</p>
        </div>

        <div id="mensagem-dashboard" class="text-center mb-3"></div>

        <div class="mb-4">
            <label for="filtro-categoria" class="form-label">
                Filtrar por categoria
            </label>

            <select id="filtro-categoria" class="form-select">
                <option value="">Todas as categorias</option>

                <?php

                /* Busca as categorias ativas para o filtro. */
                $sqlCategoriasDashboard = "SELECT ID_CATEGORIA, NM_CATEGORIA
                                           FROM categoria
                                           WHERE FL_ATIVO = 1
                                           ORDER BY NM_CATEGORIA";

                /* Executa a consulta das categorias. */
                $categoriasDashboard = $pdo->query($sqlCategoriasDashboard)->fetchAll(PDO::FETCH_ASSOC);

                ?>

                <?php foreach ($categoriasDashboard as $categoria): ?>
                    <option value="<?= (int)$categoria["ID_CATEGORIA"] ?>">
                        <?= htmlspecialchars($categoria["NM_CATEGORIA"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label for="data-inicial" class="form-label">
                        Data inicial
                    </label>

                    <input
                        type="date"
                        id="data-inicial"
                        class="form-control">
                </div>

                <div class="col-md-6">
                    <label for="data-final" class="form-label">
                        Data final
                    </label>

                    <input
                        type="date"
                        id="data-final"
                        class="form-control">
                </div>
            </div>
        </div>

        <div class="dashboardMetricas row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-dark text-white h-100">
                    <div class="card-body text-center">
                        <h5>Total vendido</h5>
                        <h3 id="total-vendido">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-dark text-white h-100">
                    <div class="card-body text-center">
                        <h5>Faturamento</h5>
                        <h3 id="faturamento-total">R$ 0,00</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-dark text-white h-100">
                    <div class="card-body text-center">
                        <h5>Produtos vendidos</h5>
                        <h3 id="produtos-vendidos">0</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-dark text-white h-100">
                    <div class="card-body text-center">
                        <h5>Produto mais vendido</h5>
                        <h3 id="produto-mais-vendido">Nenhum</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Marca</th>
                        <th>Categoria</th>
                        <th>Quantidade vendida</th>
                        <th>Faturamento</th>
                    </tr>
                </thead>

                <tbody id="tabela-dashboard">
                    <tr>
                        <td colspan="5" class="text-center">
                            Carregando dados...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>