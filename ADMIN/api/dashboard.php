<?php
/*
    Esta API busca os dados do dashboard diretamente no banco de dados.
    Ela utiliza a Stored Procedure sp_dashboard_produtos, criada no MariaDB,
    e transforma o resultado em JSON para que o TypeScript possa consumir
    os dados através do fetch().

    O fluxo fica assim:
    MariaDB → Stored Procedure → PHP → JSON → TypeScript

    Os parâmetros pagina, por_pagina e categoria podem ser enviados pela URL.

    Exemplos:
    dashboard.php?pagina=1&por_pagina=10
    dashboard.php?pagina=1&por_pagina=10&categoria=2
*/

header("Content-Type: application/json; charset=UTF-8");

require_once(__DIR__ . "/../../config.php");

try {
    $pagina = filter_input(INPUT_GET, "pagina", FILTER_VALIDATE_INT);
    $porPagina = filter_input(INPUT_GET, "por_pagina", FILTER_VALIDATE_INT);
    $categoria = filter_input(INPUT_GET, "categoria", FILTER_VALIDATE_INT);

    $pagina = ($pagina !== false && $pagina !== null && $pagina > 0) ? $pagina : 1;
    $porPagina = ($porPagina !== false && $porPagina !== null && $porPagina > 0) ? $porPagina : 10;

    if ($categoria === false || $categoria === null || $categoria <= 0) {
        $categoria = null;
    }

    $stmt = $pdo->prepare("CALL sp_dashboard_produtos(:pagina, :por_pagina, :categoria)");

    $stmt->bindValue(":pagina", $pagina, PDO::PARAM_INT);
    $stmt->bindValue(":por_pagina", $porPagina, PDO::PARAM_INT);

    if ($categoria === null) {
        $stmt->bindValue(":categoria", null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(":categoria", $categoria, PDO::PARAM_INT);
    }

    $stmt->execute();

    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    echo json_encode([
        "sucesso" => true,
        "pagina" => $pagina,
        "por_pagina" => $porPagina,
        "categoria" => $categoria,
        "dados" => $dados
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "sucesso" => false,
        "erro" => "Não foi possível carregar os dados do dashboard."
    ], JSON_UNESCAPED_UNICODE);
}
?>
