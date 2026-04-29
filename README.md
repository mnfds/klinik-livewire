<div align="center">

<img src="public/assets/aset/logo-no-text.png" alt="Logo Dokter L" height="90">

# Dokter L

**Aplikasi manajemen klinik berbasis web**

[![Laravel](https://img.shields.io/badge/Laravel-v11-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-v3-4E56A6?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-v3-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Status](https://img.shields.io/badge/status-on%20track-22c55e?style=flat-square)](https://github.com/mnfds/dokterL)

</div>

---

## Tentang Proyek

**Dokter L** adalah aplikasi manajemen klinik yang dibangun menggunakan [Laravel](https://laravel.com/), [Livewire](https://livewire.laravel.com/), dan [Volt](https://voltphp.dev/). Proyek ini dirancang untuk membantu klinik dalam mengelola laporan keuangan, SDMK, transaksi pasien, serta antrian dan rekam medis secara terpadu.

## 🛠️ Instalasi Lokal

```bash
# Clone repositori
git clone https://github.com/mnfds/dokterL.git
cd dokterL

# Install dependensi
composer install
npm install && npm run dev

# Konfigurasi environment
cp .env.example .env
php artisan key:generate

# Migrasi & seed database
php artisan migrate --seed

# Jalankan server
php artisan serve
```

---

## ⚙️ Teknologi

| Teknologi | Versi |
|---|---|
| [Laravel](https://laravel.com/) | v11 |
| [Livewire](https://livewire.laravel.com/) | v3 |
| [Blade](https://laravel.com/docs/blade) + [Tailwind CSS](https://tailwindcss.com/) | v3 |
| [DaisyUI](https://daisyui.com/) | v5 |
| [PowerGrid Livewire DataTables](https://livewire-powergrid.com/) | v6 |

---

## 📂 Struktur Utama

```
.
├── app/
│   ├── Livewire/            # Komponen Livewire (Volt)
│   └── Http/Controllers/    # Controller Laravel
├── resources/views/         # Blade templates & Livewire views
├── public/assets/           # Logo, gambar, aset statis
└── routes/web.php           # Routing aplikasi
```

---

## ✨ Fitur Tersedia

### Manajemen Data
- [x] CRUD Master Data (User, Role, Jam Kerja, Poli, Layanan, Paket Bundling)
- [x] Manajemen Role & Permission

### Operasional Klinik
- [x] Sistem Antrian
- [ ] Pasien Reservasi
- [x] Pendaftaran Pasien
- [x] Kajian Awal & Rekam Medis dengan Metode SOAP
- [x] Manajemen CPPT-O dan CPPT (Kajian Awal + SOAP)
- [x] Transaksi Klinik & Apotik
- [ ] Print Out Surat Keterangan Sehat & Sakit
- [ ] E-Resep

### Inventori & Laporan
- [x] Manajemen Inventori (Obat, Produk, Barang)
- [x] Manajemen Arsip Dokumen
- [x] Manajemen Barang Inventaris
- [x] Laporan Keuangan Klinik

### SDM & Integrasi
- [x] Integrasi SatuSehat API
- [ ] Manajemen Jadwal & Absensi Staff Klinik
- [ ] Pengajuan Cuti, Lembur, Libur, Izin Keluar

---

## 🧾 Konvensi Commit

Proyek ini mengikuti spesifikasi [Conventional Commits](https://www.conventionalcommits.org/) agar riwayat perubahan tetap jelas dan mudah dilacak.

**Format umum:**

```
<type>(scope opsional): <deskripsi singkat>
```

| Type | Keterangan |
|---|---|
| `feat` | Penambahan fitur baru |
| `fix` | Perbaikan bug |
| `docs` | Perubahan dokumentasi (README, dll) |
| `style` | Perubahan non-logik (indentasi, CSS) |
| `refactor` | Refactor kode tanpa fitur/bug baru |
| `test` | Penambahan atau perbaikan testing |
| `chore` | Tugas rutin, update dependensi, dll |
| `ci` | Perubahan terkait CI/CD |

**Contoh:**

```bash
feat(auth): menambahkan tampilan login baru
fix(laporan): perbaikan hitung total kas bulanan
docs(readme): menambahkan panduan commit
```

---

## 👤 Pengembang

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/mnfds">
        <img src="https://github.com/mnfds.png" width="72" style="border-radius:50%" alt="mnfds"/><br/>
        <sub><b>Muhammad Noor Firdaus</b></sub>
      </a>
    </td>
  </tr>
</table>

---

<div align="center">
  <sub>Sehat Sehat Dah :)</sub>
</div>
