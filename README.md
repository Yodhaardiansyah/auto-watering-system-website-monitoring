# Smart Auto Watering System with Web Monitoring 🌱💧

An IoT-based plant watering system that reads soil moisture and automatically waters the plant, while sending real-time data to a custom-built web dashboard (no framework required).

## 🌟 Features

- 🌱 Soil moisture sensing with analog sensor
- 💧 Automatic water pump control using relay
- 🌐 Send data to website via HTTP (POST/GET)
- 📊 View current status on simple web dashboard
- 📅 Optional logging for moisture history

## ⚙️ System Overview

![ChatGPT Image 26 Jun 2025, 07 33 46](https://github.com/user-attachments/assets/dcb944f1-ac02-4cae-8ea8-ab09b7b4b70a)

## 🔧 Components

| Component         | Description                   |
|-------------------|-------------------------------|
| ESP32 / ESP8266   | Microcontroller (WiFi + Logic)|
| Soil Moisture     | Analog moisture sensor        |
| Relay Module      | Controls pump                 |
| PHP Web Server    | Receives and stores data      |
| HTML Dashboard    | Displays real-time moisture   |

## 🚀 Getting Started

### 1. Hardware Wiring

- Soil Sensor → Analog pin (e.g., A0)
- Relay → Digital output (e.g., D5 / GPIO5)
- Pump → Connected to relay output
- ESP32/8266 → Connect to WiFi

### 2. ESP Firmware

- Read sensor value
- Send to server every X seconds using `HTTPClient`
- Example ESP code:
```cpp
HTTPClient http;
http.begin("http://your-server.com/submit.php?moisture=345");
int httpCode = http.GET();
3. Server Setup
Deploy submit.php to your server:

php
Salin
Edit
<?php
$moisture = $_GET['moisture'];
file_put_contents("data.txt", $moisture);
?>
Display value in index.html or dashboard.php:

php
Salin
Edit
<?php echo file_get_contents("data.txt"); ?>%
4. Directory Structure
bash
Salin
Edit
auto-watering/
├── submit.php         # Endpoint to receive data
├── data.txt           # Latest moisture value
└── index.html         # Simple dashboard
🧪 Example Data Flow
ESP reads 345 from sensor

Sends to: http://your-ip/submit.php?moisture=345

data.txt updated

Web dashboard reads and displays it

📸 Optional Screenshots
Add screenshot of your system and web dashboard here.

📜 License
MIT License

👨‍💻 Author
Yodha Ardiansyah
