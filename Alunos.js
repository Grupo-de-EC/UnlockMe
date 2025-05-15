document.addEventListener("DOMContentLoaded", () => {
    const alerta = document.getElementById("cadastrando-msg");

    setInterval(() => {
        fetch('/api/status')
            .then(res => res.json())
            .then(data => {
                if (data.cadastrando) {
                    alerta.style.display = "block";
                    setTimeout(() => alerta.style.display = "none", 3000); // Esconde após 3s
                }
            })
            .catch(err => console.error('Erro ao verificar status:', err));
    }, 1000); // Verifica a cada segundo
});
