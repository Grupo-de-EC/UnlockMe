import serial
import requests
import time

# Configure a porta serial do ESP32
SERIAL_PORT = 'COM3'  # Exemplo Windows, ou '/dev/ttyUSB0' no Linux/Mac
BAUD_RATE = 115200

# URL da API PHP que salva biometria
API_URL = 'http://localhost/api/salvarBiometria.php'

def main():
    try:
        ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
        print(f'Conectado à porta serial {SERIAL_PORT}')
    except Exception as e:
        print(f'Erro ao abrir porta serial: {e}')
        return

    buffer = []
    lendo_template = False

    while True:
        try:
            linha = ser.readline().decode('utf-8').strip()
            if not linha:
                continue

            print(f'Recebido: {linha}')

            if linha == "INICIO_TEMPLATE":
                buffer = []
                lendo_template = True
            elif linha == "FIM_TEMPLATE":
                lendo_template = False

                aluno_id = None
                template = None

                for bline in buffer:
                    if bline.startswith("ALUNO_ID:"):
                        aluno_id = bline.split(":",1)[1].strip()
                    elif bline.startswith("TEMPLATE:"):
                        template = bline.split(":",1)[1].strip()

                if aluno_id and template:
                    print(f'Enviando biometria do aluno {aluno_id} para o servidor...')
                    resp = requests.post(API_URL, json={
                        'aluno_id': aluno_id,
                        'template': template
                    })
                    if resp.status_code == 200:
                        resposta = resp.json()
                        if resposta.get('status') == 'ok':
                            ser.write(b'SALVAR\n')
                            print('Resposta OK, enviado SALVAR para ESP32')
                        else:
                            ser.write(b'DESCARTAR\n')
                            print(f'Erro no servidor: {resposta.get("mensagem")}')
                    else:
                        ser.write(b'DESCARTAR\n')
                        print(f'Erro HTTP: {resp.status_code}')
                else:
                    ser.write(b'DESCARTAR\n')
                    print('Dados incompletos para envio, descartando.')

            elif lendo_template:
                buffer.append(linha)

        except Exception as e:
            print(f'Erro no loop: {e}')
            time.sleep(1)

if __name__ == "__main__":
    main()
