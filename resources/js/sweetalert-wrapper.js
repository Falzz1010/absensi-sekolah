/**
 * SweetAlert2 Wrapper - Alert system yang cantik dan profesional
 * Terintegrasi dengan Filament dan Livewire
 */

import Swal from 'sweetalert2';

// Custom theme untuk aplikasi
const customTheme = {
    popup: 'rounded-2xl shadow-2xl',
    title: 'text-2xl font-bold text-gray-800',
    htmlContainer: 'text-gray-600',
    confirmButton: 'px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200',
    cancelButton: 'px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200',
};

// Success Alert
window.alertSuccess = function(title, message, options = {}) {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonText: options.confirmText || 'OK',
        confirmButtonColor: '#10b981',
        timer: options.timer || 3000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        customClass: customTheme,
        ...options
    });
};

// Error Alert
window.alertError = function(title, message, options = {}) {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonText: options.confirmText || 'OK',
        confirmButtonColor: '#ef4444',
        showClass: {
            popup: 'animate__animated animate__shakeX animate__faster'
        },
        customClass: customTheme,
        ...options
    });
};

// Warning Alert
window.alertWarning = function(title, message, options = {}) {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonText: options.confirmText || 'OK',
        confirmButtonColor: '#f59e0b',
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        customClass: customTheme,
        ...options
    });
};

// Info Alert
window.alertInfo = function(title, message, options = {}) {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonText: options.confirmText || 'OK',
        confirmButtonColor: '#3b82f6',
        timer: options.timer || 3000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        customClass: customTheme,
        ...options
    });
};

// Confirm Dialog
window.alertConfirm = function(title, message, options = {}) {
    return Swal.fire({
        icon: 'question',
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonText: options.confirmText || 'Ya',
        cancelButtonText: options.cancelText || 'Batal',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        customClass: customTheme,
        ...options
    });
};

// Delete Confirm
window.alertDelete = function(title = 'Hapus Data?', message = 'Data yang dihapus tidak dapat dikembalikan!', options = {}) {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonText: options.confirmText || 'Ya, Hapus!',
        cancelButtonText: options.cancelText || 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        showClass: {
            popup: 'animate__animated animate__shakeX animate__faster'
        },
        customClass: customTheme,
        ...options
    });
};

// Loading Alert
window.alertLoading = function(title = 'Memproses...', message = 'Mohon tunggu sebentar') {
    return Swal.fire({
        title: title,
        text: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
        customClass: customTheme,
    });
};

// Toast Notification (pojok kanan atas)
window.toast = function(type, message, options = {}) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: options.timer || 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
        customClass: {
            popup: 'rounded-xl shadow-2xl',
        }
    });

    const icons = {
        success: 'success',
        error: 'error',
        warning: 'warning',
        info: 'info'
    };

    return Toast.fire({
        icon: icons[type] || 'info',
        title: message,
        ...options
    });
};

// Shorthand toast methods
window.toastSuccess = (message, options) => toast('success', message, options);
window.toastError = (message, options) => toast('error', message, options);
window.toastWarning = (message, options) => toast('warning', message, options);
window.toastInfo = (message, options) => toast('info', message, options);

// Logout Confirmation
window.confirmLogout = function() {
    return Swal.fire({
        icon: 'question',
        title: 'Logout?',
        text: 'Apakah Anda yakin ingin keluar?',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        customClass: customTheme,
    });
};

// Auto Logout Warning
window.showAutoLogoutWarning = function(secondsLeft) {
    let timerInterval;
    return Swal.fire({
        icon: 'warning',
        title: 'Sesi Akan Berakhir',
        html: `Anda akan logout otomatis dalam <strong>${secondsLeft}</strong> detik.<br>Klik di mana saja untuk tetap login.`,
        timer: secondsLeft * 1000,
        timerProgressBar: true,
        showConfirmButton: true,
        confirmButtonText: 'Tetap Login',
        confirmButtonColor: '#10b981',
        allowOutsideClick: false,
        didOpen: () => {
            const content = Swal.getHtmlContainer();
            const b = content.querySelector('strong');
            timerInterval = setInterval(() => {
                const timeLeft = Math.ceil(Swal.getTimerLeft() / 1000);
                b.textContent = timeLeft;
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        },
        customClass: customTheme,
    });
};

// Session Expired
window.showSessionExpired = function() {
    return Swal.fire({
        icon: 'error',
        title: 'Sesi Berakhir',
        text: 'Anda telah logout otomatis karena tidak aktif.',
        confirmButtonText: 'Login Kembali',
        confirmButtonColor: '#3b82f6',
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: customTheme,
    }).then(() => {
        window.location.href = '/admin/login?timeout=1';
    });
};

// Listen for Livewire events
document.addEventListener('DOMContentLoaded', function() {
    if (window.Livewire) {
        // Success event
        Livewire.on('alert-success', (data) => {
            alertSuccess(data.title || 'Berhasil!', data.message || '');
        });

        // Error event
        Livewire.on('alert-error', (data) => {
            alertError(data.title || 'Error!', data.message || '');
        });

        // Warning event
        Livewire.on('alert-warning', (data) => {
            alertWarning(data.title || 'Peringatan!', data.message || '');
        });

        // Info event
        Livewire.on('alert-info', (data) => {
            alertInfo(data.title || 'Informasi', data.message || '');
        });

        // Toast event
        Livewire.on('toast', (data) => {
            toast(data.type || 'info', data.message || '');
        });
    }
});

// Intercept Filament notifications and show as toast
if (window.Filament) {
    const originalNotify = window.Filament.notify || function() {};
    window.Filament.notify = function(type, message) {
        toast(type, message);
        return originalNotify.apply(this, arguments);
    };
}

console.log('✅ SweetAlert2 initialized');

export default Swal;
