<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/security.php';

// Proteksi: Hanya Admin yang boleh masuk
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Proses Tambah User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    if (!verify_token($_POST['csrf_token'])) die("CSRF Invalid");

    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $id_prodi = ($role === 'staf_prodi') ? $_POST['id_prodi'] : "NULL";

    $sql = "INSERT INTO users (username, password, role, id_prodi) VALUES ('$username', '$password', '$role', $id_prodi)";
    if ($conn->query($sql)) {
        header("Location: admin_users.php?msg=User Berhasil Ditambah");
        exit;
    }
}

$users = $conn->query("SELECT u.*, p.nama_prodi FROM users u LEFT JOIN prodi p ON u.id_prodi = p.id_prodi");
$prodis = $conn->query("SELECT * FROM prodi");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen User - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-slate-800"><i class="fas fa-users-cog text-blue-600"></i> Manajemen Pengguna</h2>
            <a href="dashboard.php" class="text-blue-600 hover:underline">← Kembali ke Dashboard</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm"><?= h($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border border-gray-100">
            <h3 class="font-bold mb-4 text-gray-700 text-sm uppercase tracking-wider">Tambah Akun Staf Baru</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="hidden" name="csrf_token" value="<?= get_token(); ?>">
                <input type="text" name="username" placeholder="Username" class="border p-2 rounded-lg text-sm" required>
                <input type="password" name="password" placeholder="Password" class="border p-2 rounded-lg text-sm" required>
                <select name="role" class="border p-2 rounded-lg text-sm" id="roleSelect" onchange="toggleProdi(this.value)">
                    <option value="staf_baa">Staf BAA</option>
                    <option value="staf_prodi">Staf Prodi</option>
                    <option value="admin">Admin</option>
                </select>
                <select name="id_prodi" id="prodiSelect" class="border p-2 rounded-lg text-sm bg-gray-50" disabled>
                    <option value="">-- Pilih Prodi --</option>
                    <?php while($p = $prodis->fetch_assoc()): ?>
                        <option value="<?= $p['id_prodi'] ?>"><?= h($p['nama_prodi']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="add_user" class="bg-blue-600 text-white p-2 rounded-lg font-bold hover:bg-blue-700 transition">Simpan</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-bold tracking-widest">
                    <tr>
                        <th class="p-4 border-b">Username</th>
                        <th class="p-4 border-b">Role</th>
                        <th class="p-4 border-b">Program Studi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50">
                    <?php while($u = $users->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-semibold text-slate-700"><?= h($u['username']) ?></td>
                        <td class="p-4 text-blue-600 uppercase text-xs font-black"><?= h($u['role']) ?></td>
                        <td class="p-4 text-gray-500"><?= h($u['nama_prodi'] ?? '-') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function toggleProdi(val) {
        const select = document.getElementById('prodiSelect');
        if (val === 'staf_prodi') {
            select.disabled = false;
            select.classList.remove('bg-gray-50');
            select.required = true;
        } else {
            select.disabled = true;
            select.classList.add('bg-gray-50');
            select.required = false;
            select.value = "";
        }
    }
    </script>
</body>
</html>