# 📖 Contoh Penggunaan SweetAlert2

## 🎯 Contoh Praktis untuk Aplikasi Absensi

### 1. Logout dengan Konfirmasi
```html
<!-- Di navigation/user menu -->
<form id="logout-form" action="{{ route('filament.admin.auth.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<button onclick="confirmLogout().then((result) => {
    if (result.isConfirmed) {
        toastInfo('Logging out...');
        document.getElementById('logout-form').submit();
    }
})">
    <svg>...</svg>
    Logout
</button>
```

### 2. Hapus Data Absensi
```php
// Di AbsensiResource.php
Tables\Actions\DeleteAction::make()
    ->requiresConfirmation()
    ->action(function ($record) {
        $record->delete();
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data absensi berhasil dihapus'
        ]);
    })
```

### 3. Approve/Reject Pengajuan Izin
```php
// Di PengajuanIzinResource.php
Tables\Actions\Action::make('approve')
    ->label('Setujui')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->action(function ($record) {
        $record->update(['verification_status' => 'approved']);
        
        $this->dispatch('alert-success', [
            'title' => 'Disetujui!',
            'message' => 'Pengajuan izin telah disetujui'
        ]);
    })
    ->requiresConfirmation()
    ->modalHeading('Setujui Pengajuan?')
    ->modalDescription('Pengajuan izin akan disetujui dan siswa akan mendapat notifikasi.')
    ->modalSubmitActionLabel('Ya, Setujui');
```

### 4. Import Data dengan Loading
```javascript
// Di halaman import
function importData() {
    const fileInput = document.getElementById('file-input');
    const file = fileInput.files[0];
    
    if (!file) {
        alertWarning('Peringatan', 'Pilih file terlebih dahulu');
        return;
    }
    
    alertLoading('Mengimport...', 'Mohon tunggu, sedang memproses file');
    
    const formData = new FormData();
    formData.append('file', file);
    
    fetch('/admin/murids/import', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            alertSuccess('Berhasil!', `${data.count} data siswa berhasil diimport`);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            alertError('Gagal!', data.message);
        }
    })
    .catch(error => {
        Swal.close();
        alertError('Error!', 'Terjadi kesalahan saat import data');
    });
}
```

### 5. QR Code Scan Success
```javascript
// Di QrScanPage
function onScanSuccess(decodedText) {
    alertLoading('Memverifikasi...', 'Mohon tunggu');
    
    fetch('/api/qr-scan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ qr_code: decodedText })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            alertSuccess('Berhasil!', 'Absensi berhasil dicatat', {
                timer: 2000
            });
        } else {
            alertError('Gagal!', data.message);
        }
    });
}
```

### 6. Manual Check-in Confirmation
```php
// Di ManualAttendancePage.php
public function confirmAttendance()
{
    // ... logic ...
    
    $this->dispatch('alert-success', [
        'title' => 'Check-in Berhasil!',
        'message' => 'Absensi manual Anda telah tercatat pada ' . now()->format('H:i')
    ]);
}
```

### 7. Bulk Delete dengan Progress
```javascript
async function bulkDelete(ids) {
    const result = await alertDelete(
        'Hapus ' + ids.length + ' Data?',
        'Data yang dihapus tidak dapat dikembalikan'
    );
    
    if (!result.isConfirmed) return;
    
    alertLoading('Menghapus...', `Menghapus ${ids.length} data`);
    
    for (let i = 0; i < ids.length; i++) {
        await fetch(`/api/delete/${ids[i]}`, { method: 'DELETE' });
        
        // Update progress
        Swal.update({
            text: `Menghapus ${i + 1} dari ${ids.length} data`
        });
    }
    
    Swal.close();
    alertSuccess('Berhasil!', `${ids.length} data berhasil dihapus`);
    window.location.reload();
}
```

### 8. Form Validation Error
```javascript
function validateForm() {
    const nama = document.getElementById('nama').value;
    const email = document.getElementById('email').value;
    
    if (!nama) {
        alertWarning('Peringatan', 'Nama harus diisi');
        return false;
    }
    
    if (!email) {
        alertWarning('Peringatan', 'Email harus diisi');
        return false;
    }
    
    if (!email.includes('@')) {
        alertError('Error', 'Format email tidak valid');
        return false;
    }
    
    return true;
}
```

### 9. Session Timeout Warning
```javascript
// Di auto-logout.js
function showWarning() {
    showAutoLogoutWarning(120).then((result) => {
        if (result.isConfirmed) {
            // User klik "Tetap Login"
            resetTimer();
            toastSuccess('Sesi diperpanjang');
        } else if (result.isDismissed) {
            // Timer habis
            showSessionExpired();
        }
    });
}
```

### 10. Export Data dengan Progress
```javascript
async function exportData() {
    alertLoading('Menyiapkan Export...', 'Mohon tunggu');
    
    try {
        const response = await fetch('/admin/export/excel');
        const blob = await response.blob();
        
        Swal.close();
        
        // Download file
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'laporan-absensi.xlsx';
        a.click();
        
        toastSuccess('Export berhasil!');
    } catch (error) {
        Swal.close();
        alertError('Gagal!', 'Terjadi kesalahan saat export data');
    }
}
```

### 11. Pengumuman Baru
```php
// Di PengumumanResource CreatePage
protected function afterCreate(): void
{
    $this->dispatch('alert-success', [
        'title' => 'Pengumuman Dibuat!',
        'message' => 'Notifikasi telah dikirim ke ' . $this->getTargetCount() . ' siswa'
    ]);
}
```

### 12. Hari Libur Notification
```php
// Di HariLiburObserver
public function created(HariLibur $hariLibur): void
{
    // Send notifications...
    
    // Show toast to admin
    session()->flash('toast', [
        'type' => 'success',
        'message' => 'Hari libur berhasil dibuat dan notifikasi dikirim ke semua siswa'
    ]);
}
```

### 13. Network Error Handler
```javascript
window.addEventListener('online', () => {
    toastSuccess('Koneksi kembali normal');
});

window.addEventListener('offline', () => {
    toastError('Koneksi internet terputus');
});
```

### 14. Copy to Clipboard
```javascript
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        toastSuccess('Berhasil disalin!');
    }).catch(() => {
        toastError('Gagal menyalin');
    });
}
```

### 15. Auto-save Indicator
```javascript
let saveTimeout;
function autoSave() {
    clearTimeout(saveTimeout);
    
    saveTimeout = setTimeout(() => {
        toastInfo('Menyimpan draft...', { timer: 1000 });
        
        // Save logic
        fetch('/api/save-draft', { method: 'POST' })
            .then(() => {
                toastSuccess('Draft tersimpan', { timer: 1000 });
            });
    }, 2000);
}
```

## 🎨 Tips & Tricks

### 1. Chain Alerts
```javascript
alertSuccess('Step 1 Complete!', 'Lanjut ke step 2?')
    .then((result) => {
        if (result.isConfirmed) {
            return alertInfo('Step 2', 'Isi form berikut');
        }
    })
    .then(() => {
        alertSuccess('Selesai!', 'Semua step complete');
    });
```

### 2. Custom HTML Content
```javascript
Swal.fire({
    title: 'Detail Absensi',
    html: `
        <div class="text-left">
            <p><strong>Nama:</strong> ${nama}</p>
            <p><strong>Kelas:</strong> ${kelas}</p>
            <p><strong>Status:</strong> <span class="badge badge-success">Hadir</span></p>
            <p><strong>Waktu:</strong> ${waktu}</p>
        </div>
    `,
    confirmButtonText: 'Tutup'
});
```

### 3. Input Dialog
```javascript
Swal.fire({
    title: 'Alasan Penolakan',
    input: 'textarea',
    inputLabel: 'Masukkan alasan penolakan',
    inputPlaceholder: 'Tulis alasan di sini...',
    showCancelButton: true,
    confirmButtonText: 'Tolak',
    cancelButtonText: 'Batal'
}).then((result) => {
    if (result.isConfirmed) {
        // Reject with reason
        rejectPengajuan(result.value);
    }
});
```

Dengan SweetAlert2, semua alert di aplikasi jadi lebih cantik, profesional, dan user-friendly! 🎉
