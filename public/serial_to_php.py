import serial
import requests
import time

# === CONFIGURAÇÕES ===
PORTA_SERIAL = 'COM6'        # Altere para a porta correta do seu ESP32
VELOCIDADE = 115200
URL_API = 'http://localhost/UnlockMe/public/api/salvarBiometria.php'

# === ABRIR SERIAL SEM RESETAR O ESP32 ===
ser = serial.Serial(PORTA_SERIAL, VELOCIDADE, timeout=1)
ser.dtr = False
ser.rts = False
time.sleep(2)  # Tempo para estabilizar conexão

# === LIMPA QUALQUER DADO ANTERIOR ===
ser.flushInput()

print("📡 Aguardando dados do ESP32...\n")

while True:
    try:
        linha = ser.readline().decode('utf-8', errors='ignore').strip()
        if linha:
            print(f"[Recebido] {linha}")
            
            if linha.startswith("DIGITAL_CADASTRADA_ID:"):
                id_digital = linha.split(":")[1].strip()
                print(f"📤 Enviando ID {id_digital} para o servidor...")

                # Envia o ID para o PHP
                try:
                    response = requests.post(URL_API, data={"id": id_digital})
                    print(f"✅ Resposta do servidor: {response.status_code} - {response.text}")
                except requests.RequestException as e:
                    print(f"❌ Erro ao enviar para o servidor: {e}")

    except KeyboardInterrupt:
        print("\n🛑 Encerrando leitura serial.")
        break
    except Exception as e:
        print(f"⚠️ Erro inesperado: {e}")
