<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $capacidade = $_POST['capacidade'] ?? '';

    if (empty($nome) || empty($capacidade)) {
        echo 'Dados incompletos';
        exit;
    }
    if (!is_numeric($capacidade) || $capacidade <= 0) {
        echo 'Capacidade inválida';
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO salas (nome, capacidade) VALUES (?, ?)");
    if ($stmt === false) {
        echo 'Erro ao preparar a consulta: ' . $conn->error;
        exit;
    }

    $stmt->bind_param("si", $nome, $capacidade);

    if ($stmt->execute()) {
        echo 'Sala adicionada com sucesso!';
    } else {
        echo 'Erro ao inserir a sala: ' . $stmt->error;
    }
} else {
    echo 'Método HTTP inválido';
}
?>
