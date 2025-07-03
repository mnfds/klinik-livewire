# 🩺 Dokter L - Aplikasi Manajemen Klinik

**Dokter L** adalah aplikasi manajemen klinik berbasis web yang dibangun menggunakan [Laravel](https://laravel.com/), [Livewire](https://livewire.laravel.com/), dan [Volt](https://voltphp.dev/).  
Proyek ini **masih dalam pengembangan (on track)** dan dirancang untuk membantu klinik dalam mengelola laporan keuangan, transaksi pasien, serta tampilan visual yang dinamis dan responsif.

## 🧩 Fitur Sekarang

- 🔐 **Autentikasi**: Login, register, reset password
- 🌙 **Dark Mode**: Tampilan responsif dan nyaman di siang/malam hari
- 🎨 **Komponen Dinamis**: Menggunakan Tailwind CSS + Livewire Volt

## 🖼️ Cuplikan Logo

<div align="center">
  <img src="public/assets/logo_dr_l.png" alt="Logo Dokter L" height="100">
</div>

## 📂 Struktur Utama

```

.
├── app/
│   ├── Livewire/           # Komponen Livewire (Volt)
│   ├── Http/Controllers/   # Controller Laravel
├── resources/views/        # Blade templates dan Livewire views
├── public/assets/          # Logo, gambar, dll.
├── routes/web.php          # Routing aplikasi
├── vendor/                 # Laravel core (diubah: quotes Inspiring.php)

````

## 🛠️ Instalasi Lokal

```bash
git clone https://github.com/mnfds/dokterL.git
cd dokterL
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
````

## ⚙️ Teknologi

* Laravel v11
* Livewire v3
* Blade + Tailwind CSS v3
* PowerGrid Livewire DataTables v6
* Daisy UI v5

```

## 🗓️ Roadmap

* [ ] Crud Master Data (User, Role, Jam Kerja, Poli, Layanan, Produk & Obat, Paket Bundling)
* [ ] Manajemen Role & Permission
* [ ] Antrian Real Time
* [ ] Pasien Reservasi
* [ ] Kajian Awal & Rekam Medis Dengan Metode SOAP
* [ ] Manajemen CPPT-O dan CPPT (Kajian Awal + SOAP)
* [ ] Transaksi Klinik
* [ ] Manajemen Inventori (Obat, Produk, Barang)
* [ ] Integrasi SatuSehat API
* [ ] Manajemen Jadwal Dan Absensi Staff Klinik
* [ ] Pengajuan Cuti, Lembur, Libur, Izin Keluar
* [ ] Laporan Keuangan Klinik

## 👤 Pengembang

* Muhammad Noor Firdaus ([@mnfds](https://github.com/mnfds))

---

## 🧾 Konvensi Commit

Proyek ini mengikuti [Conventional Commits](https://www.conventionalcommits.org/) agar riwayat perubahan tetap jelas dan mudah dilacak.

### Format umum

```bash
<type>(optional-scope): <deskripsi singkat>
```

### Jenis-jenis commit

| Type       | Keterangan                           |
| ---------- | ------------------------------------ |
| `feat`     | Penambahan fitur baru                |
| `fix`      | Perbaikan bug                        |
| `docs`     | Perubahan dokumentasi (README, dll)  |
| `style`    | Perubahan non-logik (indentasi, CSS) |
| `refactor` | Refactor kode (tanpa fitur/bug)      |
| `test`     | Penambahan/perbaikan testing         |
| `chore`    | Tugas rutin, update dependensi, dll  |
| `ci`       | Perubahan terkait CI/CD              |

### Contoh

```bash
feat(auth): menambahkan tampilan login baru
fix(laporan): perbaikan hitung total kas bulanan
docs(readme): menambahkan panduan commit
```

---
