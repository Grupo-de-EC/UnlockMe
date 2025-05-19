#include <WiFi.h>
#include <HTTPClient.h>

// Defina sua rede Wi-Fi
const char* ssid = "Seu_SSID";
const char* password = "Sua_Senha";

const int buttonPin = 12;  // Pin onde o botão está conectado

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Conectando à Wi-Fi...");
  }
  Serial.println("Conectado à Wi-Fi!");
  pinMode(buttonPin, INPUT_PULLUP);
}

void loop() {
  if (digitalRead(buttonPin) == LOW) {
    sendHTTPRequest();
    delay(1000);
  }
}

void sendHTTPRequest() {
  HTTPClient http;
  String url = "http://192.168.x.x:3000/api/cadastrar";  // IP do seu computador na rede, O URL do seu servidor Express

  // Fazendo uma requisição POST
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  
  String payload = "{\"acao\": \"cadastrar\"}";
  int httpResponseCode = http.POST(payload);

  if (httpResponseCode > 0) {
    Serial.println("Requisição enviada com sucesso");
  } else {
    Serial.println("Erro ao enviar requisição");
  }

  http.end();
}
