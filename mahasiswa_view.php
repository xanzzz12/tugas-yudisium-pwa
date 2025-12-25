<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/security.php';

// Mahasiswa login pake NPM sebagai username
if (!isset($_SESSION['username'])) { header("Location: index.php"); exit; }

$npm = $_SESSION['username'];
$query = "SELECT y.*, p.nama_prodi, pr.nama_periode 
          FROM yudisium y 
          LEFT JOIN prodi p ON y.id_prodi = p.id_prodi 
          LEFT JOIN periode pr ON y.id_periode = pr.id_periode 
          WHERE y.npm = '$npm'";
$result = $conn->query($query);
$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Status Yudisium Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 p-4 md:p-10">
    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden border border-white">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-white text-center relative">
            <i class="fas fa-user-graduate text-6xl opacity-20 absolute top-4 right-4"></i>
            <h2 class="text-2xl font-black tracking-tight">HASIL YUDISIUM</h2>
            <p class="text-blue-100 text-sm">Verifikasi Data Kelulusan Mahasiswa</p>
        </div>

        <div class="p-8">
            <?php if ($data): ?>
                <div class="grid grid-cols-1 gap-6">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-400 text-xs font-bold uppercase">Nama Lengkap</span>
                        <span class="font-bold text-slate-800"><?= h($data['nama_mahasiswa']) ?></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-400 text-xs font-bold uppercase">NPM</span>
                        <span class="font-mono font-bold text-slate-800"><?= h($data['npm']) ?></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-400 text-xs font-bold uppercase">Program Studi</span>
                        <span class="font-bold text-slate-800"><?= h($data['nama_prodi'] ?? 'Belum Diatur') ?></span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-400 text-xs font-bold uppercase">IPK / Predikat</span>
                        <span class="font-bold text-blue-600"><?= h($data['ipk']) ?> (<?= h($data['predikat']) ?>)</span>
                    </div>
                    
                    <div class="mt-4 p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Nomor Ijazah Nasional (NINA)</p>
                        <?php if ($data['nina']): ?>
                            <p class="text-2xl font-black text-indigo-700 tracking-widest font-mono">
                                <?= h(decrypt_data($data['nina'])) ?>
                            </p>
                            <span class="text-[9px] text-green-500 font-bold"><i class="fas fa-check-shield"></i> Verified by BAA</span>
                        <?php else: ?>
                            <p class="text-xl font-bold text-orange-400 italic">BELUM TERBIT</p>
                            <span class="text-[9px] text-slate-400 italic">Dalam proses verifikasi berkas</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <i class="fas fa-exclamation-triangle text-4xl text-amber-400 mb-4"></i>
                    <p class="text-gray-500 font-bold">Data yudisium Anda belum diinput oleh Prodi.</p>
                </div>
            <?php endif; ?>
            
            <div class="mt-8">
                <a href="dashboard.php" class="block w-full text-center bg-slate-800 text-white font-bold py-3 rounded-xl hover:bg-slate-900 transition">KEMBALI KE DASHBOARD</a>
            </div>
        </div>
    </div>
</body>
</html>