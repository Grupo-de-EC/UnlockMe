<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Aluno</title>
    <link rel="stylesheet" href="formCadastroAluno.css">
</head>
<body>
    <form id="cadastroForm">
        <h2>Cadastro de Aluno</h2>
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required><br><br>

        <label for="codigo">Código da Sala:</label>
        <input type="text" name="codigo" id="codigo" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>

    <p id="mensagem"></p>

    <script>
        const form = document.getElementById("cadastroForm");
        const mensagem = document.getElementById("mensagem");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const dados = {
                nome: nome.value,
                senha: senha.value,
                codigo: codigo.value
            };

            const res = await fetch("addAluno.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(dados)
            });

            const result = await res.json();

            if (result.status === "aguardando_biometria") {
                mensagem.innerText = "Cadastre sua biometria no leitor...";
                aguardarBiometria(result.aluno_id);
            } else {
                mensagem.innerText = "Erro: " + result.mensagem;
            }
        });

        async function aguardarBiometria(alunoId) {
            let tentativas = 0;

            const intervalo = setInterval(async () => {
                const res = await fetch(`/api/biometria?id=${alunoId}`);
                const data = await res.json();

                if (data.biometria && data.status === "ok") {
                    clearInterval(intervalo);
                    mensagem.innerText = "Cadastro completo com sucesso!";
                    setTimeout(() => window.location.href = "login.php", 2000);
                }

                if (++tentativas >= 20) {
                    clearInterval(intervalo);
                    mensagem.innerText = "Tempo de biometria esgotado.";
                }
            }, 1000);
        }
    </script>
</body>
</html>
