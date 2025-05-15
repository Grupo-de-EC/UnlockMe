const express = require('express');
const bodyParser = require('body-parser');
const app = express();
const port = 3000;

let alunos = [
    { id: 1, nome: 'Aluno 1', retirou: false, devolveu: false },
    { id: 2, nome: 'Aluno 2', retirou: false, devolveu: false },
];

app.use(bodyParser.json());
app.use(express.static('public'));

// API para pegar o checklist de alunos
app.get('/api/checklist', (req, res) => {
    res.json(alunos);
});

// API para atualizar o status de retirada ou devolução
app.post('/api/update', (req, res) => {
    const { alunoId, retirou, devolveu } = req.body;
    const aluno = alunos.find(a => a.id === alunoId);

    if (aluno) {
        aluno.retirou = retirou;
        aluno.devolveu = devolveu;
        res.status(200).send('Status atualizado');
    } else {
        res.status(404).send('Aluno não encontrado');
    }
});

app.listen(port, () => {
    console.log(`Servidor rodando na porta ${port}`);
});
