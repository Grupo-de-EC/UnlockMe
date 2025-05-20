document.getElementById('formSala').addEventListener('submit', function (e) {
    e.preventDefault();

    const nome = document.getElementById('nome').value;
    const capacidade = document.getElementById('capacidade').value;
    const status = document.getElementById('status').value;

    fetch('adicionar_sala.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `nome=${nome}&capacidade=${capacidade}&status=${status}`
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'OK') {
            window.location.reload();
        } else {
            alert('Erro ao adicionar sala: ' + data);
        }
    });
});
