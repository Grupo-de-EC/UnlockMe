#include <Adafruit_Fingerprint.h>
#include <HardwareSerial.h>

// Pinos do sensor (RX do ESP32 ligado ao TX do sensor e vice-versa)
#define RXD2 16
#define TXD2 17

HardwareSerial mySerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);

const int aluno_id = 42;  // Id fixo, pode ser alterado conforme sua lógica

void setup() {
  Serial.begin(115200);         // Serial USB
  delay(1000);
  Serial.println("Setup iniciado");

  mySerial.begin(57600, SERIAL_8N1, RXD2, TXD2);
  delay(1000);

  if (!finger.begin()) {
    Serial.println("Erro ao iniciar sensor AS608.");
    while (1) delay(1);
  }

  Serial.println("Sensor AS608 conectado.");
  finger.getTemplateCount();
  Serial.print("Templates armazenados: ");
  Serial.println(finger.templateCount);
}

void loop() {
  Serial.println("\nAguardando dedo no sensor...");
  if (capturarDigital()) {
    Serial.println("Digital capturada com sucesso. Enviando via USB...");
    enviarViaUSB();
  } else {
    Serial.println("Erro ao capturar digital.");
  }

  delay(10000);
}

bool capturarDigital() {
  int p = -1;
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) {
      delay(100);
      continue;
    } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
      Serial.println("Erro de pacote");
      return false;
    } else if (p != FINGERPRINT_OK) {
      Serial.println("Falha ao capturar imagem");
      return false;
    }
  }

  p = finger.image2Tz(1);
  if (p != FINGERPRINT_OK) {
    Serial.println("Falha ao converter imagem");
    return false;
  }

  return true;
}

void enviarViaUSB() {
  // Obtem o modelo digital capturado (template)
  int p = finger.getModel();
  if (p != FINGERPRINT_OK) {
    Serial.println("Falha ao obter modelo.");
    return;
  }

  // Nota: A biblioteca Adafruit_Fingerprint não disponibiliza diretamente
  // um buffer público com o template. Como alternativa, o exemplo original
  // tentava usar finger.templates[0], que não existe na biblioteca.
  // Para simplificar, vamos enviar apenas o status OK e o aluno_id.

  // Se você quiser enviar o template, será necessário modificar a biblioteca
  // para expor os dados, ou enviar os dados que o sensor retorna.

  // Por enquanto, enviando uma mensagem simples:

  Serial.print("Aluno ID: ");
  Serial.print(aluno_id);
  Serial.print(", Template capturado com sucesso.");

  // Se quiser enviar dados específicos, adapte conforme seu hardware/biblioteca
  Serial.println();
}
