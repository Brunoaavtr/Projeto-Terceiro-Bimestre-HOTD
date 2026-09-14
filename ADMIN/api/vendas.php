<?php
/*
    Esta API disponibiliza os dados detalhados das vendas do Covil do Dragão.
    Os dados são obtidos através da View vw_dashboard_vendas_detalhadas,
    criada no MariaDB.

    A View reúne informações de pedidos, produtos, marcas e categorias,
    além da quantidade vendida, valor unitário, subtotal e data do pedido.

    Os parâmetros de categoria e período podem ser enviados pela URL para
    permitir que o TypeScript faça filtros específicos no dashboard.

    O fluxo fica:
    MariaDB → View → PHP → JSON → TypeScript
*/

header("Content-Type: application/json; charset=UTF-8");

require_once(__DIR__ . "/../../config.php");

try {
    $categoria = filter_input(INPUT_GET, "categoria", FILTER_VALIDATE_INT);
    $dataInicial = filter_input(INPUT_GET, "data_inicial", FILTER_DEFAULT);
    $dataFinal = filter_input(INPUT_GET, "data_final", FILTER_DEFAULT);

    if ($categoria === false || $categoria === null || $categoria <= 0) {
        $categoria = null;
    }

    if ($dataInicial === false || $dataInicial === null || $dataInicial === "") {
        $dataInicial = null;
    }

    if ($dataFinal === false || $dataFinal === null || $dataFinal === "") {
        $dataFinal = null;
    }

    $sql = "SELECT ID_PEDIDO, DT_PEDIDO, ID_PRODUTO, NM_PRODUTO, NM_MARCA,
                   ID_CATEGORIA, NM_CATEGORIA, QT_PRODUTO, VL_UNITARIO, VL_SUBTOTAL
            FROM vw_dashboard_vendas_detalhadas
            WHERE (:categoria IS NULL OR ID_CATEGORIA = :categoria)
            AND (:dataInicial IS NULL OR DATE(DT_PEDIDO) >= :dataInicial)
            AND (:dataFinal IS NULL OR DATE(DT_PEDIDO) <= :dataFinal)
            ORDER BY DT_PEDIDO ASC, NM_PRODUTO ASC";

    $stmt = $pdo->prepare($sql);

    if ($categoria === null) {
        $stmt->bindValue(":categoria", null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(":categoria", $categoria, PDO::PARAM_INT);
    }

    if ($dataInicial === null) {
        $stmt->bindValue(":dataInicial", null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(":dataInicial", $dataInicial, PDO::PARAM_STR);
    }

    if ($dataFinal === null) {
        $stmt->bindValue(":dataFinal", null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(":dataFinal", $dataFinal, PDO::PARAM_STR);
    }

    $stmt->execute();

    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "sucesso" => true,
        "categoria" => $categoria,
        "data_inicial" => $dataInicial,
        "data_final" => $dataFinal,
        "dados" => $dados
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "erro" => "Não foi possível carregar os dados das vendas."
    ], JSON_UNESCAPED_UNICODE);
}
?>
