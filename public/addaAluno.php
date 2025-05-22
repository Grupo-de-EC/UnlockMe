<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $sala_id = intval($_POST['sala_id']);

    $stmt = $conn->prepare("INSERT INTO alunos (nome, digital, status, horario_retirada, devolucao, sala_id) VALUES (?, ?, 'Pendente', NOW(), NULL, ?)");
    $stmt->bind_param("ssi", $nome, $matricula, $sala_id);
    $stmt->execute();

    echo "Aluno adicionado com sucesso!";
}
?>
