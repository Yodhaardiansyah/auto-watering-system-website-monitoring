#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <DHT.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// WiFi Credentials
const char* ssid = "Udin Petot";
const char* password = "12345678910";

// Server URL
const char* serverName = "http://192.168.20.77:8000/submit.php"; // Ganti sesuai URL mu

// Pin Config
#define DHTPIN 32
#define DHTTYPE DHT11
#define SOIL_PIN 33
#define RELAY_PIN 15

// LCD I2C (alamat 0x27, ukuran 16x2)
LiquidCrystal_I2C lcd(0x27, 16, 2);

DHT dht(DHTPIN, DHTTYPE);

String mode = "otomatis";  // default mode
String relayStatus = "off"; // default relay

void setup() {
  Serial.begin(115200);
  delay(1000);

  // Init LCD
  Wire.begin(5, 18);  // SDA=GPIO5, SCL=GPIO18, sesuaikan jika berbeda
  lcd.init();
  lcd.backlight();
  lcd.clear();

  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, HIGH);  // Relay OFF (LOW = ON asumsi)

  dht.begin();

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");
}

void loop() {
  float suhu = dht.readTemperature();
  float lembab = dht.readHumidity();
  int soilRaw = analogRead(SOIL_PIN);

  int soilPercent = map(soilRaw, 4095, 1710, 0, 100);
  soilPercent = constrain(soilPercent, 0, 100);

  // Tampilkan dulu error jika sensor gagal
  if (isnan(suhu) || isnan(lembab)) {
    Serial.println("Failed to read from DHT sensor!");
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("Sensor DHT Error");
  } else {
    Serial.printf("Suhu: %.1f C, Lembab: %.1f %%, Soil: %d%%\n", suhu, lembab, soilPercent);
    
    if (WiFi.status() == WL_CONNECTED) {
      // Kirim data ke server
      HTTPClient http;
      String url = String(serverName) + "?suhu=" + String(suhu, 1) + "&lembab=" + String(lembab, 1) + "&soil=" + String(soilPercent);
      http.begin(url);
      int httpCode = http.GET();

      if (httpCode == 200) {
        String payload = http.getString();
        Serial.println("Response: " + payload);

        StaticJsonDocument<200> doc;
        DeserializationError error = deserializeJson(doc, payload);
        if (!error) {
          mode = doc["mode"].as<String>();
          relayStatus = doc["relay"].as<String>();

          Serial.println("Mode: " + mode + ", Relay: " + relayStatus);

          // Logika kontrol relay berdasarkan mode
          if (mode == "otomatis") {
            if (soilPercent < 60) {
              digitalWrite(RELAY_PIN, LOW); // Relay ON
              relayStatus = "on";
            } else {
              digitalWrite(RELAY_PIN, HIGH); // Relay OFF
              relayStatus = "off";
            }
          } else if (mode == "manual") {
            if (relayStatus == "on") {
              digitalWrite(RELAY_PIN, LOW); // Relay ON
            } else {
              digitalWrite(RELAY_PIN, HIGH); // Relay OFF
            }
          }
          
          // Update LCD
          lcd.clear();
          lcd.setCursor(0, 0);
          lcd.printf("T:%.1fC H:%.0f%%", suhu, lembab);
          lcd.setCursor(0, 1);
          char modeChar = (mode == "otomatis") ? 'O' : 'M';
          lcd.printf("S:%d%% M:%c R:%s", soilPercent, modeChar, relayStatus.c_str());


        } else {
          Serial.println("Gagal parsing JSON");
          lcd.clear();
          lcd.setCursor(0,0);
          lcd.print("JSON parse error");
        }
      } else {
        Serial.printf("Error HTTP code: %d\n", httpCode);
        lcd.clear();
        lcd.setCursor(0,0);
        lcd.print("HTTP error ");
        lcd.print(httpCode);
      }
      http.end();
    } else {
      Serial.println("WiFi tidak terhubung");
      lcd.clear();
      lcd.setCursor(0,0);
      lcd.print("WiFi not conn");
    }
  }

  delay(1000);
}
