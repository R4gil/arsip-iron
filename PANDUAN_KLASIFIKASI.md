# Panduan Modul Klasifikasi Arsip

## Daftar Isi
1. [Struktur Database](#struktur-database)
2. [Cara Impor Data CSV](#cara-impor-data-csv)
3. [Cara Menggunakan di Form Tambah Arsip](#cara-menggunakan-di-form-tambah-arsip)
4. [Format File CSV](#format-file-csv)
5. [Troubleshooting](#troubleshooting)

---

## Struktur Database

### Tabel `klasifikasi`
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | INT | Primary key, auto increment |
| `kode` | VARCHAR(255) | Kode unik klasifikasi (contoh: PR, KU, IMU) |
| `nama` | VARCHAR(255) | Nama atau deskripsi klasifikasi |
| `created_at` | TIMESTAMP | Waktu pembuatan data |
| `updated_at` | TIMESTAMP | Waktu perubahan terakhir |

**Catatan:** Gunakan kolom `nama` bukan `nama_klasifikasi` di query atau import.

---

## Cara Impor Data CSV

### Langkah-langkah:
1. Buka menu **Pengaturan > Klasifikasi**
2. Gulir ke bawah, temukan form **Impor Klasifikasi dari CSV**
3. Klik tombol **Pilih File** atau drag-drop file CSV
4. Klik tombol **Impor CSV**
5. Tunggu sampai muncul pesan sukses

### File CSV Harus Memiliki:
- **Baris pertama (Header):** Berisi nama kolom (minimal `kode` dan salah satu dari: `nama`, `keterangan`, `deskripsi`, `nama_klasifikasi`)
- **Baris berikutnya:** Data klasifikasi sesuai dengan kolom
- **Pemisah:** Koma (`,`)
- **Encoding:** UTF-8 (agar karakter Indonesia tidak rusak)

### Contoh Format CSV yang Benar:

#### Format 1 (Paling Sederhana):
```
Kode,Keterangan
PR,PERENCANAAN
PR.01,Program dan Anggaran
PR.02,Evaluasi
KU,KEUANGAN
KU.01,Pelaksanaan Anggaran
```

#### Format 2 (Dengan Kolom `Nama`):
```
Kode,Nama,Deskripsi
PR,Perencanaan,Bagian dari perencanaan strategis
KU,Keuangan,Manajemen keuangan negara
```

---

## Cara Menggunakan di Form Tambah Arsip

### Langkah-langkah:
1. Buka menu **Arsip > Tambah Arsip Baru**
2. Pada field **Nomor Surat**, ada tombol **"Cari Kode"**
3. Klik tombol tersebut untuk membuka modal pencarian
4. Ketik kode atau nama klasifikasi yang dicari (contoh: "PR" atau "Perencanaan")
5. Sistem akan menampilkan hasil real-time
6. Klik pada hasil yang diinginkan
7. Sistem akan otomatis isi field nomor surat dengan format: `WIM.11.IMI.2-[KODE]-/[TAHUN]`

### Contoh:
- Input: `PR.01`
- Hasil otomatis: `WIM.11.IMI.2-PR.01-/2026`

---

## Format File CSV

### Aturan Umum:
- **Pembatas kolom:** Tanda koma (`,`)
- **Encoding file:** UTF-8
- **Header (baris pertama) harus ada dan minimal berisi:**
  - Kolom `Kode` atau `code` atau `kode_klasifikasi`
  - Kolom `Nama` atau `Keterangan` atau `Deskripsi` atau `Nama Klasifikasi`

### Template CSV yang Siap Pakai:

**Simpan file ini dengan nama `klasifikasi.csv`:**

```
Kode,Keterangan
PR,PERENCANAAN
PR.01,Program dan Anggaran
PR.01.01,Rencana Strategis
PR.01.02,Trilateral Meeting
PR.01.03,Rencana Kerja
PR.01.04,Rencana Kerja dan Anggaran
PR.02,Evaluasi
PR.02.01,Unit Utama
PR.02.02,Kantor Wilayah
PR.03,Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP)
PR.04,Pelaporan
PR.04.01,Laporan Tetap
PR.04.02,Laporan Insidentil
PR.05,Rapat Kerja
PR.05.01,Dengan Dewan Perwakilan Rakyat (DPR)
PR.05.02,Tingkat Kementerian
PR.05.03,Tingkat Unit Utama (Rapat Kerja Teknis)
PR.05.04,Tingkat Kantor Wilayah
PR.05.05,Rapat Pimpinan dan Rapat Staf
PR.06,Sidang Kabinet
PR.06.01,Sidang Kabinet Terbatas
PR.06.02,Sidang Kabinet Paripurna
KU,KEUANGAN
KU.01,Pelaksanaan Anggaran
KU.01.01,"Pedoman, Petunjuk, dan Administrasi Pelaksanaan Anggaran"
KU.01.02,Daftar Isian Pelaksanaan Anggaran (DIPA)
KU.01.03,Penerimaan Negara Bukan Pajak (PNBP)
KU.02,Tata Usaha Keuangan
KU.02.01,Pedoman dan Petunjuk Administrasi Keuangan
KU.02.02,Penatausahaan Hibah
KU.02.03,Pejabat Perbendaharaan Negara
KU.02.04,Penyelesaian Kerugian Negara
KU.02.05,Penatausahaan Rekening Pemerintah
KU.03,Perbendaharaan
KU.03.01,Dokumen Pertanggungjawaban Belanja
KU.03.02,Buku Kas Umum dan Buku Pembantu
KU.03.03,Laporan Pertanggungjawaban (LPJ) Bendahara Pengeluaran
KU.03.04,LPJ Bendahara Penerimaan
```

---

## Troubleshooting

### Error: "Kolom tidak ditemukan" saat impor
**Penyebab:** Nama kolom di header CSV tidak sesuai
**Solusi:** Pastikan kolom pertama bernama `Kode` dan kolom kedua bernama `Keterangan` (atau `Nama`, `Deskripsi`, etc.)

### Data tidak muncul di pencarian "Cari Kode"
**Penyebab:** Data belum diimpor atau impor gagal
**Solusi:** 
1. Buka menu Klasifikasi
2. Cek apakah ada data di tabel
3. Jika belum ada, lakukan impor CSV
4. Refresh halaman tambah arsip

### Karakter Indonesia tidak tampil dengan benar
**Penyebab:** File CSV tidak dalam encoding UTF-8
**Solusi:** 
1. Buka file CSV dengan text editor (Notepad++, Visual Studio Code)
2. Ubah encoding ke UTF-8
3. Simpan dan coba impor lagi

### Impor berhasil tapi data hanya sebagian
**Penyebab:** Ada baris di CSV yang tidak valid (kosong atau format salah)
**Solusi:** 
1. Cek file CSV, hapus baris kosong
2. Pastikan tidak ada comma ekstra di akhir baris
3. Impor ulang

### Tombol "Cari Kode" di form Tambah Arsip tidak bekerja
**Penyebab:** JavaScript error atau modal tidak terbuka
**Solusi:**
1. Buka browser console (F12)
2. Cek error messages
3. Clear browser cache dan refresh
4. Coba di browser lain

---

## Referensi Teknis

### Route yang Tersedia:
| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/klasifikasi` | Daftar klasifikasi |
| POST | `/klasifikasi` | Tambah klasifikasi manual |
| GET | `/klasifikasi/create` | Form tambah klasifikasi |
| POST | `/klasifikasi/import` | Impor CSV |
| GET | `/klasifikasi/{id}/edit` | Form edit klasifikasi |
| PUT | `/klasifikasi/{id}` | Perbarui klasifikasi |
| DELETE | `/klasifikasi/{id}` | Hapus klasifikasi |
| GET | `/ajax/klasifikasi?q=...` | API pencarian (digunakan di modal Cari Kode) |

### Query Pencarian:
```
GET /ajax/klasifikasi?q=PR
```
Mengembalikan JSON:
```json
[
  {
    "id": 1,
    "kode": "PR",
    "nama": "PERENCANAAN"
  },
  {
    "id": 2,
    "kode": "PR.01",
    "nama": "Program dan Anggaran"
  }
]
```

---

## Tanya Jawab

**T: Apakah saya bisa menghapus klasifikasi yang sudah digunakan?**
> Tidak disarankan. Jika sudah digunakan di data arsip, hapus akan menyebabkan referensi rusak. Lebih baik set status arsip menjadi "Inaktif".

**T: Berapa banyak data klasifikasi yang bisa diimpor sekaligus?**
> Tidak ada batas maksimal dalam sistem. Tergantung ukuran file dan timeout server (biasanya 5-10 menit).

**T: Bisakah membuat struktur klasifikasi bertingkat (parent-child)?**
> Sistem ini menggunakan kode bertingkat (PR.01.01), bukan relasi parent_id. Untuk fitur parent-child, gunakan tabel `klasifikasi_arsip` di fase selanjutnya.

**T: Bagaimana menghubungkan klasifikasi dengan lokasi penyimpanan?**
> Klasifikasi adalah pengelompokan dokumen berdasarkan jenis/fungsi. Lokasi adalah tempat fisik penyimpanan. Keduanya independen dan dapat dikombinasikan di form tambah arsip.

---

**Terakhir diperbarui:** 8 Juli 2026
**Versi:** 1.0
