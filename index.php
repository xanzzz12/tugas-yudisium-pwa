<?php
require_once 'config/security.php';
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Yudisium</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Login Yudisium</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                <?= h($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= get_token(); ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition">Login</button>
        </form>
        <p class="mt-4 text-xs text-center text-gray-500">Mahasiswa mendaftar? <a href="register.php" class="text-blue-500">Klik di sini</a></p>
    </div>
</body>
</html>