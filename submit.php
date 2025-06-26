<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies

$suhu = $_GET['suhu'] ?? null;
$lembab = $_GET['lembab'] ?? null;
$soil = $_GET['soil'] ?? null;

// Ambil status.json
$status = json_decode(file_get_contents("status.json"), true);

// Simpan data jika lengkap
if ($suhu && $lembab && $soil) {
    $waktu = date('Y-m-d H:i:s');
    $data = [
        "waktu" => $waktu,
        "suhu" => $suhu,
        "lembab" => $lembab,
        "soil" => $soil
    ];
    file_put_contents("data.txt", json_encode($data));
    echo json_encode([
        "status" => "ok",
        "mode" => $status['mode'],
        "relay" => $status['relay']
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "mode" => $status['mode'],
        "relay" => $status['relay']
    ]);
}
?>
