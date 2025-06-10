<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Aluno</title>
    <link rel="stylesheet" href="formCadastroAluno.css">
</head>
<body>
    <form action="addAluno.php" method="POST">
        <h2>Cadastro de Aluno</h2>
        <label for="nome">Nome:</label>
        <input type="text" name="nome" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" required><br><br>

        <label for="codigo">Código da Sala:</label>
        <input type="text" name="codigo" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>
