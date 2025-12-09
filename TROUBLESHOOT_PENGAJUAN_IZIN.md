# Troubleshooting: Pengajuan Izin/Sakit Tidak Muncul

## Problem
Pengajuan izin/sakit sudah dikirim dari Student Panel tapi tidak muncul di menu "Pengajuan Izin/Sakit" di Admin/Guru Panel.

## Diagnosis

### 1. Check Database ✅
```bash
php check-pengajuan.php
```

**Result**: 
- Total Pengajuan: 1
- Data test berhasil dibuat
- Query resource berfungsi dengan benar

### 2. Possible Causes

#### A. Menu Belum Muncul (Cache Issue)
**Symptoms**:
- Menu "Pengajuan Izin/Sakit" tidak ada di sidebar
- Badge notification tidak muncul

**Solution**:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Refresh browser (Ctrl+Shift+R)
```

#### B. Pengajuan Gagal Submit
**Symptoms**:
- Form submit tapi tidak ada notifikasi sukses
- Data tidak masuk database
- Ada error di console browser

**Check**:
1. Buka browser console (F12)
2. Submit form pengajuan
3. Lihat ada error atau tidak

**Common Errors**:
- CORS error (sudah fixed)
- Validation error
- File upload error
- Database constraint error

#### C. Data Tidak Sesuai Filter
**Symptoms**:
- Data ada di database
- Tapi tidak muncul di resource

**Check Query**:
```php
// Resource query
Absensi::whereNotNull('verification_status')
    ->whereIn('status', ['Sakit', 'Izin'])
    ->with(['murid'])
    ->latest('tanggal')
```

**Requirements**:
- `verification_status` must NOT be NULL
- `status` must be 'Sakit' or 'Izin' (case-sensitive!)
- Must have related `murid`

## Solutions

### Solution 1: Clear Cache & Refresh
```bash
# Backend
php artisan optimize:clear

# Frontend
1. Hard refresh browser (Ctrl+Shift+R)
2. Or clear browser cache
3. Or use incognito mode
```

### Solution 2: Check Menu Registration
Menu should appear in sidebar under "Manajemen Absensi".

**Check**:
1. Login as Admin/Guru
2. Look for "Pengajuan Izin/Sakit" menu
3. Should have badge with pending count

**If not visible**:
- Resource might not be registered
- Check `PengajuanIzinResource.php` exists
- Check navigation group and sort

### Solution 3: Test Manual Submit
```bash
# Create test pengajuan
php test-create-pengajuan.php

# Check if it appears
php check-pengajuan.php
```

### Solution 4: Check Student Panel Submit

**Steps**:
1. Login as student
2. Go to "Ajukan Izin/Sakit"
3. Fill form:
   - Jenis: Sakit or Izin
   - Tanggal: Today or past date
   - Alasan: Any text
   - Bukti: Upload image
4. Click submit
5. Check for success notification

**Expected**:
- Green notification: "Pengajuan berhasil dikirim"
- Form resets
- No errors in console

**If fails**:
- Check browser console for errors
- Check Laravel log: `storage/logs/laravel.log`
- Check file upload works (CORS fixed)

## Verification Steps

### Step 1: Verify Data in Database
```bash
php check-pengajuan.php
```

Should show:
- Total Pengajuan: X (> 0)
- List of pengajuan with details
- Query resource count matches

### Step 2: Verify Menu Appears
1. Login as Admin/Guru
2. Check sidebar
3. Look for "Pengajuan Izin/Sakit" under "Manajemen Absensi"
4. Badge should show pending count

### Step 3: Verify Data Displays
1. Click menu "Pengajuan Izin/Sakit"
2. Should see table with data
3. Tabs: Semua, Menunggu, Disetujui, Ditolak
4. Default tab: "Menunggu Verifikasi"

### Step 4: Test Actions
1. Click "View" on one record
2. Should see full details
3. Click "Setujui" or "Tolak"
4. Should work without errors

## Common Issues & Fixes

### Issue 1: Menu Not Visible
**Fix**:
```bash
php artisan optimize:clear
# Refresh browser
```

### Issue 2: Empty Table
**Check**:
- Is there data in database? Run `php check-pengajuan.php`
- Is status exactly 'Sakit' or 'Izin'? (case-sensitive)
- Is verification_status NOT NULL?

### Issue 3: Badge Shows 0
**Reason**: No pending submissions

**Check**:
```sql
SELECT COUNT(*) FROM absensis 
WHERE verification_status = 'pending';
```

### Issue 4: Cannot Submit from Student Panel
**Checks**:
1. File upload works? (CORS fixed)
2. Form validation passes?
3. Murid record exists?
4. No duplicate for same date?

## Test Data

Created test pengajuan:
```
ID: 157
Murid: Murid Satu
Kelas: 10 IPA
Status: Sakit
Verification Status: pending
Tanggal: 2025-12-09
```

This should appear in Admin Panel.

## Quick Fix Commands

```bash
# Full reset
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

# Check data
php check-pengajuan.php

# Create test data
php test-create-pengajuan.php
```

## Expected Behavior

### Student Side:
1. Fill form "Ajukan Izin/Sakit"
2. Upload bukti (surat dokter/izin)
3. Submit
4. See success notification
5. Status: "Menunggu Verifikasi"

### Admin/Guru Side:
1. See badge notification (pending count)
2. Click menu "Pengajuan Izin/Sakit"
3. See list of submissions
4. Can approve/reject
5. Can view proof document

## Status

✅ **Backend**: Working (test data created successfully)
⏳ **Frontend**: Need to verify menu appears and data displays
⏳ **Student Submit**: Need to test actual submission from Student Panel

## Next Steps

1. **Clear cache** and refresh browser
2. **Check menu** appears in Admin Panel
3. **Test submit** from Student Panel
4. **Verify** data appears in table
5. **Test actions** (approve/reject)
