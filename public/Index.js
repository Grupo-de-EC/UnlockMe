document.addEventListener('DOMContentLoaded', function() {
    const salasLink = document.getElementById('salas-link');
    const alunosLink = document.getElementById('alunos-link');
    const salasSection = document.getElementById('salas');
    const alunosSection = document.getElementById('alunos');

    // Exibir salas e esconder alunos
    salasLink.addEventListener('click', function() {
        salasSection.style.display = 'block';
        alunosSection.style.display = 'none';
        salasLink.style.fontWeight = 'bold';
        alunosLink.style.fontWeight = 'normal';
    });

    // Exibir alunos e esconder salas
    alunosLink.addEventListener('click', function() {
        salasSection.style.display = 'none';
        alunosSection.style.display = 'block';
        salasLink.style.fontWeight = 'normal';
        alunosLink.style.fontWeight = 'bold';
        loadAlunos();
    });

    // Função para carregar a lista de alunos
    function loadAlunos() {
        fetch('/api/checklist')
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#checklist tbody');
                tbody.innerHTML = ''; // Limpa os dados existentes
                data.forEach(aluno => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${aluno.nome}</td>
                        <td>${aluno.retirou ? 'Sim' : 'Não'}</td>
                        <td>${aluno.devolveu ? 'Sim' : 'Não'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => console.error('Erro ao carregar dados:', error));
    }

    // Carregar as salas inicialmente
    salasLink.click();
});
