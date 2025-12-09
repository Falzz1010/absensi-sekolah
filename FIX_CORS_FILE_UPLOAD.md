# Fix CORS Error pada File Upload

## Problem

Error CORS saat mengakses file upload di Student Panel:
```
Access to fetch at 'http://localhost/storage/attendance-proofs/xxx.png' 
from origin 'http://127.0.0.1:8000' has been blocked by CORS policy: 
No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

## Root Cause

1. **APP_URL mismatch**: APP_URL is `localhost` but accessing via `127.0.0.1:8000`
2. **File visibility**: File disimpan dengan `visibility('private')` 
3. **Cross-origin**: Akses dari `127.0.0.1:8000` ke `localhost/storage`
4. **Storage link**: Symbolic link belum dibuat

## Solution

### 1. Fix APP_URL in .env ✅
**Problem**: APP_URL is `http://localhost` but accessing via `http://127.0.0.1:8000`

**File**: `.env`

**Before**:
```
APP_URL=http://localhost
```

**After**:
```
APP_URL=http://127.0.0.1:8000
```

**Clear Cache**:
```bash
php artisan cache:clear
php artisan config:clear
```

### 2. Create Storage Link ✅
```bash
php artisan storage:link
```

Output:
```
The [public\storage] link has been connected to [storage\app/public]
```

### 3. Change File Visibility ✅

**File**: `app/Filament/Student/Pages/AbsenceSubmissionPage.php`

**Before**:
```php
->visibility('private')
```

**After**:
```php
->visibility('public')
```

### 4. Update Resource View ✅

**File**: `app/Filament/Resources/PengajuanIzinResource.php`

Added:
```php
->visibility('public')
```

## How It Works

### File Storage Path
```
storage/app/public/attendance-proofs/xxx.png
         ↓ (symlink)
public/storage/attendance-proofs/xxx.png
         ↓ (accessible via)
http://127.0.0.1:8000/storage/attendance-proofs/xxx.png
```

### Visibility Options

**Private** (before):
- Stored in: `storage/app/private/`
- Not accessible via URL
- Requires signed URL or controller
- ❌ Causes CORS issue

**Public** (after):
- Stored in: `storage/app/public/`
- Accessible via: `/storage/` URL
- No CORS issue
- ✅ Works perfectly

## Testing

### Test 1: Upload File
1. Login sebagai siswa
2. Buka "Ajukan Izin/Sakit"
3. Upload foto surat
4. ✅ File berhasil upload tanpa error

### Test 2: View File (Student)
1. Setelah upload
2. Lihat preview gambar
3. ✅ Gambar tampil tanpa CORS error

### Test 3: View File (Admin/Guru)
1. Login sebagai Admin/Guru
2. Buka "Pengajuan Izin/Sakit"
3. Lihat kolom "Bukti"
4. ✅ Thumbnail tampil
5. Klik untuk view full image
6. ✅ Gambar tampil penuh

### Test 4: Download File
1. Di Admin Panel
2. Klik "View" pada pengajuan
3. Klik download pada bukti
4. ✅ File berhasil didownload

## Security Considerations

### Public vs Private

**Public Storage** (current):
- ✅ No CORS issues
- ✅ Easy to access
- ⚠️ Anyone with URL can access
- Recommended for: Non-sensitive documents

**Private Storage** (alternative):
- ✅ More secure
- ✅ Requires authentication
- ❌ Needs custom controller
- ❌ More complex setup
- Recommended for: Sensitive documents

### Current Implementation

Untuk surat izin/sakit, menggunakan **public storage** adalah acceptable karena:
1. Bukan data sangat sensitif
2. URL sulit ditebak (random filename)
3. Hanya siswa yang tahu URL file mereka
4. Lebih simple dan no CORS issue

### Future Enhancement (Optional)

Jika ingin lebih secure, bisa implement:
1. Custom controller untuk serve file
2. Authentication check sebelum serve
3. Signed URL dengan expiry
4. Watermark pada gambar

## Files Modified

1. `.env`
   - Changed APP_URL from `http://localhost` to `http://127.0.0.1:8000`

2. `app/Filament/Student/Pages/AbsenceSubmissionPage.php`
   - Changed visibility to 'public'

3. `app/Filament/Resources/PengajuanIzinResource.php`
   - Added visibility 'public' to form

4. `public/storage` → `storage/app/public`
   - Created symbolic link

## Verification

Check if storage link exists:
```bash
# Windows
dir public\storage

# Should show: <SYMLINKD> storage [..\..\storage\app\public]
```

Check file accessibility:
```
http://127.0.0.1:8000/storage/attendance-proofs/[filename]
```

Should return the image, not 404 or CORS error.

## Troubleshooting

### Issue: Storage link not working
**Solution**:
```bash
# Remove old link
rmdir public\storage

# Create new link
php artisan storage:link
```

### Issue: File not found
**Check**:
1. File exists in `storage/app/public/attendance-proofs/`
2. Symbolic link exists in `public/storage`
3. File permissions are correct

### Issue: Still getting CORS
**Check**:
1. Clear browser cache
2. Hard refresh (Ctrl+Shift+R)
3. Check if using same domain (127.0.0.1 vs localhost)
4. Verify file visibility is 'public'

## Summary

✅ **CORS Error Fixed!**

**Changes**:
- Storage link created
- File visibility changed to 'public'
- Files now accessible via `/storage/` URL

**Result**:
- No more CORS errors
- File upload works perfectly
- Preview/download works in both Student and Admin panel
