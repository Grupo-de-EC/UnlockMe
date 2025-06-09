#include <Adafruit_Fingerprint.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <Wire.h>
#include <HardwareSerial.h>

// ==== Display OLED ====
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_RESET -1
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

// ==== Leitor biométrico ====
HardwareSerial mySerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);
uint8_t id;

// ==== Função para mostrar mensagens ====
void showMessage(String message, int delayTime = 0) {
  Serial.println(message);
  display.clearDisplay();
  display.setTextSize(2);
  display.setTextColor(WHITE);

  int y = 0;
  int lineHeight = 16;
  int start = 0;
  while (start < message.length()) {
    int end = message.indexOf('\n', start);
    if (end == -1) end = message.length();
    String line = message.substring(start, end);
    display.setCursor(0, y);
    display.println(line);
    y += lineHeight;
    start = end + 1;
  }

  display.display();
  if (delayTime > 0) delay(delayTime);
}

void setup() {
  Serial.begin(57600);
  mySerial.begin(57600, SERIAL_8N1, 16, 17); // RX=16, TX=17
  delay(100);

  // Inicializa o display
  if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println("Falha ao iniciar o display OLED");
    while (true);
  }
  display.clearDisplay();
  display.display();

  showMessage("Iniciando...", 1000);

  // Inicializa o sensor biométrico
  finger.begin(57600);
  delay(5);

  if (finger.verifyPassword()) {
    showMessage("Sensor\nConectado", 1000);
  } else {
    showMessage("Sensor\nNAO\nDetectado", 5000);
    while (1);
  }

  finger.getTemplateCount();
  if (finger.templateCount == 0) {
    showMessage("Nenhuma\ndigital\ncadastrada", 2000);
  } else {
    showMessage("Digitais\ncadastradas:\n" + String(finger.templateCount), 2000);
  }

  showMessage("Digite 1\nou 2 no\nSerial", 2000);
}

uint8_t readnumber() {
  uint8_t num = 0;
  while (num == 0) {
    while (!Serial.available());
    num = Serial.parseInt();
  }
  return num;
}

void loop() {
  if (Serial.available() > 0) {
    char comando = Serial.read();

    if (comando == '1') {
      showMessage("Modo\nCadastro", 1000);
      Serial.println("Coloque o ID de 1 a 127...");
      id = readnumber();
      if (id == 0) {
        showMessage("ID\nInvalido", 1000);
        return;
      }
      showMessage("Cadastrando\nID: " + String(id), 1000);
      while (!getFingerprintEnroll());
      showMessage("Cadastro\nConcluido!", 2000);
    } else if (comando == '2') {
      showMessage("Modo\nAnalise", 1000);
      showMessage("Coloque o\ndedo...", 2000);
      int resultado = getFingerprintID();
      if (resultado >= 1) {
        showMessage("Digital\nValida", 2000);
      } else {
        showMessage("Digital\nInexistente", 2000);
      }
    } else {
      showMessage("Comando\nInvalido", 1500);
    }

    Serial.println("Aguardando novo comando:");
    showMessage("Digite 1\nou 2 no\nSerial", 2000);
  }
}

// ==== Função de Cadastro ====
uint8_t getFingerprintEnroll() {
  int p = -1;
  showMessage("Aguardando\ndedo...", 1000);
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) {
      delay(100);
      continue;
    } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
      showMessage("Erro de\ncomunicacao", 1000);
      continue;
    } else if (p == FINGERPRINT_IMAGEFAIL) {
      showMessage("Erro na\nimagem", 1000);
      continue;
    }
  }

  p = finger.image2Tz(1);
  if (p != FINGERPRINT_OK) return p;

  showMessage("Remova\no dedo", 1500);
  delay(2000);
  while (finger.getImage() != FINGERPRINT_NOFINGER);

  showMessage("Coloque o\nmesmo dedo", 1500);
  while (finger.getImage() != FINGERPRINT_OK);
  p = finger.image2Tz(2);
  if (p != FINGERPRINT_OK) return p;

  p = finger.createModel();
  if (p != FINGERPRINT_OK) return p;

  p = finger.storeModel(id);
  return (p == FINGERPRINT_OK);
}

// ==== Função de Análise ====
uint8_t getFingerprintID() {
  Serial.println("Iniciando análise...");
  int tentativas = 50;
  uint8_t p = 0;
  while (tentativas--) {
    p = finger.getImage();
    if (p == FINGERPRINT_OK) break;
    delay(100);
  }

  if (p != FINGERPRINT_OK) {
    Serial.println("Falha ao capturar imagem.");
    return 0;
  }

  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) {
    Serial.println("Falha ao converter imagem.");
    return 0;
  }

  p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) {
    Serial.print("ID Encontrado: ");
    Serial.print(finger.fingerID);
    Serial.print(" | Confiança: ");
    Serial.println(finger.confidence);
    return finger.fingerID;
  } else {
    Serial.println("Digital não encontrada.");
    return 0;
  }
}