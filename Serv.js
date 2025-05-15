const express = require('express');
const bodyParser = require('body-parser');
const app = express();
const port = 3000;

// Variável para sinalizar quando o ESP32 requisitar o cadastro
let cadastrando = false;

// Middleware
app.use(bodyParser.json());
app.use(express.static('public')); // Serve arquivos estáticos da pasta public

// Requisição do ESP32 para iniciar o cadastro
app.post('/api/cadastrar', (req, res) => {
    console.log('ESP32 solicitou cadastro!');
    cadastrando = true; // Ativa o alerta
    res.status(200).send('Requisição de cadastro recebida.');
});

// Endpoint para o frontend checar o status
app.get('/api/status', (req, res) => {
    res.json({ cadastrando });

    // Reseta a flag após o frontend pegar
    if (cadastrando) {
        cadastrando = false;
    }
});

// Inicia o servidor
app.listen(port, () => {
    console.log(`Servidor rodando em http://localhost:${port}`);
});
