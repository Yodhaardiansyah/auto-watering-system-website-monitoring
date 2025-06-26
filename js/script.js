function fetchData() {
    fetch('data.txt?_=' + new Date().getTime())
        .then(res => res.json())
        .then(data => {
            const waktu = document.getElementById('waktu');
            const dt = new Date(data.waktu);
            waktu.innerHTML = dt.toLocaleTimeString('id-ID') + '<br>' +
                              dt.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) + ' WIB';

            document.getElementById('suhu').textContent = data.suhu + "°C";
            document.getElementById('soil').textContent = data.soil + "%";
        })
        .catch(console.error);
}

function fetchStatus() {
    fetch('status.json?_=' + new Date().getTime())
        .then(res => res.json())
        .then(status => {
            const modeSelect = document.getElementById('mode-select');
            if (modeSelect.value !== status.mode) {
                modeSelect.value = status.mode;
                modeSelect.dispatchEvent(new Event('change'));
            }
            const relaySelect = document.getElementById('relay-select');
            if (relaySelect) {
                relaySelect.value = status.relay;
                const statusElem = document.getElementById('relay-status');
                statusElem.textContent = status.relay.toUpperCase();
                statusElem.className = status.relay === 'on' ? 'status-on' : 'status-off';
            }
        })
        .catch(console.error);
}

setInterval(() => {
    fetchData();
    fetchStatus();
}, 5000);

document.addEventListener("DOMContentLoaded", () => {
    fetchData();
    fetchStatus();

    const modeForm = document.getElementById('mode-form');
    modeForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('', { method: 'POST', body: formData })
            .then(() => fetchStatus())
            .catch(console.error);
    });

    const relayForm = document.getElementById('relay-form');
    if (relayForm) {
        relayForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('input[type="submit"]');
            submitButton.disabled = true;
            submitButton.value = 'Mengirim...';
            fetch('', { method: 'POST', body: formData })
                .then(() => fetchStatus())
                .catch(console.error)
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.value = 'Kirim Perintah';
                });
        });
    }

    const modeSelect = document.getElementById('mode-select');
    const relayBox = document.getElementById('relay-box');
    modeSelect.addEventListener('change', function () {
        relayBox.style.display = this.value === 'manual' ? 'block' : 'none';
        const formData = new FormData(modeForm);
        fetch('', { method: 'POST', body: formData }).then(() => fetchStatus()).catch(console.error);
    });

    // Theme toggle
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        document.body.classList.add("dark");
        document.querySelector(".toggle-theme").textContent = "☀️ Mode Siang";
    }
});

function toggleTheme() {
    const body = document.body;
    body.classList.toggle("dark");
    const isDark = body.classList.contains("dark");
    document.querySelector(".toggle-theme").textContent = isDark ? "☀️ Mode Siang" : "🌙 Mode Malam";
    localStorage.setItem("theme", isDark ? "dark" : "light");
}
