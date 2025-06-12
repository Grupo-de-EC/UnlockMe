<?php
include('db.php');


$sala_id = isset($_GET['sala_id']) ? intval($_GET['sala_id']) : 0;

$codigo_sala = '';
if ($sala_id > 0) {
    $stmt = $conn->prepare("SELECT codigo FROM salas WHERE id = ?");
    $stmt->bind_param("i", $sala_id);
    $stmt->execute();
    $stmt->bind_result($codigo_sala);
    $stmt->fetch();
    $stmt->close();
}
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
            </ul>
        </nav>

        <div class="content">
            <header>
                <h1>Alunos da Sala</h1>
            </header>

            <section>
                <p id="cadastrando-msg" style="display:none; color: red; font-weight: bold;">
                    Cadastrando...
                </p>
            </section>
            <section class="alunos-section">
                <h2>Lista de Alunos<?php if ($codigo_sala): ?> - Código: <span style="font-weight: normal;"><?= htmlspecialchars($codigo_sala) ?></span><?php endif; ?></h2>
                <table class="alunos-table" id="alunos-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Numero do Computador</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="alunos-tbody">
                        <?php
                        $query = "SELECT * FROM alunos WHERE sala_id = $sala_id";
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['nome']}</td>
                                    <td>{$row['codigo_computador']}</td>
                                    <td>{$row['status']}</td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <script src="alunos.js"></script>
</body>
</html>
