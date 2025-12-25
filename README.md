## 📦 Cara Instalasi & Setup Vendor (DOMPDF)

Karena folder `vendor` tidak diunggah ke repositori untuk efisiensi penyimpanan, silakan ikuti langkah berikut untuk menjalankan fitur cetak PDF:

### Opsi 1: Menggunakan Composer (Rekomendasi)
Jika Anda memiliki Composer di perangkat Anda, jalankan perintah berikut di root folder proyek:
```bash
composer require dompdf/dompdf```

Opsi 2: Download Manual (Jika Tanpa Composer)
Download library DOMPDF dari halaman rilis resmi.
Buat folder bernama vendor di root direktori proyek.
Ekstrak isi library ke dalam folder vendor.
Pastikan file cetak_laporan.php sudah memanggil autoload:

require_once 'vendor/autoload.php';

🛠️ Cara Menjalankan Aplikasi
Clone repositori ini: git clone https://github.com/xanzzz12/tugas-yudisium-pwa.git
Pindahkan ke folder htdocs (XAMPP) atau www (Laragon).
Import database db_yudisium.sql yang tersedia di folder proyek.
Sesuaikan kredensial database di config/db.php.
Buka di browser: http://localhost/tugas-yudisium-pwa
