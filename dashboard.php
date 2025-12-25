<?php
ob_start();
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/db.php';

// Proteksi Halaman: Kalau belum login, tendang ke index.php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

// --- AMBIL DATA REAL DARI DATABASE ---

// 1. Hitung Total Mahasiswa Terdaftar
$res_mhs = $conn->query("SELECT COUNT(*) as total FROM yudisium");
$total_mhs = $res_mhs->fetch_assoc()['total'] ?? 0;

// 2. Hitung Total yang Sudah Divalidasi oleh Prodi
$res_valid = $conn->query("SELECT COUNT(*) as total FROM yudisium WHERE status_validasi = 'valid'");
$total_valid = $res_valid->fetch_assoc()['total'] ?? 0;

// 3. Hitung Total yang NINA-nya masih kosong (Tugas BAA)
$res_nina = $conn->query("SELECT COUNT(*) as total FROM yudisium WHERE nina IS NULL OR nina = ''");
$total_nina = $res_nina->fetch_assoc()['total'] ?? 0;

// 4. Ambil 5 Pendaftar Terbaru (Pake JOIN ke tabel prodi buat ambil nama prodinya)
$query_recent = "SELECT y.nama_mahasiswa, p.nama_prodi, y.status_validasi 
                 FROM yudisium y 
                 LEFT JOIN prodi p ON y.id_prodi = p.id_prodi 
                 ORDER BY y.id_yudisium DESC LIMIT 5";
$recent_mhs = $conn->query($query_recent);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Yudisium</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex">

    <aside class="w-64 bg-slate-900 min-h-screen text-white hidden md:block shadow-xl flex-shrink-0">
        <div class="p-6 border-b border-slate-800 text-center">
            <h1 class="text-xl font-bold flex items-center justify-center gap-2">
                <i class="fas fa-graduation-cap text-blue-400"></i> SI-YUDISIUM
            </h1>
        </div>
        <nav class="p-4 space-y-2">
            <p class="text-xs text-slate-500 uppercase font-bold px-2 mb-2">Menu Utama</p>
            
            <a href="dashboard.php" class="flex items-center gap-3 p-3 bg-blue-600 rounded-lg hover:bg-blue-500 transition shadow-md shadow-blue-900/20">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>
            
            <?php if ($role === 'admin'): ?>
            <a href="admin_users.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg transition text-slate-300 hover:text-white">
                <i class="fas fa-users-cog w-5"></i> Manajemen User
            </a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $role === 'staf_prodi'): ?>
            <a href="prodi_yudisium.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg transition text-slate-300 hover:text-white">
                <i class="fas fa-user-edit w-5"></i> Data Mahasiswa
            </a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $role === 'staf_baa'): ?>
            <a href="baa_nina.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg transition text-slate-300 hover:text-white">
                <i class="fas fa-certificate w-5"></i> Penomoran NINA
            </a>
            <?php endif; ?>

            <div class="pt-4 border-t border-slate-800 mt-4">
                <a href="cetak_laporan.php" target="_blank" class="flex items-center gap-3 p-3 text-red-400 hover:bg-red-500 hover:text-white rounded-lg transition">
                    <i class="fas fa-file-pdf w-5"></i> Laporan PDF
                </a>
                <a href="logout.php" class="flex items-center gap-3 p-3 text-slate-400 hover:bg-slate-800 rounded-lg transition mt-2">
                    <i class="fas fa-sign-out-alt w-5"></i> Logout
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-1">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center px-8 border-b border-gray-100">
            <div class="text-slate-500 font-medium text-sm italic">
                Sistem Informasi Yudisium &copy; 2025
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-slate-800"><?= h($username) ?></p>
                    <p class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-extrabold uppercase inline-block"><?= strtoupper($role) ?></p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="p-8">
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 rounded-2xl text-white shadow-xl mb-8 relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold italic">Halo, <?= h($username) ?>! 👋</h2>
                    <p class="mt-2 text-slate-300">Selamat datang di panel kendali utama. Berikut adalah ringkasan data yudisium saat ini.</p>
                </div>
                <i class="fas fa-graduation-cap absolute -right-4 -bottom-4 text-9xl text-white/5 rotate-12"></i>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Mahasiswa</p>
                            <p class="text-3xl font-black mt-2 text-slate-800"><?= number_format($total_mhs) ?></p>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-500 rounded-xl"><i class="fas fa-users text-xl"></i></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Sudah Divalidasi</p>
                            <p class="text-3xl font-black mt-2 text-green-600"><?= number_format($total_valid) ?></p>
                        </div>
                        <div class="p-3 bg-green-50 text-green-500 rounded-xl"><i class="fas fa-check-double text-xl"></i></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Belum Ada NINA</p>
                            <p class="text-3xl font-black mt-2 text-orange-500"><?= number_format($total_nina) ?></p>
                        </div>
                        <div class="p-3 bg-orange-50 text-orange-500 rounded-xl"><i class="fas fa-file-signature text-xl"></i></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i> Pendaftar Terbaru
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] uppercase font-bold tracking-widest">
                            <tr>
                                <th class="p-4">Nama Mahasiswa</th>
                                <th class="p-4">Program Studi</th>
                                <th class="p-4 text-center">Status Validasi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-600 divide-y divide-gray-50">
                            <?php if ($recent_mhs->num_rows > 0): ?>
                                <?php while($row = $recent_mhs->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 font-semibold text-slate-700"><?= h($row['nama_mahasiswa']) ?></td>
                                    <td class="p-4"><?= h($row['nama_prodi'] ?? 'N/A') ?></td>
                                    <td class="p-4 text-center">
                                        <?php if($row['status_validasi'] === 'valid'): ?>
                                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-[10px] font-black uppercase">VALID</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-[10px] font-black uppercase">PENDING</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="p-10 text-center italic text-gray-400 text-sm">
                                        <i class="fas fa-folder-open block text-3xl mb-2 opacity-20"></i>
                                        Belum ada data mahasiswa yang terdaftar.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js');
            });
        }
    </script>
</body>
</html>