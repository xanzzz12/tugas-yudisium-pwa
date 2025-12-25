## 📦 Cara Instalasi & Setup Vendor (DOMPDF)

Karena folder `vendor` tidak diunggah ke repositori untuk efisiensi penyimpanan, silakan ikuti langkah berikut untuk menjalankan fitur cetak PDF:

### Opsi 1: Menggunakan Composer (Rekomendasi)
Jika Anda memiliki Composer di perangkat Anda, jalankan perintah berikut di root folder proyek:
```bash
composer require dompdf/dompdf
Opsi 2: Download Manual (Jika Tanpa Composer)
Download library DOMPDF dari halaman rilis resmi.

Buat folder bernama vendor di root direktori proyek.

Ekstrak isi library ke dalam folder vendor.

Pastikan file cetak_laporan.php sudah memanggil autoload:

PHP

require_once 'vendor/autoload.php';
🛠️ Cara Menjalankan Aplikasi
Clone repositori ini: git clone https://github.com/xanzzz12/tugas-yudisium-pwa.git

Pindahkan ke folder htdocs (XAMPP) atau www (Laragon).

Import database db_yudisium.sql yang tersedia di folder proyek.

Sesuaikan kredensial database di config/db.php.

Buka di browser: http://localhost/tugas-yudisium-pwa


---

### Tips Buat Laporan Word Lo:
Pas dosen lo baca bagian **Struktur Folder** di Word, lo kasih catatan kecil di bawahnya:

> *"Catatan: Folder `vendor` sengaja diabaikan (ignored) dalam repositori GitHub sesuai best practice pengembangan software untuk menghindari redundansi data. Instruksi instalasi dependency DOMPDF telah disertakan dalam file README.md."*

**Beres!** Sekarang GitHub lo udah punya dokumentasi yang "mahal". 

**Ada lagi bree?** Atau udah siap tancap gas buat bikin laporan Word-nya? Jangan lupa istirahat, besok bantai pas presentasi! 🚀🔥
