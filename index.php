<?php
$statusFile = "status.json";
$status = file_exists($statusFile) ? json_decode(file_get_contents($statusFile), true) : ["mode" => "otomatis", "relay" => "off"];

if (isset($_POST['mode'])) {
    $status['mode'] = $_POST['mode'];
    if ($status['mode'] == 'otomatis') {
        $status['relay'] = 'off';
    }
    file_put_contents($statusFile, json_encode($status));
}
if (isset($_POST['relay']) && $status['mode'] == 'manual') {
    $status['relay'] = $_POST['relay'];
    file_put_contents($statusFile, json_encode($status));
}

$dataFile = "data.txt";
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Tanaman IoT</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>


<div class="container">
    <div class="header-bar">
        <h2>Monitoring Tanaman IoT</h2>
        <button class="toggle-theme" onclick="toggleTheme()">🌙 Mode Malam</button>
    </div>

    
    <div class="box" id="data-box">
        <h3>Data Terbaru</h3>
        <p><b>Waktu:</b> <span id="waktu"><?= $data ? htmlspecialchars($data['waktu']) : 'Belum ada data.' ?></span></p>
        <div class="sensor-boxes">
            <div class="sensor-box">
                <div class="sensor-label">SUHU</div>
                <div class="sensor-value" id="suhu"><?= $data ? htmlspecialchars($data['suhu']) : '-' ?>°C</div>
            </div>
            <div class="sensor-box">
                <div class="sensor-label">SOIL</div>
                <div class="sensor-value" id="soil"><?= $data ? htmlspecialchars($data['soil']) : '-' ?>%</div>
            </div>
        </div>
    </div>

    <div class="box">
        <h3>Pengaturan Mode</h3>
        <form id="mode-form" method="post" action="">
            <label for="mode-select">Mode:</label>
            <select name="mode" id="mode-select">
                <option value="otomatis" <?= $status['mode'] == "otomatis" ? "selected" : "" ?>>Otomatis</option>
                <option value="manual" <?= $status['mode'] == "manual" ? "selected" : "" ?>>Manual</option>
            </select>
            <br><input type="submit" value="Ubah Mode">
        </form>
    </div>

    <div class="box" id="relay-box" style="display: <?= $status['mode'] == 'manual' ? 'block' : 'none' ?>;">
        <h3>Kontrol Relay (Manual)</h3>
        <form id="relay-form" method="post" action="">
            <label for="relay-select">Relay:</label>
            <select name="relay" id="relay-select">
                <option value="on" <?= $status['relay'] == "on" ? "selected" : "" ?>>ON</option>
                <option value="off" <?= $status['relay'] == "off" ? "selected" : "" ?>>OFF</option>
            </select>
            <br><input type="submit" value="Kirim Perintah">
        </form>
        <p>Status Relay: <span id="relay-status" class="<?= $status['relay'] == 'on' ? 'status-on' : 'status-off' ?>"><?= strtoupper($status['relay']) ?></span></p>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
