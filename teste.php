<?php

$url = "https://viacep.com.br/ws/01001000/json/";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$resposta = curl_exec($ch);
$erro = curl_error($ch);

curl_close($ch);

echo "<pre>";

echo "Resposta:\n";
var_dump($resposta);

echo "\n\nErro:\n";
var_dump($erro);

echo "</pre>";