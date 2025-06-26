<?php
// Ambil status
$statusFile = "status.json";
$status = file_exists($statusFile) ? json_decode(file_get_contents($statusFile), true) : ["mode" => "otomatis", "relay" => "off"];

// Handle update
if (isset($_POST['mode'])) {
    $status['mode'] = $_POST['mode'];
    if ($status['mode'] == 'otomatis') {
        $status['relay'] = 'off'; // Reset relay jika otomatis
    }
    file_put_contents($statusFile, json_encode($status));
}
if (isset($_POST['relay']) && $status['mode'] == 'manual') {
    $status['relay'] = $_POST['relay'];
    file_put_contents($statusFile, json_encode($status));
}

// Ambil data sensor
$dataFile = "data.txt";
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kontrol Tanaman IoT</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f8ff; text-align: center; }
        .box { border: 1px solid #ccc; padding: 15px; margin: 0 auto 20px auto; width: 400px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: left; }
        label { display: inline-block; width: 100px; }
        input[type="submit"], button { padding: 6px 12px; margin-top: 10px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 4px; }
        input[type="submit"]:hover, button:hover { background-color: #0056b3; }
        h2, h3 { color: #333; }
        .status-on { color: green; font-weight: bold; }
        .status-off { color: red; font-weight: bold; }
        select { padding: 4px; border-radius: 4px; border: 1px solid #ccc; }
        #waktu {
            font-weight: bold;
            font-size: 1.4em;
            color: #007bff;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            line-height: 1.2;
        }
        #waktu-icon {
            margin-bottom: 6px;
            width: 24px;
            height: 24px;
            fill: #007bff;
        }
        .sensor-boxes {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .sensor-box {
            flex: 1;
            border: 1px solid #007bff;
            border-radius: 8px;
            background: #e6f0ff;
            margin: 0 5px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .sensor-icon {
            width: 32px;
            height: 32px;
            margin-bottom: 8px;
            fill: #007bff;
        }
        .sensor-label {
            font-weight: bold;
            margin-bottom: 4px;
            color: #0056b3;
        }
        .sensor-value {
            font-size: 1.3em;
            font-weight: bold;
            color: #003366;
        }
    </style>
</head>
<body>

<h2>Monitoring Tanaman IoT</h2>

    <div class="box" id="data-box">
        <h3>Data Terbaru</h3>
        <p><b>Waktu:</b> <span id="waktu"><svg id="waktu-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zm0-18c5.523 0 10 4.477 10 10s-4.477 10-10 10-10-4.477-10-10 4.477-10 10-10zm-.5 5v5.25l4.5 2.67-.75 1.23-5-3v-6.15h1.25z"/></svg> <span id="waktu-text"><?= $data ? htmlspecialchars($data['waktu']) : 'Belum ada data.' ?></span></span></p>
        <div class="sensor-boxes">
            <div class="sensor-box">
                <svg class="sensor-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6 2a1 1 0 0 0-1 1v18a1 1 0 0 0 2 0V3a1 1 0 0 0-1-1zm12 4a1 1 0 0 0-1 1v14a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1z"/></svg>
                <div class="sensor-label">SUHU</div>
                <div class="sensor-value" id="suhu"><?= $data ? htmlspecialchars($data['suhu']) : '-' ?>° C</div>
            </div>
            
            <div class="sensor-box">
                <svg class="sensor-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-10 10c0 5.523 4.477 10 10 10s10-4.477 10-10a10 10 0 0 0-10-10z"/></svg>
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

<script>
    // Function to fetch latest data and update UI
    async function fetchData() {
        try {
            const response = await fetch('data.txt?_=' + new Date().getTime(), { cache: 'no-store' });
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            // Format waktu to custom string
            const waktuElem = document.getElementById('waktu-text');
            if (data.waktu) {
                const dt = new Date(data.waktu);
                const optionsDate = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                const timeStr = dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/:/g, '.');
                const dateStr = dt.toLocaleDateString('id-ID', optionsDate);
                waktuElem.innerHTML = `${timeStr}<br>${dateStr} (WIB)`;
            } else {
                waktuElem.textContent = '-';
            }
            document.getElementById('suhu').textContent = data.suhu || '-';
            document.getElementById('lembab').textContent = data.lembab || '-';
            document.getElementById('soil').textContent = data.soil || '-';
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    // Function to fetch latest status and update UI
    async function fetchStatus() {
        try {
            const response = await fetch('status.json?_=' + new Date().getTime());
            if (!response.ok) throw new Error('Network response was not ok');
            const status = await response.json();
            const modeSelect = document.getElementById('mode-select');
            if (modeSelect.value !== status.mode) {
                modeSelect.value = status.mode;
                modeSelect.dispatchEvent(new Event('change'));
            }
            const relaySelect = document.getElementById('relay-select');
            if (relaySelect) {
                relaySelect.value = status.relay;
                document.getElementById('relay-status').textContent = status.relay.toUpperCase();
                document.getElementById('relay-status').className = status.relay === 'on' ? 'status-on' : 'status-off';
            }
        } catch (error) {
            console.error('Error fetching status:', error);
        }
    }

    // Periodically update data and status every 5 seconds
    setInterval(() => {
        fetchData();
        fetchStatus();
    }, 5000);

    // Initial fetch on page load
    fetchData();
    fetchStatus();

    // AJAX form submission for mode
    document.getElementById('mode-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });
            if (!response.ok) throw new Error('Network response was not ok');
            // Optionally fetch status to update UI
            fetchStatus();
        } catch (error) {
            console.error('Error submitting mode:', error);
        }
    });

    // AJAX form submission for relay
    const relayForm = document.getElementById('relay-form');
    if (relayForm) {
        relayForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('input[type="submit"]');
            submitButton.disabled = true;
            submitButton.value = 'Mengirim...';
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) throw new Error('Network response was not ok');
                // Optionally fetch status to update UI
                await fetchStatus();
            } catch (error) {
                console.error('Error submitting relay:', error);
            } finally {
                submitButton.disabled = false;
                submitButton.value = 'Kirim Perintah';
            }
        });
    }

    // Handle mode select change to show/hide relay control dynamically and auto-submit mode form
    const modeSelect = document.getElementById('mode-select');
    const modeForm = document.getElementById('mode-form');
    modeSelect.addEventListener('change', function() {
        const relayBox = document.getElementById('relay-box');
        if (this.value === 'manual') {
            if (relayBox) {
                relayBox.style.display = 'block';
            }
        } else {
            if (relayBox) {
                relayBox.style.display = 'none';
            }
        }
        // Auto-submit mode form via AJAX
        const formData = new FormData(modeForm);
        fetch('', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            fetchStatus();
        }).catch(error => {
            console.error('Error submitting mode:', error);
        });
    });
</script>

</body>
</html>
