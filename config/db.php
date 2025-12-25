<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 
$user = 
$pass =  
$db   = 

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
    // echo "Koneksi Berhasil, gasss!"; 
} catch (mysqli_sql_exception $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>
