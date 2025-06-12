<?php
include('db.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$aluno_id = intval($data['aluno_id'] ?? 0);
$digital = trim($data['digital'] ?? '');

if ($aluno_id <= 0 || empty($digital)) {
    echo json_encode(["status" => "erro", "mensagem" => "Dados inválidos"]);
    exit;
}

$stmt = $conn->prepare("UPDATE alunos SET digital = ? WHERE id = ?");
$stmt->bind_param("si", $digital, $aluno_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => $stmt->error]);
}
