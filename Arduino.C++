#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>

const char* ssid = "aaaaa";
const char* password = "1234";

WiFiClient client;
HTTPClient http;

Adafruit_Fingerprint finger = Adafruit_Fingerprint(&Serial2);

void setup() {
  Serial.begin(115200);
  Serial2.begin(57600, SERIAL_8N1, 16, 17);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Conectando ao WiFi...");
  }
  Serial.println("Conectado ao WiFi!");

  if (finger.begin()) {
    Serial.println("Sensor de impressão digital encontrado!");
  } else {
    Serial.println("Não foi possível encontrar o sensor de impressão digital");
    while (1);
  }
}

void loop() {
  int fingerprintID = getFingerprintID();
  if (fingerprintID != -1) {
    // Enviar dados para o servidor
    String url = "http://Serv.com";
    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");

    String payload = "{\"alunoId\": " + String(fingerprintID) + ", \"retirou\": true, \"devolveu\": false}";
    int httpCode = http.POST(payload);

    if (httpCode > 0) {
      String response = http.getString();
      Serial.println(response);
    } else {
      Serial.println("Erro ao enviar dados");
    }
    http.end();
  }
  delay(5000);
}

int getFingerprintID() {
  int fingerprintID = -1;
  if (finger.getImage() != FINGERPRINT_OK) return -1;
  if (finger.image2Tz() != FINGERPRINT_OK) return -1;
  if (finger.fingerSearch() != FINGERPRINT_OK) return -1;
  fingerprintID = finger.fingerID;
  return fingerprintID;
}
