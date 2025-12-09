# 📥 Panduan Import Data via Excel

## ✅ Fitur yang Tersedia

1. **Import Guru** - Import data guru dari Excel
2. **Import Murid** - Import data murid dari Excel
3. **Download Template** - Download template Excel yang sudah sesuai format

---

## 🎯 CARA IMPORT MURID

### Langkah 1: Download Template

1. Login sebagai **Admin**
2. Buka menu **Akademik > Data Murid**
3. Klik tombol **"Download Template"** (icon download)
4. File `template_murid.xlsx` akan terdownload

### Langkah 2: Isi Data di Excel

Buka file template dan isi data sesuai format:

| nama | email | kelas | status |
|------|-------|-------|--------|
| Ahmad Fauzi | ahmad@student.com | X IPA 1 | 1 |
| Siti Nur | siti@student.com | X IPS 1 | 1 |

**Keterangan Kolom:**
- **nama**: Nama lengkap murid (Required)
- **email**: Email unik (Required)
- **kelas**: Nama kelas yang sudah ada di sistem (Required)
- **status**: 1 = Aktif, 0 = Tidak Aktif (Optional, default: 1)

**Daftar Kelas yang Tersedia:**
- X IPA 1, X IPA 2
- X IPS 1, X IPS 2
- XI IPA 1, XI IPA 2
- XI IPS 1, XI IPS 2
- XII IPA 1, XII IPA 2
- XII IPS 1, XII IPS 2

### Langkah 3: Import File

1. Klik tombol **"Import Excel"** (icon upload hijau)
2. Pilih file Excel yang sudah diisi
3. Klik **"Import"**
4. Tunggu proses selesai
5. Lihat notifikasi hasil import

### Contoh Data:

```
nama,email,kelas,status
Ahmad Fauzi,ahmad.fauzi@student.com,X IPA 1,1
Siti Nurhaliza,siti.nur@student.com,X IPA 1,1
Budi Santoso,budi.santoso@student.com,X IPS 1,1
Dewi Lestari,dewi.lestari@student.com,XI IPA 1,1
Rizki Pratama,rizki.pratama@student.com,XI IPS 1,1
```

---

## 🎯 CARA IMPORT GURU

### Langkah 1: Download Template

1. Login sebagai **Admin**
2. Buka menu **Akademik > Data Guru**
3. Klik tombol **"Download Template"** (icon download)
4. File `template_guru.xlsx` akan terdownload

### Langkah 2: Isi Data di Excel

Buka file template dan isi data sesuai format:

| nama | mata_pelajaran | kelas |
|------|----------------|-------|
| Pak Budi | Matematika | 10 IPA |
| Bu Siti | Bahasa Indonesia | 10 IPS |

**Keterangan Kolom:**
- **nama**: Nama lengkap guru (Required)
- **mata_pelajaran**: Mata pelajaran yang diajar (Required)
- **kelas**: Kelas yang diajar (Required)

### Langkah 3: Import File

1. Klik tombol **"Import Excel"** (icon upload hijau)
2. Pilih file Excel yang sudah diisi
3. Klik **"Import"**
4. Tunggu proses selesai
5. Lihat notifikasi hasil import

### Contoh Data:

```
nama,mata_pelajaran,kelas
Pak Budi Santoso,Matematika,10 IPA
Bu Siti Aisyah,Bahasa Indonesia,10 IPS
Pak Joko Widodo,Fisika,11 IPA
Bu Rina Wati,Sejarah,11 IPS
Pak Anto Wijaya,Kimia,12 IPA
```

---

## ⚠️ PENTING! Hal yang Harus Diperhatikan

### Validasi Data:

1. **Email Harus Unik**
   - Tidak boleh ada email yang sama
   - Format email harus valid (contoh@domain.com)

2. **Kelas Harus Ada di Sistem**
   - Pastikan kelas sudah dibuat di menu "Manajemen Kelas"
   - Nama kelas harus persis sama (case sensitive)

3. **Format File**
   - Gunakan file .xlsx atau .xls
   - Baris pertama HARUS berisi header kolom
   - Jangan ada baris kosong di tengah data

4. **Karakter Khusus**
   - Hindari karakter khusus di nama (', ", \, dll)
   - Gunakan encoding UTF-8

### Jika Import Gagal:

1. **Cek Error Message**
   - Baca pesan error yang muncul
   - Biasanya menunjukkan baris yang bermasalah

2. **Validasi Data**
   - Pastikan email unik
   - Pastikan kelas ada di sistem
   - Pastikan format sesuai

3. **Coba Import Ulang**
   - Perbaiki data yang error
   - Import ulang file yang sudah diperbaiki

---

## 💡 TIPS & TRIK

### Untuk Import Banyak Data:

1. **Bagi Menjadi Batch**
   - Jangan import ribuan data sekaligus
   - Bagi menjadi 50-100 data per file
   - Lebih mudah tracking error

2. **Backup Data Lama**
   - Export data lama sebelum import baru
   - Jaga-jaga jika ada kesalahan

3. **Test dengan Sample**
   - Import 5-10 data dulu untuk test
   - Jika berhasil, baru import semua

### Untuk Menghindari Error:

1. **Gunakan Template**
   - Selalu download template terbaru
   - Jangan ubah nama kolom header

2. **Copy-Paste dari Excel Lain**
   - Paste ke template, bukan sebaliknya
   - Hindari format yang aneh

3. **Cek Kelas Dulu**
   - Pastikan semua kelas sudah dibuat
   - Buka menu "Manajemen Kelas" untuk cek

---

## 📊 CONTOH KASUS

### Kasus 1: Import 50 Murid Baru

1. Download template murid
2. Isi 50 baris data
3. Pastikan email unik semua
4. Pastikan kelas sesuai
5. Import file
6. Cek hasilnya di list murid

### Kasus 2: Import Guru Baru Tahun Ajaran

1. Download template guru
2. Isi data guru baru
3. Isi mata pelajaran dan kelas
4. Import file
5. Assign sebagai wali kelas jika perlu

### Kasus 3: Update Data Murid

1. Export data murid yang ada
2. Edit di Excel
3. Hapus data lama (optional)
4. Import data yang sudah diedit

---

## 🆘 TROUBLESHOOTING

### Error: "Email already exists"
**Solusi:** Email sudah ada di database, gunakan email lain

### Error: "Kelas not found"
**Solusi:** Buat kelas dulu di menu "Manajemen Kelas"

### Error: "Invalid file format"
**Solusi:** Pastikan file .xlsx atau .xls, bukan .csv

### Error: "Missing required field"
**Solusi:** Pastikan semua kolom required terisi

### Import Berhasil tapi Data Tidak Muncul
**Solusi:** Refresh halaman atau clear cache browser

---

## ✅ CHECKLIST SEBELUM IMPORT

- [ ] Template sudah didownload
- [ ] Data sudah diisi sesuai format
- [ ] Email semua unik
- [ ] Kelas sudah ada di sistem
- [ ] Tidak ada baris kosong
- [ ] Header kolom tidak diubah
- [ ] File format .xlsx atau .xls
- [ ] Sudah backup data lama (jika ada)

---

## 📞 BANTUAN

Jika masih ada masalah:
1. Cek dokumentasi lengkap di `FITUR_MANAJEMEN_USER_LENGKAP.md`
2. Lihat contoh template di folder `public/templates/`
3. Coba import dengan data sample dulu

**Status:** ✅ Fitur Import Excel Siap Digunakan
**Last Update:** 6 Desember 2025
