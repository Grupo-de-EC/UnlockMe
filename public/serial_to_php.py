import serial
import requests
import time

# Configurações
PORTA_SERIAL = 'COM6'
VELOCIDADE = 115200
API_CADASTRO = 'http://localhost/UnlockMe/public/api/addAluno.php'
API_BIOMETRIA = 'http://localhost/UnlockMe/public/api/registrar_Biometria.php'

# Dados do aluno a serem cadastrados
ser = serial.Serial(PORTA_SERIAL, VELOCIDADE, timeout=1)
ser.dtr = False
ser.rts = False
time.sleep(2)
ser.flushInput()

# Envia o ID do aluno para o ESP32
ser.write(f"ALUNO_ID:{aluno_id}\n".encode())

print("Aguardando digital...")
# Cadastrar aluno e obter ID
res = requests.post(API_CADASTRO, json=dados_aluno)
resposta = res.json()

if resposta['status'] != 'aguardando_biometria':
    print("Erro ao cadastrar aluno:", resposta['mensagem'])
    exit()

aluno_id = resposta['aluno_id']
print("ID do aluno:", aluno_id)

# Aguardar digital do ESP32
ser = serial.Serial(PORTA_SERIAL, VELOCIDADE, timeout=1)
ser.dtr = False
ser.rts = False
time.sleep(2)
ser.flushInput()

print("Aguardando digital...")

template_recebido = ""

while True:
    linha = ser.readline().decode('utf-8', errors='ignore').strip()
    if linha:
        print(f"[ESP32] {linha}")

        if linha == "INICIO_TEMPLATE":
            template_recebido = ""
        elif linha == "FIM_TEMPLATE":
            break
        else:
            template_recebido += linha

# Enviar para o backend
dados_biometria = {
    "aluno_id": aluno_id,
    "biometria_id": template_recebido
}

res_bio = requests.post(API_BIOMETRIA, json=dados_biometria)
print("Resposta ao salvar biometria:", res_bio.text)
