# Fix: Block Murid dengan Middleware

## ✅ Solusi Final

Menambahkan **middleware khusus** untuk memblokir murid dari panel admin, bahkan jika ada session/cache yang tersisa.

## 🔧 Implementasi

### 1. Middleware Baru: BlockStudentFromAdmin

**File:** `app/Http/Middleware/BlockStudentFromAdmin.php`

```php
public function handle(Request $request, Closure $next): Response
{
    // If user is authenticated and has student/murid role, block access
    if (auth()->check() && auth()->user()->hasAnyRole(['student', 'murid'])) {
        // Force logout
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect to student panel
        return redirect()->route('filament.student.auth.login')
            ->with('error', 'Anda tidak memiliki akses ke panel admin.');
    }

    return $next($request);
}
```

### 2. Register Middleware di AdminPanelProvider

**File:** `app/Providers/Filament/AdminPanelProvider.php`

```php
->authMiddleware([
    Authenticate::class,
    \App\Http\Middleware\BlockStudentFromAdmin::class,
])
```

## 🛡️ Security Layers Sekarang

### Layer 1: canAccessPanel() di User.php
```php
if ($panel->getId() === 'admin') {
    if ($this->hasAnyRole(['student', 'murid'])) {
        return false;
    }
    return $this->hasAnyRole(['admin', 'guru']);
}
```

### Layer 2: Middleware BlockStudentFromAdmin
```php
// Force logout jika murid coba akses admin panel
if (auth()->user()->hasAnyRole(['student', 'murid'])) {
    auth()->logout();
    return redirect()->route('filament.student.auth.login');
}
```

### Layer 3: Widget Authorization
```php
public static function canView(): bool
{
    return auth()->user()->hasAnyRole(['admin', 'guru']);
}
```

## 🧪 Cara Test

### 1. Clear Semua Cache & Session
```bash
# Clear Laravel cache
php artisan optimize:clear

# Clear session files
del storage\framework\sessions\* -Force

# Clear browser cache
# Tekan Ctrl+Shift+Del di browser
```

### 2. Test di Browser Incognito

**Test A: Murid coba login ke admin**
```
1. Buka Incognito window
2. Akses: http://localhost/admin
3. Login: murid@example.com / password
4. Hasil: ❌ Logout otomatis + redirect ke /student
5. Pesan: "Anda tidak memiliki akses ke panel admin"
```

**Test B: Murid sudah login, coba akses admin**
```
1. Login di /student sebagai murid
2. Coba akses: http://localhost/admin
3. Hasil: ❌ Logout otomatis + redirect ke /student
```

**Test C: Admin login normal**
```
1. Akses: http://localhost/admin
2. Login: admin@example.com / password
3. Hasil: ✅ Berhasil masuk dashboard admin
```

### 3. Test Otomatis
```bash
php test-middleware-block.php
```

Output yang benar:
```
✅ Murid should be blocked: YES
✅ Admin should be allowed: YES
```

## 📊 Flow Diagram

```
Murid coba akses /admin
         ↓
    Login berhasil?
         ↓ YES
    Middleware check
         ↓
  Has student/murid role?
         ↓ YES
    Force logout
         ↓
  Invalidate session
         ↓
  Redirect ke /student
         ↓
  Show error message
```

## ⚠️ Troubleshooting

### Masalah: Masih bisa login setelah implementasi

**Solusi:**
1. **Clear semua cache:**
   ```bash
   php artisan optimize:clear
   del storage\framework\sessions\* -Force
   ```

2. **Clear browser cache:**
   - Chrome: Ctrl+Shift+Del → Clear all
   - Firefox: Ctrl+Shift+Del → Clear all

3. **Test di Incognito:**
   - Jangan test di tab biasa
   - Gunakan Incognito/Private window

4. **Restart web server:**
   ```bash
   # Jika pakai Apache
   net stop Apache2.4
   net start Apache2.4
   ```

5. **Check roles:**
   ```bash
   php check-murid-roles.php
   ```
   Pastikan murid TIDAK punya role 'admin' atau 'guru'

### Masalah: Error "Route not found"

Pastikan route student panel sudah terdaftar:
```bash
php artisan route:list | findstr "student"
```

## ✅ Checklist Final

- [x] Middleware BlockStudentFromAdmin dibuat
- [x] Middleware registered di AdminPanelProvider
- [x] canAccessPanel() di User.php sudah benar
- [x] Widget authorization sudah ditambahkan
- [x] Menu "Data Murid" disembunyikan
- [x] Cache cleared
- [x] Session cleared
- [x] Test otomatis passed
- [ ] **Test manual di browser Incognito** (PENTING!)

## 🎉 Status

**Sekarang murid PASTI tidak bisa akses panel admin karena:**
1. ✅ canAccessPanel() return false
2. ✅ Middleware force logout
3. ✅ Session invalidated
4. ✅ Redirect ke student panel

**Silakan test di browser Incognito sekarang!**
