# ✅ SweetAlert2 Integration - Alert System Profesional

## 🎯 Overview

Sistem alert menggunakan SweetAlert2 yang lebih cantik, profesional, dan user-friendly dibanding alert biasa.

## 📦 Installation

```bash
npm install sweetalert2
npm run build
```

## 🎨 Fitur Alert

### 1. Success Alert
```javascript
alertSuccess('Berhasil!', 'Data berhasil disimpan');
```

### 2. Error Alert
```javascript
alertError('Error!', 'Terjadi kesalahan saat menyimpan data');
```

### 3. Warning Alert
```javascript
alertWarning('Peringatan!', 'Harap isi semua field yang required');
```

### 4. Info Alert
```javascript
alertInfo('Informasi', 'Sistem akan maintenance besok');
```

### 5. Confirm Dialog
```javascript
alertConfirm('Konfirmasi', 'Apakah Anda yakin?').then((result) => {
    if (result.isConfirmed) {
        // User klik Ya
    }
});
```

### 6. Delete Confirmation
```javascript
alertDelete('Hapus Data?', 'Data tidak dapat dikembalikan').then((result) => {
    if (result.isConfirmed) {
        // Hapus data
    }
});
```

### 7. Loading Alert
```javascript
alertLoading('Memproses...', 'Mohon tunggu');
// Tutup loading
Swal.close();
```

### 8. Toast Notifications (Pojok Kanan Atas)
```javascript
toast('success', 'Data berhasil disimpan');
toastSuccess('Berhasil!');
toastError('Gagal!');
toastWarning('Peringatan!');
toastInfo('Informasi');
```

### 9. Logout Confirmation
```javascript
confirmLogout().then((result) => {
    if (result.isConfirmed) {
        // Logout
    }
});
```

### 10. Auto Logout Warning
```javascript
showAutoLogoutWarning(120); // 120 detik
```

### 11. Session Expired
```javascript
showSessionExpired(); // Auto redirect ke login
```

## 🔧 Penggunaan di Livewire/Filament

### Dari PHP (Livewire Component)
```php
$this->dispatch('alert-success', [
    'title' => 'Berhasil!',
    'message' => 'Data berhasil disimpan'
]);

$this->dispatch('alert-error', [
    'title' => 'Error!',
    'message' => 'Terjadi kesalahan'
]);

$this->dispatch('toast', [
    'type' => 'success',
    'message' => 'Data berhasil disimpan'
]);
```

### Dari JavaScript
```javascript
// Success
alertSuccess('Berhasil!', 'Data tersimpan');

// Error
alertError('Error!', 'Gagal menyimpan');

// Confirm
alertConfirm('Hapus?', 'Yakin hapus data ini?').then((result) => {
    if (result.isConfirmed) {
        // Lakukan penghapusan
        Livewire.dispatch('delete-item', { id: 123 });
    }
});

// Toast
toastSuccess('Data berhasil disimpan!');
```

## 🎬 Contoh Implementasi

### 1. Logout Button
```html
<button onclick="confirmLogout().then((result) => {
    if (result.isConfirmed) {
        document.getElementById('logout-form').submit();
    }
})">
    Logout
</button>
```

### 2. Delete Button
```html
<button onclick="alertDelete().then((result) => {
    if (result.isConfirmed) {
        Livewire.dispatch('delete-record', { id: {{ $record->id }} });
    }
})">
    Hapus
</button>
```

### 3. Form Submit dengan Loading
```javascript
function submitForm() {
    alertLoading('Menyimpan...', 'Mohon tunggu');
    
    fetch('/api/save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        alertSuccess('Berhasil!', 'Data berhasil disimpan');
    })
    .catch(error => {
        Swal.close();
        alertError('Error!', 'Gagal menyimpan data');
    });
}
```

### 4. Auto Logout Integration
```javascript
// Di auto-logout.js
function showWarning() {
    showAutoLogoutWarning(120).then((result) => {
        if (result.isConfirmed) {
            // User klik "Tetap Login"
            resetTimer();
        } else if (result.isDismissed) {
            // Timer habis, logout
            performLogout();
        }
    });
}
```

## 🎨 Customization

### Custom Options
```javascript
alertSuccess('Berhasil!', 'Data tersimpan', {
    timer: 5000,              // Auto close setelah 5 detik
    confirmText: 'Oke',       // Text tombol
    showConfirmButton: true,  // Tampilkan tombol
    timerProgressBar: true,   // Progress bar
});
```

### Custom Styling
```javascript
Swal.fire({
    title: 'Custom Alert',
    text: 'Dengan styling custom',
    icon: 'success',
    confirmButtonColor: '#10b981',
    background: '#f3f4f6',
    customClass: {
        popup: 'rounded-2xl',
        title: 'text-3xl font-bold',
    }
});
```

## 📱 Responsive

SweetAlert2 sudah responsive by default:
- Desktop: Modal di tengah
- Mobile: Full width dengan padding
- Toast: Menyesuaikan ukuran layar

## 🎭 Animations

Menggunakan Animate.css untuk animasi:
- fadeInDown: Muncul dari atas
- fadeOutUp: Hilang ke atas
- shakeX: Goyang (untuk error/warning)

## 🔊 Sound (Optional)

Bisa ditambahkan sound notification:
```javascript
function playSound(type) {
    const audio = new Audio(`/sounds/${type}.mp3`);
    audio.play();
}

alertSuccess('Berhasil!', 'Data tersimpan');
playSound('success');
```

## 📝 Best Practices

1. **Success**: Gunakan untuk aksi berhasil (save, update, delete)
2. **Error**: Gunakan untuk error/gagal
3. **Warning**: Gunakan untuk peringatan sebelum aksi penting
4. **Info**: Gunakan untuk informasi umum
5. **Toast**: Gunakan untuk notifikasi ringan yang tidak mengganggu
6. **Confirm**: Gunakan sebelum aksi yang tidak bisa di-undo
7. **Loading**: Gunakan saat proses yang memakan waktu

## 🚀 Keuntungan SweetAlert2

✅ Tampilan lebih cantik dan profesional
✅ Animasi smooth
✅ Responsive untuk mobile
✅ Customizable
✅ Support keyboard navigation
✅ Accessible (ARIA labels)
✅ Tidak perlu jQuery
✅ Ukuran kecil (~40KB gzipped)
✅ Support TypeScript
✅ Active maintenance

## 📚 Dokumentasi Lengkap

https://sweetalert2.github.io/

## ✅ Status

- ✅ SweetAlert2 installed
- ✅ Wrapper functions created
- ✅ Livewire integration
- ✅ Toast notifications
- ✅ Logout confirmation
- ✅ Auto-logout warning
- ✅ Delete confirmation
- ✅ Loading states
- ✅ Ready to use!

Sekarang semua alert di aplikasi akan menggunakan SweetAlert2 yang lebih cantik dan profesional!
