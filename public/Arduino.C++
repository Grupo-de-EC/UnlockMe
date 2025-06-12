#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>
#include <HardwareSerial.h>

// Altere os pinos conforme necessário
#define RXD2 16  // Pino RX do sensor (TX do ESP32)
#define TXD2 17  // Pino TX do sensor (RX do ESP32)

const char* ssid = "SEU_WIFI";
const char* password = "SUA_SENHA";
const char* servidor = "http://SEU_SERVIDOR/registrar_digital.php";  // Altere para seu servidor PHP

const int aluno_id = 42;  // Defina dinamicamente se quiser

HardwareSerial mySerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);

void setup() {
  Serial.begin(115200);
  delay(1000);

  WiFi.begin(ssid, password);
  Serial.print("Conectando ao WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi conectado!");

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
    Serial.println("Digital capturada com sucesso. Enviando...");
    enviarParaServidor();
  } else {
    Serial.println("Erro ao capturar digital.");
  }

  delay(10000); // Aguarda 10s antes da próxima tentativa
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

void enviarParaServidor() {
  // Coleta os dados do buffer
  uint8_t packet[512];
  finger.getModel();
  memcpy(packet, finger.templates[0], 512); // Copia os dados do modelo

  // Converte para string hexadecimal
  String hexTemplate = "";
  for (int i = 0; i < 512; i++) {
    if (packet[i] < 16) hexTemplate += "0";
    hexTemplate += String(packet[i], HEX);
  }

  // Envia para o servidor
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(servidor);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{\"aluno_id\": " + String(aluno_id) + ", \"digital\": \"" + hexTemplate + "\"}";
    int responseCode = http.POST(jsonPayload);

    if (responseCode > 0) {
      String resposta = http.getString();
      Serial.println("Resposta do servidor:");
      Serial.println(resposta);
    } else {
      Serial.print("Erro HTTP: ");
      Serial.println(responseCode);
    }

    http.end();
  } else {
    Serial.println("WiFi desconectado.");
  }
}
