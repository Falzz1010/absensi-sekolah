// Real-time notifications untuk Absensi
if (window.Echo) {
    // Listen untuk QR Code scan events
    window.Echo.channel('absensi')
        .listen('QrCodeScanned', (e) => {
            // Show notification
            if (window.Filament) {
                window.Filament.notifications.send({
                    title: 'QR Code Scanned',
                    body: `${e.murid_name} - ${e.status} (${e.kelas})`,
                    icon: 'success',
                    duration: 5000,
                });
            }
        })
        .listen('AbsensiCreated', (e) => {
            console.log('Absensi created:', e);
            // Refresh widgets if on dashboard
            if (window.Livewire) {
                window.Livewire.dispatch('$refresh');
            }
        })
        .listen('AbsensiUpdated', (e) => {
            console.log('Absensi updated:', e);
            // Refresh widgets if on dashboard
            if (window.Livewire) {
                window.Livewire.dispatch('$refresh');
            }
        });
}
