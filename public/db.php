<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'unlock_me';

$conn = new mysqli($host, $user, $pass, $dbname, 3307);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
