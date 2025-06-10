<?php
session_start();
include('db.php');

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$nome || !$senha) {
        $erro = "Preencha todos os campos.";
    } else {
        // Verifica se é professor
        $stmt = $conn->prepare("SELECT id, nome, senha FROM professores WHERE nome = ?");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($professor = $result->fetch_assoc()) {
            // Aqui assumo senha armazenada sem hash (melhor usar password_hash)
            if ($senha === $professor['senha']) {
                $_SESSION['user_id'] = $professor['id'];
                $_SESSION['user_nome'] = $professor['nome'];
                $_SESSION['user_role'] = 'professor';
                header('Location: salas.php');
                exit;
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            // Verifica se é aluno
            $stmt = $conn->prepare("SELECT id, nome, senha FROM alunos WHERE nome = ?");
            $stmt->bind_param("s", $nome);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($aluno = $result->fetch_assoc()) {
                if ($senha === $aluno['senha']) {
                    $_SESSION['user_id'] = $aluno['id'];
                    $_SESSION['user_nome'] = $aluno['nome'];
                    $_SESSION['user_role'] = 'aluno';
                    header('Location: computador.php');
                    exit;
                } else {
                    $erro = "Senha incorreta.";
                }
            } else {
                $erro = "Usuário não encontrado.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login - Controle de Retirada</title>
    <link rel="stylesheet" href="login.css" />
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <?php if ($erro): ?>
        <div class="error-msg"><?=htmlspecialchars($erro)?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required />

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required />

        <button type="submit">Entrar</button>
    </form>

    <a href="formCadastroAluno.php" class="btn-cadastrar">Cadastrar como Aluno</a>
</div>
</body>
</html>
