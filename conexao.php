<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'foco';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro na conexao com o banco de dados: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>