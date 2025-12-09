<x-filament-panels::page>
    <div class="space-y-4 sm:space-y-6">
        {{-- Scanner Container --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <div class="text-center mb-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">
                    Scan QR Code untuk Check-in
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-2">
                    Arahkan kamera ke QR code untuk melakukan absensi
                </p>
            </div>

            {{-- Camera View --}}
            <div class="relative min-h-[250px] sm:min-h-[300px]">
                {{-- QR Reader Container - Always visible when scanning --}}
                <div id="qr-reader" class="w-full max-w-md mx-auto rounded-lg overflow-hidden bg-black" style="min-height: 250px;">
                    <p class="text-white text-center py-8">Kamera akan muncul di sini...</p>
                </div>
                
                {{-- Loading State - Overlays on top --}}
                <div id="loading-state" class="hidden absolute inset-0 bg-white dark:bg-gray-800 flex items-center justify-center">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Memuat kamera...</p>
                    </div>
                </div>

                {{-- Camera Permission Error - Overlays on top --}}
                <div id="camera-error" class="hidden absolute inset-0 bg-white dark:bg-gray-800 flex items-center justify-center">
                    <div class="text-center text-red-600 dark:text-red-400">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">Akses Kamera Ditolak</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Mohon izinkan akses kamera untuk menggunakan fitur scan QR code
                        </p>
                        <button onclick="retryCamera()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>

            {{-- Control Buttons --}}
            <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <button id="start-scan-btn" onclick="startScanning()" 
                    class="w-full sm:w-auto min-h-[44px] px-6 py-3 sm:py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 active:bg-primary-800 transition-colors duration-200 text-sm sm:text-base font-medium shadow-sm">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        Mulai Scan
                    </span>
                </button>
                <button id="stop-scan-btn" onclick="stopScanning()" 
                    class="w-full sm:w-auto min-h-[44px] px-6 py-3 sm:py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 active:bg-red-800 transition-colors duration-200 text-sm sm:text-base font-medium shadow-sm"
                    style="display: none;">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                        </svg>
                        Berhenti
                    </span>
                </button>
            </div>

            {{-- Alternative: Upload QR Image --}}
            <div class="mt-4 text-center">
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-2">
                    Atau upload gambar QR code
                </p>
                <input type="file" id="qr-file-input" accept="image/*" class="hidden" onchange="handleFileUpload(event)">
                <button onclick="document.getElementById('qr-file-input').click()" class="text-sm text-primary-600 hover:text-primary-700 underline">
                    Pilih File Gambar
                </button>
            </div>
        </div>

        {{-- Success Message --}}
        @if($scanResult)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 sm:p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-green-800 dark:text-green-200">Absensi Berhasil!</h3>
                    <p class="mt-1 text-xs sm:text-sm text-green-700 dark:text-green-300 break-words">{{ $scanResult }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Error Message --}}
        @if($errorMessage)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 sm:p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-red-800 dark:text-red-200">Scan Gagal</h3>
                    <p class="mt-1 text-xs sm:text-sm text-red-700 dark:text-red-300 break-words">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Result Display (Dynamic) --}}
        <div id="scan-result" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 sm:p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-green-800 dark:text-green-200">Absensi Berhasil!</h3>
                    <p id="scan-result-message" class="mt-1 text-xs sm:text-sm text-green-700 dark:text-green-300 break-words"></p>
                </div>
            </div>
        </div>

        {{-- Error Display (Dynamic) --}}
        <div id="scan-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 sm:p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-red-800 dark:text-red-200">Scan Gagal</h3>
                    <p id="scan-error-message" class="mt-1 text-xs sm:text-sm text-red-700 dark:text-red-300 break-words"></p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;
        let isScanning = false;
        let isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // Check if browser supports camera
        function isCameraSupported() {
            return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
        }

        function showLoading() {
            document.getElementById('loading-state').classList.remove('hidden');
            document.getElementById('camera-error').classList.add('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-state').classList.add('hidden');
        }

        function showCameraError() {
            document.getElementById('camera-error').classList.remove('hidden');
            document.getElementById('loading-state').classList.add('hidden');
        }

        function hideCameraError() {
            document.getElementById('camera-error').classList.add('hidden');
        }

        function showResult(message) {
            const resultDiv = document.getElementById('scan-result');
            const messageEl = document.getElementById('scan-result-message');
            messageEl.textContent = message;
            resultDiv.classList.remove('hidden');
            
            // Hide after 5 seconds
            setTimeout(() => {
                resultDiv.classList.add('hidden');
            }, 5000);
        }

        function showError(message) {
            const errorDiv = document.getElementById('scan-error');
            const messageEl = document.getElementById('scan-error-message');
            messageEl.textContent = message;
            errorDiv.classList.remove('hidden');
            
            // Hide after 5 seconds
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        async function startScanning() {
            if (isScanning) {
                console.log('Already scanning, ignoring start request');
                return;
            }

            console.log('Starting QR scanner...');

            // Check camera support first
            if (!isCameraSupported()) {
                showError('Browser Anda tidak mendukung akses kamera. Gunakan browser modern seperti Chrome atau Safari.');
                return;
            }

            showLoading();
            hideCameraError();
            document.getElementById('scan-result').classList.add('hidden');
            document.getElementById('scan-error').classList.add('hidden');

            try {
                html5QrCode = new Html5Qrcode("qr-reader");
                console.log('Html5Qrcode instance created');
                
                // Mobile-optimized configuration (minimal constraints)
                const config = {
                    fps: isMobile ? 5 : 10,
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        let qrboxSize = Math.floor(minEdge * 0.7);
                        qrboxSize = Math.max(qrboxSize, 50);
                        qrboxSize = Math.min(qrboxSize, minEdge - 20);
                        qrboxSize = Math.max(qrboxSize, 50);
                        
                        console.log('QR box size:', qrboxSize);
                        return { width: qrboxSize, height: qrboxSize };
                    }
                    // Removed aspectRatio - can cause OverconstrainedError
                };

                // Try multiple camera configurations with proper error handling
                let cameraStarted = false;
                let lastError = null;
                
                // Try 1: Back camera (environment) - preferred for QR scanning
                try {
                    await html5QrCode.start(
                        { facingMode: "environment" },
                        config,
                        onScanSuccess,
                        onScanError
                    );
                    cameraStarted = true;
                    console.log('✓ Camera started: back camera (environment)');
                } catch (err) {
                    console.log('✗ Back camera failed:', err.name);
                    lastError = err;
                }
                
                // Try 2: Front camera (user) - fallback
                if (!cameraStarted) {
                    try {
                        await html5QrCode.start(
                            { facingMode: "user" },
                            config,
                            onScanSuccess,
                            onScanError
                        );
                        cameraStarted = true;
                        console.log('✓ Camera started: front camera (user)');
                    } catch (err) {
                        console.log('✗ Front camera failed:', err.name);
                        lastError = err;
                    }
                }
                
                // Try 3: Any camera without constraints - last resort
                if (!cameraStarted) {
                    try {
                        await html5QrCode.start(
                            { facingMode: { ideal: "environment" } },
                            { fps: config.fps, qrbox: config.qrbox },
                            onScanSuccess,
                            onScanError
                        );
                        cameraStarted = true;
                        console.log('✓ Camera started: any available camera');
                    } catch (err) {
                        console.log('✗ All camera attempts failed');
                        throw lastError || err;
                    }
                }
                
                // Check if camera started successfully
                if (!cameraStarted) {
                    throw lastError || new Error('Failed to start camera');
                }

                // Camera started successfully - update UI
                isScanning = true;
                hideLoading();
                
                // Toggle buttons with debug logging
                const startBtn = document.getElementById('start-scan-btn');
                const stopBtn = document.getElementById('stop-scan-btn');
                
                console.log('Button elements:', { startBtn, stopBtn });
                
                if (startBtn) {
                    startBtn.style.cssText = 'display: none !important;';
                    console.log('Start button hidden');
                } else {
                    console.error('Start button not found!');
                }
                
                if (stopBtn) {
                    stopBtn.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
                    console.log('Stop button shown');
                    console.log('Stop button computed style:', window.getComputedStyle(stopBtn).display);
                } else {
                    console.error('Stop button not found!');
                }
                
                console.log('✅ Scanner started successfully - UI updated');
                
                // Debug: Check if video element exists
                setTimeout(() => {
                    const video = document.querySelector('#qr-reader video');
                    const canvas = document.querySelector('#qr-reader canvas');
                    console.log('Video element:', video);
                    console.log('Canvas element:', canvas);
                    if (video) {
                        console.log('Video dimensions:', video.videoWidth, 'x', video.videoHeight);
                        console.log('Video playing:', !video.paused);
                    }
                }, 1000);
            } catch (err) {
                console.error('Error starting scanner:', err);
                
                // Reset state on error
                isScanning = false;
                hideLoading();
                
                // Reset buttons
                document.getElementById('start-scan-btn').style.display = 'block';
                document.getElementById('stop-scan-btn').style.display = 'none';
                
                // Provide specific error messages
                let errorMsg = 'Gagal mengakses kamera';
                
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    showCameraError();
                    return;
                } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                    errorMsg = 'Kamera tidak ditemukan pada perangkat Anda';
                } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                    errorMsg = 'Kamera sedang digunakan oleh aplikasi lain';
                } else if (err.message) {
                    errorMsg = 'Gagal mengakses kamera: ' + err.message;
                } else if (typeof err === 'string') {
                    errorMsg = 'Gagal mengakses kamera: ' + err;
                }
                
                showError(errorMsg);
            }
        }

        async function stopScanning() {
            if (!isScanning || !html5QrCode) {
                // Force reset state even if not scanning
                isScanning = false;
                document.getElementById('start-scan-btn').style.display = 'block';
                document.getElementById('stop-scan-btn').style.display = 'none';
                return;
            }

            try {
                await html5QrCode.stop();
                html5QrCode.clear();
            } catch (err) {
                console.error('Error stopping scanner:', err);
            } finally {
                // Always reset state
                isScanning = false;
                document.getElementById('start-scan-btn').style.display = 'block';
                document.getElementById('stop-scan-btn').style.display = 'none';
            }
        }

        async function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning temporarily to prevent multiple scans
            if (html5QrCode && isScanning) {
                await stopScanning();
            }

            // Process the QR code
            await processQrCode(decodedText);
        }

        function onScanError(errorMessage) {
            // Ignore scan errors (they happen frequently during scanning)
            // console.log('Scan error:', errorMessage);
        }

        async function retryCamera() {
            hideCameraError();
            
            // Reset state first
            if (html5QrCode && isScanning) {
                try {
                    await html5QrCode.stop();
                    html5QrCode.clear();
                } catch (err) {
                    console.log('Error stopping previous scanner:', err);
                }
            }
            
            isScanning = false;
            html5QrCode = null;
            
            // Try starting again
            await startScanning();
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (html5QrCode && isScanning) {
                html5QrCode.stop();
            }
        });

        // Handle page visibility changes (mobile optimization)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && isScanning) {
                // Pause scanning when page is hidden to save battery
                stopScanning();
            }
        });

        // Handle file upload for QR code
        async function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            try {
                // Initialize scanner if not already done
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qr-reader");
                }

                showLoading();
                document.getElementById('scan-result').classList.add('hidden');
                document.getElementById('scan-error').classList.add('hidden');

                // Scan the uploaded file
                const result = await html5QrCode.scanFile(file, true);
                
                hideLoading();
                
                // Process the result
                await processQrCode(result);
                
            } catch (err) {
                hideLoading();
                console.error('Error scanning file:', err);
                showError('Gagal membaca QR code dari gambar. Pastikan gambar berisi QR code yang valid.');
            }
        }

        // Process QR code result (shared by camera and file upload)
        async function processQrCode(decodedText) {
            try {
                const response = await fetch('/api/qr-scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        code: decodedText
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showResult(data.message + ' - ' + data.data.murid + ' (' + data.data.kelas + ')');
                    // Show success alert
                    if (typeof alertSuccess === 'function') {
                        alertSuccess('Absensi Berhasil!', data.message + ' - ' + data.data.murid + ' (' + data.data.kelas + ')');
                    }
                } else {
                    showError(data.message || 'Terjadi kesalahan saat memproses QR code');
                    // Show error alert
                    if (typeof alertError === 'function') {
                        alertError('Scan Gagal', data.message || 'Terjadi kesalahan saat memproses QR code');
                    }
                }
            } catch (error) {
                console.error('Error processing scan:', error);
                showError('Koneksi gagal, silakan coba lagi');
                // Show error alert
                if (typeof alertError === 'function') {
                    alertError('Koneksi Gagal', 'Tidak dapat terhubung ke server. Silakan coba lagi.');
                }
            }
        }

        // Check camera support on page load
        window.addEventListener('DOMContentLoaded', () => {
            if (!isCameraSupported()) {
                const startBtn = document.getElementById('start-scan-btn');
                if (startBtn) {
                    startBtn.disabled = true;
                    startBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    startBtn.textContent = 'Kamera Tidak Didukung';
                }
                
                // Show message to use file upload instead
                showError('Kamera tidak tersedia. Silakan gunakan opsi upload gambar QR code di bawah.');
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
