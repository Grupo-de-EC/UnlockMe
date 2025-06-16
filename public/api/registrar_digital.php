<?php
include('db.php');
header('Content-Type: application/json');

// Lê os dados enviados pelo Python via POST
$data = json_decode(file_get_contents("php://input"), true);

$aluno_id = $data['aluno_id'] ?? null;
$biometria_id = $data['biometria_id'] ?? null;

if (!$aluno_id || !$biometria_id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos.']);
    exit;
}

// Verifica se a biometria já está em uso
$verifica = $conn->prepare("SELECT id FROM alunos WHERE digital = ?");
$verifica->bind_param("s", $biometria_id);
$verifica->execute();
$verifica->store_result();

if ($verifica->num_rows > 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Esta biometria já está cadastrada.']);
    exit;
}
$verifica->close();

// Atualiza o campo digital do aluno
$stmt = $conn->prepare("UPDATE alunos SET digital = ? WHERE id = ?");
$stmt->bind_param("si", $biometria_id, $aluno_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'mensagem' => 'Biometria registrada com sucesso.']);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Falha ao registrar biometria.']);
}

$stmt->close();
