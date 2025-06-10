<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome']);
    $senha = trim($_POST['senha']);
    $codigo = trim($_POST['codigo']);

    if (empty($nome) || empty($senha) || empty($codigo)) {
        die("Preencha todos os campos.");
    }

    // Buscar ID da sala com base no código
    $stmtSala = $conn->prepare("SELECT id FROM salas WHERE codigo = ?");
    $stmtSala->bind_param("s", $codigo);
    $stmtSala->execute();
    $stmtSala->bind_result($sala_id);
    $stmtSala->fetch();
    $stmtSala->close();

    if (!$sala_id) {
        die("Código da sala inválido.");
    }

    $status = 'nao pegou';
    $stmtAluno = $conn->prepare("INSERT INTO alunos (nome, senha, status, sala_id) VALUES (?, ?, ?, ?)");
    $stmtAluno->bind_param("sssi", $nome, $senha, $status, $sala_id);

    if ($stmtAluno->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "Erro ao cadastrar: " . $stmtAluno->error;
    }

    $stmtAluno->close();
}
?>
