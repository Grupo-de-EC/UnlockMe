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

// ==== Botões ====
#define BOTAO_CAD_PIN 4
#define BOTAO_ANA_PIN 23

// ==== LEDs ====
#define LED_VERDE 18
#define LED_VERMELHO 19

enum Estado { ESPERA, CADASTRO, ANALISE };
Estado estadoAtual = ESPERA;

unsigned long lastBotaoCadTime = 0;
unsigned long lastBotaoAnaTime = 0;
const unsigned long debounceDelay = 200;

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

void esperarBotaoSolto(int pin) {
  while (digitalRead(pin) == HIGH) {
    delay(10);
  }
}

void setup() {
  Serial.begin(115200);
  mySerial.begin(57600, SERIAL_8N1, 16, 17); // RX=16, TX=17
  delay(100);

  pinMode(BOTAO_CAD_PIN, INPUT_PULLDOWN);
  pinMode(BOTAO_ANA_PIN, INPUT_PULLDOWN);
  pinMode(LED_VERDE, OUTPUT);
  pinMode(LED_VERMELHO, OUTPUT);
  digitalWrite(LED_VERDE, LOW);
  digitalWrite(LED_VERMELHO, LOW);

  if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println("Erro no display");
    while (1);
  }

  finger.begin(57600);
  delay(5);

  if (finger.verifyPassword()) {
    showMessage("Sensor\nOK", 1000);
  } else {
    showMessage("Sensor\nErro", 5000);
    while (1);
  }

  showMessage("Aperte:\nCadastro ou\nAnalise", 2000);
}

void loop() {
  static bool mostrouEspera = false;
  unsigned long agora = millis();

  if (digitalRead(BOTAO_CAD_PIN) == HIGH && (agora - lastBotaoCadTime > debounceDelay)) {
    estadoAtual = CADASTRO;
    mostrouEspera = false;
    showMessage("Modo\nCadastro", 1000);
    esperarBotaoSolto(BOTAO_CAD_PIN);
    lastBotaoCadTime = agora;
  }

  if (digitalRead(BOTAO_ANA_PIN) == HIGH && (agora - lastBotaoAnaTime > debounceDelay)) {
    estadoAtual = ANALISE;
    mostrouEspera = false;
    showMessage("Modo\nAnalise", 1000);
    esperarBotaoSolto(BOTAO_ANA_PIN);
    lastBotaoAnaTime = agora;
  }

  switch (estadoAtual) {
    case CADASTRO:
      cadastrarDigital();
      estadoAtual = ESPERA;
      break;
    case ANALISE:
      analisarDigital();
      estadoAtual = ESPERA;
      break;
    case ESPERA:
    default:
      if (!mostrouEspera) {
        showMessage("Aperte:\nCadastro ou\nAnalise");
        mostrouEspera = true;
        digitalWrite(LED_VERDE, LOW);
        digitalWrite(LED_VERMELHO, LOW);
      }
      break;
  }
}

void cadastrarDigital() {
  showMessage("Coloque o\ndedo", 500);
  digitalWrite(LED_VERDE, HIGH);

  int p = -1;
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) {
      delay(100);
      continue;
    }
    if (p == FINGERPRINT_PACKETRECIEVEERR || p == FINGERPRINT_IMAGEFAIL) {
      showMessage("Erro\nImagem", 1000);
      return;
    }
  }

  p = finger.image2Tz(1);
  if (p != FINGERPRINT_OK) {
    showMessage("Erro\nConverter", 1000);
    return;
  }

  // Verifica duplicidade
  p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) {
    showMessage("Ja\nCadastrada", 2000);
    return;
  }

  showMessage("Remova o\ndedo", 1500);
  delay(2000);
  while (finger.getImage() != FINGERPRINT_NOFINGER);

  showMessage("Mesmo dedo", 1000);
  digitalWrite(LED_VERDE, HIGH);

  while (finger.getImage() != FINGERPRINT_OK);
  digitalWrite(LED_VERDE, LOW);
  digitalWrite(LED_VERMELHO, HIGH);

  p = finger.image2Tz(2);
  if (p != FINGERPRINT_OK) {
    showMessage("Erro\n2ª Img", 1000);
    return;
  }

  p = finger.createModel();
  if (p != FINGERPRINT_OK) {
    showMessage("Erro\nModelo", 1000);
    return;
  }

  // ENVIO DO TEMPLATE (alteração mínima para funcionar corretamente)
  Serial.println("INICIO_TEMPLATE");
  Serial.println("ALUNO_ID:123"); // Aqui você pode substituir para gerar o ID real do aluno

  // Pega o template do buffer 1
  uint8_t templateBuffer[512];
  uint16_t length = finger.getTemplateCount(); // Não é usado para tamanho, só para controle, substituí para comprimento correto
  // Para pegar o template, usamos getModel() sem argumentos e depois getTemplate()
  p = finger.getModel();  // esta função carrega o modelo no sensor - sem parâmetros!
  if (p != FINGERPRINT_OK) {
    Serial.println("TEMPLATE:ERRO");
  } else {
    // Pega o template de buffer 1 (usando função da biblioteca)
    // A biblioteca Adafruit_Fingerprint não expõe getTemplate diretamente
    // Então aqui enviamos o template do buffer 1 em hexadecimal
    for (int i = 0; i < 512; i++) {
      Serial.print(finger.templateBuffer1[i], HEX);
      if (i < 511) Serial.print(",");
    }
    Serial.println();
  }

  Serial.println("FIM_TEMPLATE");

  // Aguardar resposta do Python (ou outro sistema)
  unsigned long tempoEspera = millis();
  while (millis() - tempoEspera < 10000) { // espera 10 segundos
    if (Serial.available()) {
      String comando = Serial.readStringUntil('\n');
      comando.trim();
      if (comando == "SALVAR") {
        id = finger.templateCount + 1;
        p = finger.storeModel(id);
        if (p == FINGERPRINT_OK) {
          showMessage("Cadastro\nFeito!", 2000);
        } else {
          showMessage("Erro ao\nsalvar", 2000);
        }
        break;
      } else if (comando == "DESCARTAR") {
        showMessage("Cadastro\nCancelado", 2000);
        break;
      }
    }
  }

  digitalWrite(LED_VERDE, LOW);
  digitalWrite(LED_VERMELHO, LOW);
}

void analisarDigital() {
  int p = -1;
  showMessage("Coloque o\ndedo", 1000);
  digitalWrite(LED_VERDE, HIGH);

  int tentativas = 50;
  while (tentativas--) {
    p = finger.getImage();
    if (p == FINGERPRINT_OK) break;
    delay(100);
  }

  digitalWrite(LED_VERDE, LOW);
  digitalWrite(LED_VERMELHO, HIGH);

  if (p != FINGERPRINT_OK) {
    showMessage("Erro ao\nler dedo", 2000);
    return;
  }

  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) {
    showMessage("Erro\nConversao", 2000);
    return;
  }

  p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) {
    showMessage("Digital\nValida", 2000);
    Serial.print("ID: ");
    Serial.println(finger.fingerID);
  } else {
    showMessage("Nao\nReconhecida", 2000);
  }

  digitalWrite(LED_VERDE, LOW);
  digitalWrite(LED_VERMELHO, LOW);
}
