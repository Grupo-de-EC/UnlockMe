<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $capacidade = $_POST['capacidade'] ?? '';

    if ($nome && $capacidade && $status) {
        $stmt = $conn->prepare("INSERT INTO salas (nome, capacidade) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nome, $capacidade);
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
