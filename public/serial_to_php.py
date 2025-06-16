import serial
import requests
import time

# Configura a porta serial e a velocidade
ser = serial.Serial('COM6', 115200, timeout=1)  # Altere COM4 se necessário
time.sleep(2)

def obter_ultimo_aluno_id():
    try:
        resp = requests.get("http://localhost/api/biometria.php")
        if resp.status_code == 200:
            dados = resp.json()
            return dados.get("aluno_id")
    except Exception as e:
        print(f"[Erro ao buscar aluno] {e}")
    return None

while True:
    try:
        # Corrigido: ignora erros de caracteres inválidos
        linha = ser.readline().decode('utf-8', errors='ignore').strip()

        if not linha:
            continue

        print(f"[Recebido] {linha}")

        if linha.startswith("BIOMETRIA_ID:"):
            digital = linha.split(":")[1].strip()
            aluno_id = obter_ultimo_aluno_id()

            if aluno_id and biometria_id:
                payload = {
                    "aluno_id": aluno_id,
                    "digital": digital
                }

                resp = requests.post("http://localhost/registrar_digital.php", json=payload)

                if resp.status_code == 200:
                    print("[Sucesso] Biometria enviada com sucesso.")
                    print(resp.text)
                else:
                    print(f"[Erro HTTP {resp.status_code}] {resp.text}")

    except Exception as e:
        print(f"[Erro geral] {e}")
