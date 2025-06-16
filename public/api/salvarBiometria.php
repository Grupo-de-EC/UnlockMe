<?php
include('../db.php');  // ajuste caminho se necessário
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$aluno_id = $data['aluno_id'] ?? null;
$template = $data['template'] ?? null;

if (!$aluno_id || !$template) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos']);
    exit;
}

// Atualiza o template biométrico (campo digital) e status na tabela alunos
$stmt = $conn->prepare("UPDATE alunos SET digital = ?, status = 'pegou' WHERE id = ?");
$stmt->bind_param("si", $template, $aluno_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => $stmt->error]);
}
?>
