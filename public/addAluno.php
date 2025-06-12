<?php
include('db.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$nome = trim($data['nome'] ?? '');
$senha = trim($data['senha'] ?? '');
$codigo = trim($data['codigo'] ?? '');

if (empty($nome) || empty($senha) || empty($codigo)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Preencha todos os campos.']);
    exit;
}

$stmtSala = $conn->prepare("SELECT id, capacidade FROM salas WHERE codigo = ?");
$stmtSala->bind_param("s", $codigo);
$stmtSala->execute();
$stmtSala->bind_result($sala_id, $capacidade);
$stmtSala->fetch();
$stmtSala->close();

if (!$sala_id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Código da sala inválido.']);
    exit;
}

$stmtContagem = $conn->prepare("SELECT COUNT(*) FROM alunos WHERE sala_id = ?");
$stmtContagem->bind_param("i", $sala_id);
$stmtContagem->execute();
$stmtContagem->bind_result($total_alunos);
$stmtContagem->fetch();
$stmtContagem->close();

if ($total_alunos >= $capacidade) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Sala cheia.']);
    exit;
}

$status = 'nao pegou';
$stmtAluno = $conn->prepare("INSERT INTO alunos (nome, senha, status, sala_id) VALUES (?, ?, ?, ?)");
$stmtAluno->bind_param("sssi", $nome, $senha, $status, $sala_id);

if ($stmtAluno->execute()) {
    $aluno_id = $stmtAluno->insert_id;
    echo json_encode(['status' => 'aguardando_biometria', 'aluno_id' => $aluno_id]);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => $stmtAluno->error]);
}

$stmtAluno->close();
