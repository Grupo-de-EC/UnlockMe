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

setInterval(() => {
    fetch('/api/status')
      .then(res => res.json())
      .then(data => {
        if (data.cadastrando) {
          alert('ESP32 solicitou cadastro!');
          // ou mostre um popup, uma animação, etc.
        }
      })
      .catch(err => console.error('Erro ao checar status:', err));
  }, 2000);
