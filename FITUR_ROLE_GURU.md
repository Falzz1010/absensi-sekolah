# 🧑‍🏫 Fitur Role GURU - Status & Implementation

## 📊 Status Overview

| Fitur | Status | Notes |
|-------|--------|-------|
| A. Absensi Kelas | ✅ Partial | Perlu filter by kelas guru |
| B. Laporan Absensi Murid | ✅ Yes | Sudah ada |
| C. Wali Kelas Fitur | ⏳ Partial | Perlu enhancement |

---

## 🔹 A. Absensi Kelas

### ✅ Yang Sudah Ada:

#### 1. **Input Absensi Kelas**
- **File:** `app/Filament/Pages/InputAbsensiKelas.php`
- **Access:** ✅ Admin & Guru
- **Features:**
  - Pilih kelas
  - Pilih tanggal
  - Input status per murid (Hadir/Sakit/Izin/Alfa)
  - Bulk save

**Status:** ✅ Working

#### 2. **Akses QR Code**
- **File:** `app/Filament/Resources/QrCodeResource.php`
- **Access:** ❌ Admin only (need to add guru)
- **Features:**
  - View QR codes
  - Download QR codes
  - Scan via API

**Status:** ⚠️ Need to add guru access

#### 3. **Scan QR via HP**
- **File:** `app/Http/Controllers/Api/QrScanController.php`
- **Access:** ✅ Public API
- **Features:**
  - POST `/api/qr-scan`
  - Real-time notification
  - Auto create absensi

**Status:** ✅ Working

### ⏳ Yang Perlu Ditambahkan:

1. **Filter Absensi by Kelas Guru**
   - Guru hanya lihat absensi kelas yang dia ajar
   - Filter otomatis berdasarkan jadwal guru

2. **QR Code Access untuk Guru**
   - Guru bisa lihat QR code kelasnya
   - Guru bisa download QR code

---

## 🔹 B. Laporan Absensi Murid

### ✅ Yang Sudah Ada:

#### 1. **Laporan Kehadiran**
- **File:** `app/Filament/Resources/LaporanKehadiranResource.php`
- **Access:** ✅ Admin & Guru
- **Features:**
  - View riwayat absensi
  - Filter by tanggal, status, kelas
  - Export Excel
  - Export PDF

**Status:** ✅ Working

#### 2. **Laporan Harian**
- **File:** `app/Filament/Pages/LaporanHarian.php`
- **Access:** ✅ All (need to add role check)
- **Features:**
  - Filter by tanggal & kelas
  - Summary cards
  - Detail per kelas
  - Persentase kehadiran

**Status:** ✅ Working (need role check)

#### 3. **Edit Absensi**
- **File:** `app/Filament/Resources/AbsensiResource.php`
- **Access:** ✅ Admin & Guru
- **Features:**
  - Edit status absensi
  - Ubah tanggal
  - Update kelas

**Status:** ✅ Working

### ⏳ Yang Perlu Ditambahkan:

1. **Catatan Tambahan**
   - Field `keterangan` di tabel absensis
   - Guru bisa tambah catatan (misal: "izin ke dokter")

2. **Rekap Per Murid**
   - Page khusus untuk lihat rekap per murid
   - Statistik kehadiran murid
   - History lengkap

---

## 🔹 C. Wali Kelas Fitur

### ✅ Yang Sudah Ada:

#### 1. **Relationship Wali Kelas**
- **File:** `app/Models/Kelas.php`
- **Relationship:** `belongsTo(Guru, 'wali_kelas_id')`
- **Status:** ✅ Database ready

#### 2. **Dashboard Widgets**
- **Files:** Various widgets
- **Access:** ✅ All users
- **Features:**
  - Stats overview
  - Charts
  - Rekap mingguan/bulanan

**Status:** ✅ Working

### ⏳ Yang Perlu Ditambahkan:

1. **Dashboard Wali Kelas**
   - Page khusus untuk wali kelas
   - Lihat absensi lengkap kelas
   - Rekap kehadiran bulanan
   - Statistik per murid

2. **Rekap Bulanan Kelas**
   - Export rekap bulanan
   - Persentase kehadiran per murid
   - Grafik trend kehadiran

3. **Input Keterangan Murid**
   - Tambah catatan untuk murid
   - History catatan
   - Notifikasi ke admin

---

## 🚀 Implementation Plan

### Priority 1: Essential Features (Quick Wins)

#### 1. Add Guru Access to QR Code
```php
// app/Filament/Resources/QrCodeResource.php
public static function canViewAny(): bool
{
    return auth()->user()->hasRole(['admin', 'guru']);
}
```

#### 2. Add Role Check to Laporan Harian
```php
// app/Filament/Pages/LaporanHarian.php
public static function canAccess(): bool
{
    return auth()->user()->hasRole(['admin', 'guru']);
}
```

#### 3. Add Keterangan Field to Absensi
```php
// Migration
Schema::table('absensis', function (Blueprint $table) {
    $table->text('keterangan')->nullable()->after('status');
});

// Model
protected $fillable = [..., 'keterangan'];
```

### Priority 2: Enhanced Features

#### 4. Filter Absensi by Guru's Kelas
```php
// app/Filament/Resources/AbsensiResource.php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    
    if (auth()->user()->hasRole('guru')) {
        $guru = Guru::where('user_id', auth()->id())->first();
        if ($guru) {
            $kelasGuru = Jadwal::where('guru_id', $guru->id)
                ->pluck('kelas')
                ->unique();
            $query->whereIn('kelas', $kelasGuru);
        }
    }
    
    return $query;
}
```

#### 5. Dashboard Wali Kelas
Create new page: `app/Filament/Pages/DashboardWaliKelas.php`

### Priority 3: Advanced Features

#### 6. Rekap Per Murid Page
#### 7. Catatan Murid Management
#### 8. Export Rekap Bulanan

---

## 📝 Current Implementation Status

### ✅ Working Features for Guru:

1. **Input Absensi Kelas**
   - ✅ Pilih kelas & tanggal
   - ✅ Input status per murid
   - ✅ Bulk save

2. **View Absensi**
   - ✅ Lihat semua absensi (need filter)
   - ✅ Edit absensi
   - ✅ Real-time updates

3. **Laporan**
   - ✅ Laporan kehadiran
   - ✅ Laporan harian
   - ✅ Export Excel/PDF

4. **QR Code Scan**
   - ✅ API endpoint working
   - ✅ Real-time notifications

### ⚠️ Needs Enhancement:

1. **QR Code Access**
   - ❌ Guru can't view QR codes yet
   - Need to add role check

2. **Data Filtering**
   - ❌ Guru sees all classes
   - Need to filter by guru's classes

3. **Keterangan Field**
   - ❌ No field for additional notes
   - Need migration & form update

4. **Wali Kelas Dashboard**
   - ❌ No dedicated dashboard
   - Need new page

---

## 🎯 Recommended Next Steps

### Step 1: Quick Fixes (30 minutes)
1. Add guru access to QR Code resource
2. Add role check to Laporan Harian
3. Test guru login and access

### Step 2: Add Keterangan Field (1 hour)
1. Create migration for keterangan field
2. Update Absensi model
3. Update forms to include keterangan
4. Test input & display

### Step 3: Filter by Guru's Classes (2 hours)
1. Update AbsensiResource query
2. Update LaporanKehadiranResource query
3. Test filtering
4. Ensure guru only sees their classes

### Step 4: Wali Kelas Features (3 hours)
1. Create DashboardWaliKelas page
2. Add widgets for wali kelas
3. Add rekap bulanan
4. Test wali kelas access

---

## 📊 Feature Comparison

| Feature | Admin | Guru | Wali Kelas |
|---------|-------|------|------------|
| Input Absensi | ✅ All | ✅ Own | ✅ Own |
| View Absensi | ✅ All | ⚠️ All* | ✅ Own |
| Edit Absensi | ✅ All | ✅ Own | ✅ Own |
| QR Code | ✅ All | ❌ Need | ✅ Own |
| Laporan | ✅ All | ✅ All | ✅ Own |
| Dashboard | ✅ Full | ✅ Basic | ⏳ Need |
| Keterangan | ⏳ Need | ⏳ Need | ⏳ Need |

*Need to filter by guru's classes

---

## 🔧 Code Snippets

### Add Guru Access to QR Code
```php
// app/Filament/Resources/QrCodeResource.php
public static function canViewAny(): bool
{
    return auth()->user()->hasRole(['admin', 'guru']);
}

// Only show QR codes for guru's classes
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    
    if (auth()->user()->hasRole('guru')) {
        $guru = Guru::where('user_id', auth()->id())->first();
        if ($guru) {
            $kelasGuru = Jadwal::where('guru_id', $guru->id)
                ->pluck('kelas')
                ->unique();
            
            $query->where(function($q) use ($kelasGuru) {
                $q->where('tipe', 'global')
                  ->orWhereIn('kelas', $kelasGuru);
            });
        }
    }
    
    return $query;
}
```

### Add Keterangan to Absensi Form
```php
// app/Filament/Resources/AbsensiResource.php
Forms\Components\Textarea::make('keterangan')
    ->label('Keterangan')
    ->placeholder('Tambahkan catatan (opsional)')
    ->rows(3)
    ->columnSpanFull(),
```

---

## ✅ Conclusion

**Current Status:**
- ✅ Basic features working for guru
- ⚠️ Need filtering by guru's classes
- ⏳ Need wali kelas specific features
- ⏳ Need keterangan field

**Priority:**
1. Add guru access to QR Code (5 min)
2. Add keterangan field (30 min)
3. Filter by guru's classes (1 hour)
4. Wali kelas dashboard (3 hours)

**Estimated Total Time:** 4-5 hours for complete implementation

---

**Last Updated:** December 6, 2025  
**Status:** Partial Implementation  
**Next Action:** Implement Priority 1 features
