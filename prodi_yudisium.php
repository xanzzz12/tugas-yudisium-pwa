<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/security.php';

// Proteksi: Hanya Staf Prodi atau Admin
if ($_SESSION['role'] !== 'staf_prodi' && $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php"); exit;
}

$my_prodi = $_SESSION['id_prodi'];

// Proses Input Data Yudisium
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_yudisium'])) {
    if (!verify_token($_POST['csrf_token'])) die("CSRF Invalid");

    $npm = $conn->real_escape_string($_POST['npm']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $ipk = (float)$_POST['ipk'];
    $periode = $_POST['id_periode'];
    $tgl_ujian = $_POST['tgl_ujian'];
    $tgl_mulai = $_POST['tgl_mulai'];
    
    // Logic Predikat Otomatis
    if ($ipk >= 3.51) $predikat = 'Dengan Pujian';
    elseif ($ipk >= 3.00) $predikat = 'Sangat Memuaskan';
    else $predikat = 'Memuaskan';

    // Query Insert atau Update (Validasi otomatis 'valid')
    $sql = "INSERT INTO yudisium (npm, nama_mahasiswa, id_prodi, id_periode, tgl_mulai_kuliah, tgl_ujian_sarjana, ipk, predikat, status_validasi) 
            VALUES ('$npm', '$nama', '$my_prodi', '$periode', '$tgl_mulai', '$tgl_ujian', '$ipk', '$predikat', 'valid')
            ON DUPLICATE KEY UPDATE status_validasi='valid', ipk='$ipk'";
    
    if($conn->query($sql)) {
        header("Location: prodi_yudisium.php?msg=Data Mahasiswa Berhasil Divalidasi");
        exit;
    }
}

// Ambil data mahasiswa prodi ini saja
$where_prodi = ($_SESSION['role'] === 'admin') ? "" : "WHERE y.id_prodi = '$my_prodi'";
$query = "SELECT y.*, p.nama_periode, pr.nama_prodi 
          FROM yudisium y 
          JOIN periode p ON y.id_periode = p.id_periode 
          JOIN prodi pr ON y.id_prodi = pr.id_prodi 
          $where_prodi ORDER BY y.id_yudisium DESC";
$data_yudisium = $conn->query($query);

$periodes = $conn->query("SELECT * FROM periode ORDER BY tahun DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Yudisium - Staf Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-slate-800"><i class="fas fa-user-check text-green-600"></i> Validasi Yudisium Mahasiswa</h2>
            <a href="dashboard.php" class="text-blue-600 hover:underline text-sm">← Kembali ke Dashboard</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-600 text-white p-3 rounded-lg mb-6 shadow-lg text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> <?= h($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="hidden" name="csrf_token" value="<?= get_token(); ?>">
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-gray-400">NPM Mahasiswa</label>
                    <input type="text" name="npm" placeholder="Contoh: 20210123" class="w-full border p-2 rounded-lg text-sm focus:ring-2 focus:ring-green-400 outline-none" required>
                </div>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-gray-400">Nama Lengkap</label>
                    <input type="text" name="nama" placeholder="Nama Mahasiswa" class="w-full border p-2 rounded-lg text-sm focus:ring-2 focus:ring-green-400 outline-none" required>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-gray-400">IPK Terakhir</label>
                    <input type="number" step="0.01" name="ipk" placeholder="0.00" class="w-full border p-2 rounded-lg text-sm focus:ring-2 focus:ring-green-400 outline-none" required>
                </div>

                <div class="space-y-1 text-sm">
                    <label class="text-[10px] font-bold uppercase text-gray-400">Tgl Mulai Kuliah</label>
                    <input type="date" name="tgl_mulai" class="w-full border p-2 rounded-lg text-sm" required>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-gray-400">Tgl Ujian Sarjana</label>
                    <input type="date" name="tgl_ujian" class="w-full border p-2 rounded-lg text-sm" required>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-gray-400">Periode Wisuda</label>
                    <select name="id_periode" class="w-full border p-2 rounded-lg text-sm" required>
                        <option value="">-- Pilih Periode --</option>
                        <?php while($per = $periodes->fetch_assoc()): ?>
                            <option value="<?= $per['id_periode'] ?>"><?= h($per['nama_periode']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" name="submit_yudisium" class="md:col-span-3 bg-green-600 text-white font-bold p-3 rounded-xl hover:bg-green-700 transition shadow-md shadow-green-200 mt-2">
                    SIMPAN DAN VALIDASI MAHASISWA
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                    <tr>
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">Prodi</th>
                        <th class="p-4">IPK / Predikat</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4">NINA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-slate-600">
                    <?php while($y = $data_yudisium->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-4">
                            <p class="font-bold text-slate-800"><?= h($y['nama_mahasiswa']) ?></p>
                            <p class="text-xs text-gray-400"><?= h($y['npm']) ?></p>
                        </td>
                        <td class="p-4 text-xs"><?= h($y['nama_prodi']) ?></td>
                        <td class="p-4">
                            <span class="font-bold"><?= h($y['ipk']) ?></span>
                            <p class="text-[10px] italic text-blue-500"><?= h($y['predikat']) ?></p>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-[10px] font-black">VALID</span>
                        </td>
                        <td class="p-4">
                            <?php if($y['nina']): ?>
                                <span class="text-green-500 font-mono text-xs"><i class="fas fa-lock"></i> Tersedia</span>
                            <?php else: ?>
                                <span class="text-orange-400 text-[10px] italic">Menunggu BAA...</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>