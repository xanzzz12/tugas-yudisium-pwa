<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "sql306.infinityfree.com"; // Tadi lo nulis 106, pantesan kaga nyambung!
$user = "if0_40171051"; 
$pass = "2fRKxW5FgJo8G"; 
$db   = "if0_40171051_tugas"; // Pastiin ini yang dipake buat tabel yudisium

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
    // echo "Koneksi Berhasil, gasss!"; 
} catch (mysqli_sql_exception $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>
