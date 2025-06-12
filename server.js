const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');  // Permite conexões entre domínios
const app = express();
const port = 3000;

let cadastrando = false;

app.use(cors()); // Permite que o frontend acesse a API
app.use(bodyParser.json());
app.use(express.static('public'));

app.post('/api/cadastrar', (req, res) => {
    console.log('ESP32 solicitou cadastro!');
    cadastrando = true;
    res.status(200).send('Requisição recebida');
});

app.get('/api/status', (req, res) => {
    res.json({ cadastrando });
    cadastrando = false;
});

app.listen(port, () => {
    console.log(`Servidor rodando em http://localhost:${port}`);
});
