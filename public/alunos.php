<?php
include('db.php');
$sala_id = isset($_GET['sala_id']) ? intval($_GET['sala_id']) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos - Controle de Retirada</title>
    <link rel="stylesheet" href="Index.css">
    <script defer src="alunos.js"></script>
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <h2>Controle de Retirada</h2>
            <ul>
                <li><a href="salas.php">Salas</a></li>
                <li><a href="configuracoes.html">Configurações</a></li>
            </ul>
        </nav>

        <!-- Conteúdo Principal -->
        <div class="content">
            <header>
                <h1>Alunos</h1>
            </header>

            <section>
                <p id="cadastrando-msg" style="display:none; color: red; font-weight: bold;">
                    Cadastrando...
                </p>
            </section>
            <section class="alunos-section">
                <h2>Lista de Alunos</h2>
                <table class="alunos-table" id="alunos-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Matricula</th>
                            <th>Status de Retirada</th>
                        </tr>
                    </thead>
                    <tbody id="alunos-tbody">
                        <?php
                        $query = "SELECT * FROM alunos WHERE sala_id = $sala_id";
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['nome']}</td>
                                    <td>{$row['digital']}</td>
                                    <td>{$row['status']}</td>
                                </tr>";
                        }
                        ?>
                    </tbody>

                </table>
            </section>

            <section class="add-aluno-section">
                <h2>Adicionar Novo Aluno</h2>
                <form id="add-aluno-form">
                    <input type="hidden" id="sala-id" name="sala-id" value="<?php echo $sala_id; ?>">

                    <label for="aluno-name">Nome do Aluno:</label>
                    <input type="text" id="aluno-name" name="aluno-name" placeholder="Digite o nome do aluno" required>

                    <label for="aluno-matricula">Matrícula:</label>
                    <input type="text" id="aluno-matricula" name="aluno-matricula" placeholder="Digite a matrícula" required>

                    <button type="submit" class="btn">Adicionar Aluno</button>
                </form>
                <p id="cadastrando-msg" style="display:none;">Cadastrando...</p>
            </section>
        </div>
    </div>

    <script src="alunos.js"></script>
</body>
</html>
