<?php
include('db.php');

function gerarCodigoAleatorio(int $tamanho = 6): string {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $tamanhoCaracteres = strlen($caracteres);
    $codigo = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $codigo .= $caracteres[rand(0, $tamanhoCaracteres - 1)];
    }
    return $codigo;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $capacidade = $_POST['capacidade'] ?? '';
    $codigo = $_POST['codigo'] ?? gerarCodigoAleatorio();

    if (empty($nome) || empty($capacidade)) {
        echo 'Dados incompletos';
        exit;
    }
    if (!is_numeric($capacidade) || $capacidade <= 0) {
        echo 'Capacidade inválida';
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO salas (nome, capacidade, codigo) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo 'Erro ao preparar a consulta: ' . $conn->error;
        exit;
    }

    $stmt->bind_param("sis", $nome, $capacidade, $codigo);

    if ($stmt->execute()) {
        echo 'Sala adicionada com sucesso!';
    } else {
        echo 'Erro ao inserir a sala: ' . $stmt->error;
    }

    $stmt->close();
} else {
    echo 'Método HTTP inválido';
}
?>