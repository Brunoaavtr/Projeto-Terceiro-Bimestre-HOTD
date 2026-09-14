<?php

/* Define que a resposta será enviada em formato JSON. */
header("Content-Type: application/json; charset=utf-8");

/* Verifica se a requisição foi enviada por POST. */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Método inválido."
    ]);
    exit;
}

/* Pega e limpa o CEP informado. */
$cep = preg_replace("/\D/", "", $_POST["cep"] ?? "");

if (strlen($cep) !== 8) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um CEP válido."
    ]);
    exit;
}

/* Consulta o CEP no ViaCEP. */
$url = "https://viacep.com.br/ws/" . $cep . "/json/";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$resposta = curl_exec($ch);
$erroCurl = curl_error($ch);

curl_close($ch);

/* Verifica se houve erro na consulta do CEP. */
if ($resposta === false || $erroCurl !== "") {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro no cURL: " . $erroCurl
    ]);
    exit;
}

/* Converte a resposta do ViaCEP para um array. */
$endereco = json_decode($resposta, true);

if (!$endereco || isset($endereco["erro"])) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "CEP não encontrado."
    ]);
    exit;
}

/* Pega a UF encontrada no CEP. */
$uf = strtoupper($endereco["uf"] ?? "");

/* Define os valores de frete para cada região. */
$valoresFrete = [
    "SUL" => 15.00,
    "SUDESTE" => 20.00,
    "CENTRO_OESTE" => 25.00,
    "NORDESTE" => 30.00,
    "NORTE" => 35.00
];

/* Define os estados pertencentes a cada região. */
$estadosPorRegiao = [
    "SUL" => ["PR", "SC", "RS"],
    "SUDESTE" => ["SP", "RJ", "MG", "ES"],
    "CENTRO_OESTE" => ["GO", "MT", "MS", "DF"],
    "NORDESTE" => ["BA", "SE", "AL", "PE", "PB", "RN", "CE", "PI", "MA"],
    "NORTE" => ["AM", "RR", "AP", "PA", "TO", "RO", "AC"]
];

$regiao = null;

/* Identifica a região de acordo com o estado. */
foreach ($estadosPorRegiao as $nomeRegiao => $estados) {
    if (in_array($uf, $estados, true)) {
        $regiao = $nomeRegiao;
        break;
    }
}

/* Verifica se a região foi identificada. */
if ($regiao === null) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível identificar a região do CEP."
    ]);
    exit;
}

/* Pega o valor do frete da região encontrada. */
$frete = $valoresFrete[$regiao];

/* Envia os dados do frete em formato JSON. */
echo json_encode([
    "sucesso" => true,
    "frete" => $frete,
    "cep" => $endereco["cep"] ?? "",
    "cidade" => $endereco["localidade"] ?? "",
    "uf" => $uf,
    "regiao" => $regiao
]);
?>