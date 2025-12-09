# Fix: 403 Forbidden di Student Panel

## Masalah
User dengan role 'murid' mendapat error 403 Forbidden saat login ke Student Panel.

## Penyebab
1. Cache role/permission tidak ter-refresh
2. Session issue
3. Auth guard mismatch

## Solusi

### 1. Clear All Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
php artisan route:clear
php artisan view:clear
```

### 2. Verifikasi User Access
```bash
php check-student-access.php
```

Output seharusnya:
```
✓ User CAN access Student Panel
```

### 3. Coba Login Lagi
1. **Logout** dari semua panel (jika sudah login)
2. **Clear browser cache** dan cookies
3. **Buka incognito/private window**
4. **Login** ke `/student/login` dengan:
   - Email: `murid@example.com`
   - Password: `password`

### 4. Jika Masih 403

#### Cek A: Apakah user sudah logout dari Admin Panel?
Jika user masih login sebagai Admin, logout dulu:
- Buka `/admin/logout`
- Atau clear semua cookies browser

#### Cek B: Apakah auth guard benar?
StudentPanelProvider menggunakan `authGuard('web')`. Pastikan user login dengan guard yang sama.

#### Cek C: Apakah middleware berfungsi?
Cek log Laravel saat login:
```bash
tail -f storage/logs/laravel.log
```

Cari error atau warning terkait authentication.

#### Cek D: Test manual canAccessPanel
```bash
php artisan tinker
>>> $user = App\Models\User::where('email', 'murid@example.com')->first();
>>> $panel = app(\Filament\Facades\Filament::class)->getPanel('student');
>>> $user->canAccessPanel($panel);
```

Seharusnya return `true`.

## Alternatif: Buat User Baru

Jika masih bermasalah, buat user murid baru:

```bash
php artisan tinker
>>> $user = App\Models\User::create([
...     'name' => 'Test Murid',
...     'email' => 'test@murid.com',
...     'password' => bcrypt('password123'),
... ]);
>>> $user->assignRole('murid');
>>> App\Models\Murid::create([
...     'user_id' => $user->id,
...     'name' => 'Test Murid',
...     'email' => 'test@murid.com',
...     'kelas' => 'Test',
...     'is_active' => true,
... ]);
```

Lalu login dengan:
- Email: `test@murid.com`
- Password: `password123`

## Checklist Troubleshooting

- [ ] Clear all cache (cache, config, permission, route, view)
- [ ] Verify user has correct roles (murid/student)
- [ ] Verify user has Murid record
- [ ] Verify user does NOT have admin/guru role
- [ ] Logout from all panels
- [ ] Clear browser cache and cookies
- [ ] Try incognito/private window
- [ ] Check Laravel log for errors
- [ ] Test canAccessPanel manually

## Status Saat Ini

✅ User `murid@example.com` memenuhi semua kondisi:
- Has 'murid' role: YES
- Has 'student' role: YES
- Has Murid record: YES
- Does NOT have admin/guru role: YES

✅ Cache sudah di-clear

⚠️ **Next Step**: Login ulang ke Student Panel dengan incognito window

## Jika Masih Bermasalah

Kemungkinan ada issue di:
1. **Middleware CheckStudentRole** - Cek apakah middleware ini memblokir akses
2. **Panel configuration** - Cek StudentPanelProvider
3. **Database session** - Cek apakah session tersimpan dengan benar

Untuk debug lebih lanjut, tambahkan logging di `canAccessPanel`:
```php
public function canAccessPanel(\Filament\Panel $panel): bool
{
    if ($panel->getId() === 'student') {
        $hasRole = $this->hasAnyRole(['student', 'murid']);
        $noAdminRole = !$this->hasAnyRole(['admin', 'guru']);
        $hasMurid = \App\Models\Murid::where('user_id', $this->id)->exists();
        
        \Log::info('Student Panel Access Check', [
            'user_id' => $this->id,
            'email' => $this->email,
            'has_role' => $hasRole,
            'no_admin_role' => $noAdminRole,
            'has_murid' => $hasMurid,
            'result' => $hasRole && $noAdminRole && $hasMurid,
        ]);
        
        return $hasRole && $noAdminRole && $hasMurid;
    }
    // ...
}
```

Lalu cek log saat login untuk melihat nilai setiap kondisi.
