<?php
// 1. AKTIFKAN DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. OB_START BIAR REDIRECT LANCAR
ob_start();

require_once 'config/security.php';
require_once 'config/db.php';

// Ambil data prodi buat dropdown
$prodis = $conn->query("SELECT * FROM prodi");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifikasi CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_token($token)) {
        die("CSRF Token Invalid!");
    }

    $user = $conn->real_escape_string(trim($_POST['username']));
    
    // PERBAIKAN: Gunakan password_hash bawaan PHP (BCrypt)
    $pass = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); 
    
    $prodi = $_POST['id_prodi'];

    // Cek apakah username sudah ada biar gak duplikat
    $cek = $conn->query("SELECT id_user FROM users WHERE username = '$user'");
    if ($cek->num_rows > 0) {
        $error = "Username/NPM sudah terdaftar!";
    } else {
        $sql = "INSERT INTO users (username, password, role, id_prodi) VALUES ('$user', '$pass', 'mahasiswa', '$prodi')";
        
        if ($conn->query($sql)) {
            // Berhasil: Lempar ke login dengan pesan sukses
            header("Location: index.php?msg=Registrasi Berhasil, Silahkan Login");
            exit;
        } else {
            $error = "Gagal daftar database: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-black mb-6 text-center text-green-600 uppercase tracking-tight">Daftar Mahasiswa</h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm font-bold">
                <?= h($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= get_token(); ?>">
            
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Username (NPM)</label>
                <input type="text" name="username" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-green-400 outline-none transition" placeholder="Masukkan NPM..." required>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-green-400 outline-none transition" placeholder="******" required>
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Program Studi</label>
                <select name="id_prodi" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-green-400 outline-none transition bg-white" required>
                    <option value="">-- Pilih Prodi --</option>
                    <?php while($p = $prodis->fetch_assoc()): ?>
                        <option value="<?= $p['id_prodi'] ?>"><?= h($p['nama_prodi']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-black py-3 rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-200">
                BUAT AKUN SEKARANG
            </button>
        </form>
        
        <p class="mt-6 text-center text-xs text-gray-500">
            Sudah punya akun? <a href="index.php" class="text-green-600 font-bold">Login di sini</a>
        </p>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>