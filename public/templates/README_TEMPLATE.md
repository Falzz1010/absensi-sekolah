# Template Import Murid

## Format Excel

Buat file Excel dengan kolom berikut:

| nama | email | kelas | status |
|------|-------|-------|--------|
| Ahmad Fauzi | ahmad@student.com | X IPA 1 | 1 |
| Siti Nurhaliza | siti@student.com | X IPS 1 | 1 |
| Budi Santoso | budi@student.com | XI IPA 1 | 1 |

## Keterangan Kolom:

1. **nama** (Required)
   - Nama lengkap murid
   - Contoh: Ahmad Fauzi

2. **email** (Required)
   - Email unik untuk setiap murid
   - Contoh: ahmad@student.com

3. **kelas** (Required)
   - Nama kelas sesuai yang ada di sistem
   - Contoh: X IPA 1, XI IPS 2, XII IPA 1

4. **status** (Optional)
   - 1 = Aktif
   - 0 = Tidak Aktif
   - Default: 1 (Aktif)

## Daftar Kelas yang Tersedia:

- X IPA 1, X IPA 2
- X IPS 1, X IPS 2
- XI IPA 1, XI IPA 2
- XI IPS 1, XI IPS 2
- XII IPA 1, XII IPA 2
- XII IPS 1, XII IPS 2

## Contoh Data:

```
nama,email,kelas,status
Ahmad Fauzi,ahmad.fauzi@student.com,X IPA 1,1
Siti Nurhaliza,siti.nur@student.com,X IPA 1,1
Budi Santoso,budi.santoso@student.com,X IPS 1,1
Dewi Lestari,dewi.lestari@student.com,XI IPA 1,1
Rizki Pratama,rizki.pratama@student.com,XI IPS 1,1
```

## Cara Import:

1. Buka menu **Akademik > Data Murid**
2. Klik tombol **Import Excel**
3. Upload file Excel
4. Klik **Import**
5. Tunggu proses selesai

## Catatan Penting:

- Pastikan email unik (tidak duplikat)
- Pastikan nama kelas sesuai dengan yang ada di sistem
- Baris pertama harus berisi header kolom
- Gunakan format .xlsx atau .xls
