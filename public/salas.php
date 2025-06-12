<?php include('db.php');?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas - Controle de Retirada</title>
    <link rel="stylesheet" href="Index.css">
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
            <h1>Salas</h1>
        </header>
        <section class="salas-section">
            <h2>Adicionar Nova Sala</h2>
            <form id="formSala">
                <input type="text" id="nome" placeholder="Nome da sala" required>
                <input type="number" id="capacidade" placeholder="Capacidade" required>
                <button type="submit">Adicionar</button>
            </form>

            <h2>Lista de Salas</h2>
            <table class="salas-table">
                <thead>
                    <tr>
                        <th>Nome da Sala</th>
                        <th>Capacidade</th>
                    </tr>
                </thead>
                <tbody id="listaSalas">
                    <?php
                    $result = $conn->query("SELECT * FROM salas ORDER BY id DESC");
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td><a href='Alunos.php?sala_id={$row['id']}'>{$row['nome']}</a></td>
                                <td>{$row['capacidade']}</td>
                            </tr>";
                    }
                    ?>
                </tbody>

            </table>
        </section>
    </div>
</div>

<script src="salas.js"></script>
</body>
</html>
