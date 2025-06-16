<?php
include('../db.php');
header('Content-Type: application/json');

$aluno_id = intval($_GET['id'] ?? 0);
if ($aluno_id <= 0) {
    echo json_encode(['status' => 'erro']);
    exit;
}

$stmt = $conn->prepare("SELECT digital FROM alunos WHERE id = ?");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$stmt->bind_result($digital);
$stmt->fetch();
$stmt->close();

if ($digital) {
    echo json_encode(['status' => 'ok', 'digital' => $digital]);
} else {
    echo json_encode(['status' => 'pendente']);
}
