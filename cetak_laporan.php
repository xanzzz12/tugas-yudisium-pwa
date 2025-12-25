<?php
require_once 'config/db.php';
require_once 'config/security.php';

// Cek akses: Admin, BAA, dan Prodi harusnya bisa cetak ini
if (!isset($_SESSION['role']) || $_SESSION['role'] === 'mahasiswa') {
    die("Akses ditolak!");
}

// Path ke autoload DOMPDF
require_once 'vendor/dompdf/autoload.inc.php'; 
use Dompdf\Dompdf;
use Dompdf\Options;

// Setting Options biar gambar/css eksternal bisa load (kalo ada)
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Ambil data gabungan sesuai permintaan soal
$query = "SELECT y.*, p.nama_prodi, pr.nama_periode 
          FROM yudisium y 
          JOIN prodi p ON y.id_prodi = p.id_prodi 
          JOIN periode pr ON y.id_periode = pr.id_periode 
          WHERE y.status_validasi = 'valid'
          ORDER BY pr.tahun DESC, y.ipk DESC";

$res = $conn->query($query);

// HTML Content
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Daftar Rekomendasi Yudisium / Wisuda</div>
        <div>Seluruh Program Studi Perguruan Tinggi</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. SK</th>
                <th>Tgl SK</th>
                <th>Periode</th>
                <th>NPM</th>
                <th>Nama Mahasiswa</th>
                <th>IPK</th>
                <th>Predikat</th>
                <th>NINA</th>
            </tr>
        </thead>
        <tbody>';

if ($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        // Dekripsi NINA biar tampil angka aslinya
        $nina_asli = ($row['nina']) ? decrypt_data($row['nina']) : '-';
        
        $html .= '<tr>
            <td>'.h($row['no_sk'] ?? '-').'</td>
            <td>'.h($row['tgl_sk'] ?? '-').'</td>
            <td>'.h($row['nama_periode']).'</td>
            <td>'.h($row['npm']).'</td>
            <td>'.h($row['nama_mahasiswa']).'</td>
            <td>'.h($row['ipk']).'</td>
            <td>'.h($row['predikat']).'</td>
            <td style="font-family: monospace;">'.$nina_asli.'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" style="text-align:center">Belum ada data mahasiswa divalidasi.</td></tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Landscape biar tabel muat banyak kolom
$dompdf->render();

// Output ke browser
$dompdf->stream("Laporan_Yudisium_".date('Y-m-d').".pdf", array("Attachment" => false));