<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $capacidade = $_POST['capacidade'] ?? '';
    $status = $_POST['status'] ?? '';

    if ($nome && $capacidade && $status) {
        $stmt = $conn->prepare("INSERT INTO salas (nome, capacidade, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nome, $capacidade, $status);
        if ($stmt->execute()) {
            echo 'OK';
        } else {
            echo 'Erro ao inserir';
        }
    } else {
        echo 'Dados incompletos';
    }
}
?>
