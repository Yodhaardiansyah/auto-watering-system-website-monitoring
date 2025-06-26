# Smart Auto Watering System with Web Monitoring 🌱💧

An IoT-powered automatic plant watering system with real-time web-based monitoring. Built using ESP microcontrollers and a PHP/Laravel backend dashboard.

## 🌟 Features

- 🌡️ Soil moisture sensing
- 💧 Automated watering control
- 🌐 Real-time monitoring via website
- 📊 Moisture level logging and display
- 🧑‍🌾 Admin interface for manual override and status check

## 🔧 Technologies Used

| Component     | Description                            |
|---------------|----------------------------------------|
| ESP8266/ESP32 | IoT microcontroller                    |
| Soil Sensor   | Reads soil moisture                    |
| Relay Module  | Controls water pump/valve              |
| Laravel       | Backend framework for web dashboard    |
| MySQL         | Stores sensor data & control logs      |
| HTML/CSS      | Frontend UI                            |

## 🖥 Web Dashboard

The dashboard displays:
- Current moisture level 🌡️
- Last watering time ⏰
- Manual pump control 💧
- Historical chart (if implemented)

## ⚙️ System Architecture

[Soil Moisture Sensor]
↓
[ESP32]
(read sensor & send data)
↓
[Laravel Server]
(receive/store/display data)
↓
[Website Dashboard]

bash
Salin
Edit

## 🚀 Getting Started

### Microcontroller Setup

1. Connect soil moisture sensor to analog input
2. Connect relay module to digital output (to control pump)
3. Upload firmware with:
   - WiFi credentials
   - Server endpoint URL

### Laravel Web Setup

```bash
git clone https://github.com/Yodhaardiansyah/auto-watering-system-website-monitoring.git
cd auto-watering-system-website-monitoring
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
Database Configuration
Update .env file with correct DB credentials

Add route to receive moisture data via HTTP POST

🧪 API Example
http
Salin
Edit
POST /api/moisture
Content-Type: application/json

{
  "device_id": "esp32-01",
  "moisture": 47
}
📸 Screenshots
Tambahkan tangkapan layar dari web monitoring di sini!

📜 License
MIT License

👨‍💻 Author
Yodha Ardiansyah
