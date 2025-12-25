<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/security.php';

// Proteksi: Hanya Staf BAA atau Admin
if ($_SESSION['role'] !== 'staf_baa' && $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php"); exit;
}

// Proses Input NINA (AES Encrypted)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_nina'])) {
    if (!verify_token($_POST['csrf_token'])) die("CSRF Invalid");

    $id_yudisium = $_POST['id_yudisium'];
    $nina_raw = trim($_POST['nina']);
    
    // ENKRIPSI AES-256 sebelum simpan ke database
    $nina_encrypted = encrypt_data($nina_raw);

    $sql = "UPDATE yudisium SET nina = '$nina_encrypted' WHERE id_yudisium = '$id_yudisium'";
    
    if($conn->query($sql)) {
        header("Location: baa_nina.php?msg=Nomor NINA Berhasil Dienkripsi & Disimpan");
        exit;
    }
}

// Ambil data mahasiswa yang sudah divalidasi prodi tapi belum ada NINA
$query = "SELECT y.*, pr.nama_prodi 
          FROM yudisium y 
          JOIN prodi pr ON y.id_prodi = pr.id_prodi 
          WHERE y.status_validasi = 'valid' AND (y.nina IS NULL OR y.nina = '')";
$data_pending = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Penomoran NINA - Staf BAA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-slate-800"><i class="fas fa-shield-alt text-indigo-600"></i> Penomoran NINA (AES-256)</h2>
            <a href="dashboard.php" class="text-indigo-600 hover:underline text-sm">← Kembali</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="bg-indigo-600 text-white p-4 rounded-xl mb-6 shadow-md text-sm">
                <i class="fas fa-lock"></i> <?= h($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-50 border-b border-slate-100">
                <h3 class="font-bold text-slate-700 italic">Daftar Tunggu Penomoran Ijazah</h3>
                <p class="text-xs text-slate-500">Hanya menampilkan mahasiswa yang sudah divalidasi oleh Program Studi.</p>
            </div>
            
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-400">
                    <tr>
                        <th class="p-4">Mahasiswa / NPM</th>
                        <th class="p-4">Input Nomor NINA</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if ($data_pending->num_rows > 0): ?>
                        <?php while($row = $data_pending->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4">
                                <p class="font-bold text-slate-800"><?= h($row['nama_mahasiswa']) ?></p>
                                <p class="text-[10px] text-slate-400"><?= h($row['npm']) ?> - <?= h($row['nama_prodi']) ?></p>
                            </td>
                            <form method="POST">
                                <td class="p-4">
                                    <input type="hidden" name="csrf_token" value="<?= get_token(); ?>">
                                    <input type="hidden" name="id_yudisium" value="<?= $row['id_yudisium'] ?>">
                                    <input type="text" name="nina" placeholder="Masukkan Nomor Ijazah..." 
                                           class="w-full border border-slate-300 p-2 rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none text-xs" required>
                                </td>
                                <td class="p-4 text-center">
                                    <button type="submit" name="update_nina" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 shadow-sm">
                                        <i class="fas fa-key"></i> ENKRIPSI & SIMPAN
                                    </button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="p-10 text-center text-slate-400 italic">
                                <i class="fas fa-check-double block text-2xl mb-2 opacity-20"></i>
                                Tidak ada antrean penomoran NINA.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 p-4 bg-amber-50 border border-amber-100 rounded-xl">
            <p class="text-[11px] text-amber-700 flex items-center gap-2">
                <i class="fas fa-info-circle"></i> 
                <b>Security Note:</b> Data NINA akan dienkripsi menggunakan metode <b>AES-256-CBC</b> sebelum masuk ke storage database.
            </p>
        </div>
    </div>
</body>
</html>