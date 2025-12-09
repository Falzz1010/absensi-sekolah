# Sistem Double Verification (Verifikasi Ganda)

## 🔒 Overview

Sistem Double Verification adalah fitur keamanan untuk mencegah kecurangan dalam absensi siswa. Setiap siswa **WAJIB** melakukan **2 jenis absensi** untuk dianggap hadir secara sah:

1. **QR Scan** - Scan QR Code di gerbang sekolah
2. **Absensi Manual** - Input manual melalui form di portal siswa

## 🎯 Tujuan

- ✅ Mencegah kecurangan (siswa tidak bisa hanya scan QR tanpa benar-benar hadir)
- ✅ Validasi ganda untuk memastikan kehadiran fisik
- ✅ Tracking lengkap dengan timestamp untuk setiap metode
- ✅ Fleksibilitas urutan (bisa QR dulu atau Manual dulu)

## 📊 Alur Sistem

### Skenario 1: QR Scan Dulu, Manual Kemudian

```
1. Siswa scan QR Code di gerbang
   ↓
   Status: ⚠️ Belum Lengkap (qr_scan_done=true, manual_checkin_done=false)
   Notifikasi: "QR Scan berhasil. WAJIB lakukan Absensi Manual!"
   
2. Siswa isi form Absensi Manual
   ↓
   Status: ✅ Lengkap (qr_scan_done=true, manual_checkin_done=true)
   Notifikasi: "Absensi LENGKAP! Kehadiran terverifikasi penuh."
```

### Skenario 2: Manual Dulu, QR Scan Kemudian

```
1. Siswa isi form Absensi Manual
   ↓
   Status: ⚠️ Belum Lengkap (manual_checkin_done=true, qr_scan_done=false)
   Notifikasi: "Absensi manual berhasil. WAJIB scan QR Code!"
   
2. Siswa scan QR Code di gerbang
   ↓
   Status: ✅ Lengkap (manual_checkin_done=true, qr_scan_done=true)
   Notifikasi: "Absensi LENGKAP! Kehadiran terverifikasi penuh."
```

## 🗄️ Database Schema

### Migration: `add_double_verification_to_absensis_table`

```php
Schema::table('absensis', function (Blueprint $table) {
    // QR Scan tracking
    $table->boolean('qr_scan_done')->default(false);
    $table->timestamp('qr_scan_time')->nullable();
    
    // Manual check-in tracking
    $table->boolean('manual_checkin_done')->default(false);
    $table->timestamp('manual_checkin_time')->nullable();
    
    // Completion status
    $table->boolean('is_complete')->default(false);
    
    // First method used
    $table->enum('first_method', ['qr_scan', 'manual'])->nullable();
});
```

### Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| `qr_scan_done` | boolean | True jika QR scan sudah dilakukan |
| `qr_scan_time` | timestamp | Waktu QR scan dilakukan |
| `manual_checkin_done` | boolean | True jika absensi manual sudah dilakukan |
| `manual_checkin_time` | timestamp | Waktu absensi manual dilakukan |
| `is_complete` | boolean | True jika KEDUA metode sudah dilakukan |
| `first_method` | enum | Metode pertama yang digunakan ('qr_scan' atau 'manual') |

## 💻 Implementasi

### 1. Model Absensi

```php
protected $fillable = [
    // ... existing fields
    'qr_scan_done',
    'qr_scan_time',
    'manual_checkin_done',
    'manual_checkin_time',
    'is_complete',
    'first_method'
];

protected $casts = [
    // ... existing casts
    'qr_scan_done' => 'boolean',
    'qr_scan_time' => 'datetime',
    'manual_checkin_done' => 'boolean',
    'manual_checkin_time' => 'datetime',
    'is_complete' => 'boolean',
];
```

### 2. QR Scan Controller Logic

**File**: `app/Http/Controllers/Api/QrScanController.php`

```php
// Check if record exists
$existingAbsensi = Absensi::where('murid_id', $murid->id)
    ->whereDate('tanggal', now()->toDateString())
    ->first();

if ($existingAbsensi) {
    // Update existing - add QR scan
    if ($existingAbsensi->qr_scan_done) {
        return error('QR Scan sudah dilakukan');
    }
    
    $existingAbsensi->update([
        'qr_scan_done' => true,
        'qr_scan_time' => now(),
        'is_complete' => true, // Both done
    ]);
    
    return success('✅ Absensi LENGKAP!');
} else {
    // Create new - QR scan first
    Absensi::create([
        'qr_scan_done' => true,
        'qr_scan_time' => now(),
        'manual_checkin_done' => false,
        'is_complete' => false,
        'first_method' => 'qr_scan',
    ]);
    
    return warning('⚠️ WAJIB lakukan Absensi Manual!');
}
```

### 3. Manual Attendance Page Logic

**File**: `app/Filament/Student/Pages/ManualAttendancePage.php`

```php
$existingAbsensi = Absensi::where('murid_id', $murid->id)
    ->whereDate('tanggal', $data['tanggal'])
    ->first();

if ($existingAbsensi) {
    // Update existing - add manual check-in
    if ($existingAbsensi->manual_checkin_done) {
        return error('Absensi manual sudah dilakukan');
    }
    
    $existingAbsensi->update([
        'manual_checkin_done' => true,
        'manual_checkin_time' => now(),
        'is_complete' => true, // Both done
    ]);
    
    return success('✅ Absensi LENGKAP!');
} else {
    // Create new - manual first
    Absensi::create([
        'manual_checkin_done' => true,
        'manual_checkin_time' => now(),
        'qr_scan_done' => false,
        'is_complete' => false,
        'first_method' => 'manual',
    ]);
    
    return warning('⚠️ WAJIB scan QR Code!');
}
```

### 4. Attendance History Display

**File**: `app/Filament/Student/Pages/AttendanceHistoryPage.php`

```php
TextColumn::make('is_complete')
    ->label('Verifikasi')
    ->badge()
    ->formatStateUsing(fn ($state) => $state ? '✅ Lengkap' : '⚠️ Belum Lengkap')
    ->color(fn ($state) => $state ? 'success' : 'warning')
    ->description(fn ($record) => $this->getVerificationDetails($record))

protected function getVerificationDetails($record): string
{
    $details = [];
    $details[] = $record->qr_scan_done ? '✓ QR Scan' : '✗ QR Scan';
    $details[] = $record->manual_checkin_done ? '✓ Manual' : '✗ Manual';
    return implode(' | ', $details);
}
```

## 🎨 UI/UX

### Manual Attendance Page

**Warning Banner** (Amber/Orange):
```
🔒 Sistem Verifikasi Ganda (Double Verification)

PENTING: Untuk mencegah kecurangan, Anda WAJIB melakukan 2 jenis absensi:
1. QR Scan - Scan QR Code di gerbang sekolah
2. Absensi Manual - Isi formulir di halaman ini

⚠️ Absensi Anda hanya dianggap SAH jika KEDUA metode telah dilakukan.
Urutan bebas, bisa QR dulu atau Manual dulu.
```

### Notifications

**QR Scan First (Incomplete)**:
```
⚠️ QR Scan berhasil. WAJIB lakukan Absensi Manual untuk melengkapi absensi Anda!
Type: warning
Duration: 8000ms
```

**Manual First (Incomplete)**:
```
⚠️ Absensi manual berhasil. WAJIB scan QR Code untuk melengkapi absensi Anda!
Type: warning
Duration: 8000ms
```

**Both Complete**:
```
✅ Absensi LENGKAP! Anda telah melakukan QR Scan dan Absensi Manual. 
Kehadiran Anda terverifikasi penuh.
Type: success
Duration: 8000ms
```

### Attendance History Table

| Tanggal | Status | Waktu | Verifikasi | Detail |
|---------|--------|-------|------------|--------|
| 09/12/2025 | Hadir | 07:15 | ✅ Lengkap | ✓ QR Scan \| ✓ Manual |
| 08/12/2025 | Hadir | 07:20 | ⚠️ Belum Lengkap | ✓ QR Scan \| ✗ Manual |

## 🔍 Validasi & Keamanan

### Validasi QR Scan

1. ✅ QR Code harus valid dan aktif
2. ✅ QR Code dalam periode berlaku
3. ✅ Murid terdaftar dan aktif
4. ✅ Dalam time window yang diizinkan
5. ✅ Cek duplikasi QR scan untuk hari yang sama

### Validasi Manual Check-in

1. ✅ User harus memiliki record Murid
2. ✅ Tanggal tidak boleh di masa depan
3. ✅ Cek duplikasi manual check-in untuk hari yang sama
4. ✅ Waktu check-in harus valid

### Keamanan

- **Authorization**: Hanya siswa yang login dapat akses
- **Data Integrity**: Foreign key constraints
- **Audit Trail**: Timestamp untuk setiap metode
- **Duplicate Prevention**: Validasi untuk mencegah double entry
- **Complete Status**: Flag `is_complete` untuk tracking

## 📈 Reporting & Analytics

### Query untuk Absensi Lengkap

```php
// Siswa dengan absensi lengkap hari ini
$complete = Absensi::whereDate('tanggal', today())
    ->where('is_complete', true)
    ->count();

// Siswa dengan absensi belum lengkap
$incomplete = Absensi::whereDate('tanggal', today())
    ->where('is_complete', false)
    ->count();

// Siswa yang hanya QR scan
$onlyQr = Absensi::whereDate('tanggal', today())
    ->where('qr_scan_done', true)
    ->where('manual_checkin_done', false)
    ->count();

// Siswa yang hanya manual
$onlyManual = Absensi::whereDate('tanggal', today())
    ->where('manual_checkin_done', true)
    ->where('qr_scan_done', false)
    ->count();
```

### Dashboard Widget Ideas

1. **Completion Rate**: Persentase siswa dengan absensi lengkap
2. **Incomplete Alert**: List siswa yang belum lengkap absensi
3. **Method Preference**: Chart QR first vs Manual first
4. **Time Gap**: Rata-rata waktu antara metode pertama dan kedua

## 🧪 Testing

### Test Cases

1. **QR Scan First, Then Manual**
   - [ ] QR scan creates incomplete record
   - [ ] Manual check-in completes the record
   - [ ] `is_complete` becomes true
   - [ ] Both timestamps recorded

2. **Manual First, Then QR Scan**
   - [ ] Manual creates incomplete record
   - [ ] QR scan completes the record
   - [ ] `is_complete` becomes true
   - [ ] Both timestamps recorded

3. **Duplicate Prevention**
   - [ ] Cannot QR scan twice
   - [ ] Cannot manual check-in twice
   - [ ] Proper error messages

4. **Notifications**
   - [ ] Warning when incomplete
   - [ ] Success when complete
   - [ ] Correct message content

5. **History Display**
   - [ ] Shows verification status
   - [ ] Shows method details
   - [ ] Correct badge colors

### Manual Testing Steps

```bash
# 1. Start server
php artisan serve

# 2. Login as student
# URL: http://localhost:8000/student

# 3. Test QR Scan First
# - Scan QR Code
# - Check notification (should be warning)
# - Go to Manual Attendance
# - Submit form
# - Check notification (should be success)
# - Check Riwayat Kehadiran (should show ✅ Lengkap)

# 4. Test Manual First (next day)
# - Go to Manual Attendance
# - Submit form
# - Check notification (should be warning)
# - Scan QR Code
# - Check notification (should be success)
# - Check Riwayat Kehadiran (should show ✅ Lengkap)

# 5. Test Duplicate Prevention
# - Try QR scan again (should fail)
# - Try manual check-in again (should fail)
```

## 📝 Admin Panel Integration

### View Incomplete Attendance

Admin/Guru dapat melihat siswa dengan absensi belum lengkap:

```php
// In AbsensiResource or custom page
TextColumn::make('is_complete')
    ->label('Status Verifikasi')
    ->badge()
    ->formatStateUsing(fn ($state) => $state ? 'Lengkap' : 'Belum Lengkap')
    ->color(fn ($state) => $state ? 'success' : 'danger')

// Filter for incomplete
SelectFilter::make('is_complete')
    ->label('Status Verifikasi')
    ->options([
        '1' => 'Lengkap',
        '0' => 'Belum Lengkap',
    ])
```

### Bulk Actions

Admin dapat mengirim reminder ke siswa dengan absensi belum lengkap:

```php
BulkAction::make('sendReminder')
    ->label('Kirim Reminder')
    ->action(function (Collection $records) {
        foreach ($records as $record) {
            if (!$record->is_complete) {
                // Send notification to student
                $record->murid->user->notify(
                    new IncompleteAttendanceReminder($record)
                );
            }
        }
    })
    ->requiresConfirmation()
```

## 🚀 Future Enhancements

1. **Time Limit**: Batas waktu untuk melengkapi absensi (misal: 2 jam)
2. **Geolocation**: Validasi lokasi untuk manual check-in
3. **Photo Verification**: Ambil foto saat manual check-in
4. **Auto-reminder**: Notifikasi otomatis jika belum lengkap
5. **Grace Period**: Toleransi waktu untuk melengkapi
6. **Admin Override**: Admin dapat mark as complete manual
7. **Statistics Dashboard**: Analytics lengkap untuk admin
8. **Export Report**: Laporan absensi dengan status verifikasi

## 📊 Status

✅ **IMPLEMENTED**

- [x] Database migration
- [x] Model updates
- [x] QR Scan controller logic
- [x] Manual Attendance page logic
- [x] Attendance History display
- [x] UI/UX with warnings
- [x] Notifications
- [x] Documentation

## 🎓 Kesimpulan

Sistem Double Verification memberikan lapisan keamanan tambahan untuk mencegah kecurangan dalam absensi. Dengan memerlukan 2 metode verifikasi (QR Scan + Manual), sistem memastikan bahwa siswa benar-benar hadir secara fisik di sekolah.

**Key Benefits**:
- 🔒 Keamanan tinggi
- ✅ Validasi ganda
- 📊 Tracking lengkap
- 🎯 Fleksibel (urutan bebas)
- 📱 User-friendly
- 🚨 Notifikasi jelas

---

**Created**: December 9, 2025
**Version**: 1.0.0
**Status**: Production Ready
