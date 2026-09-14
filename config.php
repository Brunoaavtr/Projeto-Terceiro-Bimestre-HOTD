<?php
/*
    Configuração da conexão com o banco de dados.

    O PDO usa o modo de exceções para que erros de INSERT, UPDATE e SELECT não
    passem silenciosamente. Assim os arquivos de cadastro podem capturar o erro,
    desfazer transações quando necessário e mostrar uma mensagem adequada.
*/
$host = "localhost";
$db = "projeto-terceiro-bimestre-hotd";
$user = "root";
$pass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>