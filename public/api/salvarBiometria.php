<?php
include('../db.php');  // ajuste se necessário
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID não fornecido']);
    exit;
}

// Atualiza o status para indicar que foi recebida a digital (opcional)
$stmt = $conn->prepare("UPDATE alunos SET status = 'digital_cadastrada' WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'mensagem' => 'Biometria recebida com sucesso']);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => $stmt->error]);
}
?>
