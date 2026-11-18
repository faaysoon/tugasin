<?php
// Koneksi database & fungsi bantu. Sesuaikan user/password MySQL Anda.
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $db = new mysqli('localhost', 'root', '', 'db_tugasin');
    $db->set_charset('utf8mb4');
} catch (Exception $ex) {
    die('Koneksi database gagal. Pastikan MySQL aktif dan database.sql sudah diimpor.');
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect($url) { header("Location: $url"); exit; }
function flash($pesan = null, $tipe = 'ok') {
    if ($pesan !== null) { $_SESSION['flash'] = [$pesan, $tipe]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
