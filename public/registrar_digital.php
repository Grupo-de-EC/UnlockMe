<?php
include("db.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$aluno_id = intval($data["aluno_id"] ?? 0);
$digital = intval($data["digital"] ?? 0);

if ($aluno_id > 0 && $digital > 0) {
    $stmt = $conn->prepare("UPDATE alunos SET digital = ?, status = 'cadastrado' WHERE id = ?");
    $stmt->bind_param("ii", $digital, $aluno_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "erro", "mensagem" => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Dados inválidos."]);
}
