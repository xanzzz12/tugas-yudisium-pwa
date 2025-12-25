<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CSRF CSPRNG Base64
function get_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = base64_encode(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// 2. AES-256-CBC (Buat data NINA / NPM biar aman)
define('AES_KEY', 'kunci_rahasia_lo_32_char_bebas!!'); 
define('AES_IV', 'iv_16_char_bebas');

function encrypt_data($data) {
    $key = substr(hash('sha256', AES_KEY), 0, 32);
    $iv = substr(hash('sha256', AES_IV), 0, 16);
    return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
}

function decrypt_data($data) {
    $key = substr(hash('sha256', AES_KEY), 0, 32);
    $iv = substr(hash('sha256', AES_IV), 0, 16);
    return openssl_decrypt(base64_decode($data), 'AES-256-CBC', $key, 0, $iv);
}

// 3. XSS Protection
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
