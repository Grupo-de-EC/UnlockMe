<?php
include('db.php');
session_start();

if ($_SESSION['user_role'] !== 'aluno') {
    die("Aluno não autenticado.");
}

$aluno_id = $_SESSION['user_id'];
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['digitalCheck'])) {
        $mensagem = "<p class='error-msg'>Você precisa confirmar que passou a digital.</p>";
    } else {
        $codigoComputador = trim($_POST['codigo_computador']);

        if (empty($codigoComputador)) {
            $mensagem = "<p class='error-msg'>Preencha o código do computador.</p>";
        } else {
            $horario = date("Y-m-d H:i:s");

            // Consulta o status atual
            $stmtStatus = $conn->prepare("SELECT status FROM alunos WHERE id = ?");
            $stmtStatus->bind_param("i", $aluno_id);
            $stmtStatus->execute();
            $stmtStatus->bind_result($statusAtual);
            $stmtStatus->fetch();
            $stmtStatus->close();

            if ($statusAtual === 'retirou') {
                // Devolução
                $stmt = $conn->prepare("UPDATE alunos SET status = 'devolveu', horario_devolucao = ?, codigo_computador = NULL WHERE id = ?");
                $stmt->bind_param("si", $horario, $aluno_id);
                $mensagem = "<p class='success-msg'>Devolução registrada com sucesso!</p>";
            } else {
                // Retirada (para status 'nao pegou' ou 'devolveu')
                $stmt = $conn->prepare("UPDATE alunos SET codigo_computador = ?, status = 'retirou', horario_retirada = ?, horario_devolucao = NULL WHERE id = ?");
                $stmt->bind_param("ssi", $codigoComputador, $horario, $aluno_id);
                $mensagem = "<p class='success-msg'>Retirada registrada com sucesso!</p>";
            }

            if (!$stmt->execute()) {
                $mensagem = "<p class='error-msg'>Erro: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
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

        .fake-recaptcha {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #d3d3d3;
            border-radius: 5px;
            padding: 12px 16px;
            background-color: #f9f9f9;
            width: 100%;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }

        .fake-recaptcha input[type="checkbox"] {
            display: none;
        }

        .fake-recaptcha label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .checkmark {
            height: 20px;
            width: 20px;
            background-color: #fff;
            border: 2px solid #ccc;
            margin-right: 10px;
            position: relative;
            border-radius: 2px;
        }

        .fake-recaptcha input[type="checkbox"]:checked + label .checkmark {
            background-color: #4285f4;
            border-color: #4285f4;
        }

        .fake-recaptcha input[type="checkbox"]:checked + label .checkmark::after {
            content: "";
            position: absolute;
            left: 5px;
            top: 1px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .recaptcha-text {
            font-size: 15px;
            color: #555;
        }

        .recaptcha-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 10px;
            color: #666;
        }

        .recaptcha-logo img {
            width: 30px;
            height: 30px;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Registro de Código do Computador</h2>

        <label for="codigo_computador">Código do Computador:</label>
        <input type="text" name="codigo_computador" required><br><br>

        <!-- Checkbox estilo reCAPTCHA -->
        <div class="fake-recaptcha">
            <input type="checkbox" id="digitalCheck" name="digitalCheck">
            <label for="digitalCheck">
                <span class="checkmark"></span>
                <span class="recaptcha-text">Confirmo que já passei minha digital</span>
            </label>
        </div>

        <input type="submit" value="Registrar Código" class="submit-btn" id="entrarBtn" disabled>

        <?php echo $mensagem; ?>
    </form>

    <script>
        const checkbox = document.getElementById('digitalCheck');
        const btn = document.getElementById('entrarBtn');

        checkbox.addEventListener('change', () => {
            btn.disabled = !checkbox.checked;
        });
    </script>
</body>
</html>
