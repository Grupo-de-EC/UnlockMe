<?php
include('db.php');
session_start();

if ($_SESSION['user_role'] !== 'aluno') {
    die("Aluno não autenticado.");
}

$aluno_id = $_SESSION['user_id'];
$mensagem = ""; // <- Variável para armazenar a mensagem

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigoComputador = trim($_POST['codigo_computador']);

    if (empty($codigoComputador)) {
        $mensagem = "<p class='error-msg'>Preencha o código do computador.</p>";
    } else {
        $horario = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("UPDATE alunos SET codigo_computador = ?, status = 'retirou', horario_retirada = ? WHERE id = ?");
        $stmt->bind_param("ssi", $codigoComputador, $horario, $aluno_id);

        if ($stmt->execute()) {
            $mensagem = "<p class='success-msg'>Código registrado com sucesso!</p>";
        } else {
            $mensagem = "<p class='error-msg'>Erro ao registrar código: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registro de Computador</title>
    <link rel="stylesheet" href="formCadastroAluno.css">
    <style>
        .success-msg {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .error-msg {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Registro de Código do Computador</h2>

        <label for="codigo_computador">Código do Computador:</label>
        <input type="text" name="codigo_computador" required><br><br>

        <input type="submit" value="Registrar Código">

        <!-- Aqui vai a mensagem -->
        <?php echo $mensagem; ?>
    </form>
</body>
</html>