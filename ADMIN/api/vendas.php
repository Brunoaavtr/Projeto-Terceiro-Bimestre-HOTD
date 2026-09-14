<?php
/* Retorna os dados das vendas para o dashboard */
header("Content-Type: application/json; charset=UTF-8");
require_once(__DIR__ . "/../../config.php");

try {
    $categoria = filter_input(INPUT_GET, "categoria", FILTER_VALIDATE_INT);
    $dataInicial = filter_input(INPUT_GET, "data_inicial", FILTER_DEFAULT);
    $dataFinal = filter_input(INPUT_GET, "data_final", FILTER_DEFAULT);

    $categoria = ($categoria !== false && $categoria !== null && $categoria > 0) ? $categoria : null;
    $dataInicial = ($dataInicial !== false && $dataInicial !== null && $dataInicial !== "") ? $dataInicial : null;
    $dataFinal = ($dataFinal !== false && $dataFinal !== null && $dataFinal !== "") ? $dataFinal : null;

    $condicoes = [];
    $parametros = [];

    if ($categoria !== null) {
        $condicoes[] = "ID_CATEGORIA = :categoria";
        $parametros[":categoria"] = $categoria;
    }

    if ($dataInicial !== null) {
        $condicoes[] = "DATE(DT_PEDIDO) >= :dataInicial";
        $parametros[":dataInicial"] = $dataInicial;
    }

    if ($dataFinal !== null) {
        $condicoes[] = "DATE(DT_PEDIDO) <= :dataFinal";
        $parametros[":dataFinal"] = $dataFinal;
    }

    $sql = "SELECT ID_PEDIDO, DT_PEDIDO, ID_PRODUTO, NM_PRODUTO, NM_MARCA,
                   ID_CATEGORIA, NM_CATEGORIA, QT_PRODUTO, VL_UNITARIO, VL_SUBTOTAL
            FROM vw_dashboard_vendas_detalhadas";

    if (!empty($condicoes)) {
        $sql .= " WHERE " . implode(" AND ", $condicoes);
    }

    $sql .= " ORDER BY DT_PEDIDO ASC, NM_PRODUTO ASC";

    $stmt = $pdo->prepare($sql);

    foreach ($parametros as $nome => $valor) {
        if ($nome === ":categoria") {
            $stmt->bindValue($nome, $valor, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($nome, $valor, PDO::PARAM_STR);
        }
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
    error_log("Erro na API de vendas: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "erro" => "Não foi possível carregar os dados das vendas."
    ], JSON_UNESCAPED_UNICODE);
}
?>
