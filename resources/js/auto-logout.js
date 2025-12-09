/**
 * Auto Logout - Deteksi inaktivitas user dan logout otomatis
 * Timeout: 30 menit (1800000 ms)
 */

(function() {
    'use strict';

    // Konfigurasi
    const TIMEOUT_DURATION = 30 * 60 * 1000; // 30 menit dalam milliseconds
    const WARNING_DURATION = 2 * 60 * 1000; // Peringatan 2 menit sebelum logout
    const CHECK_INTERVAL = 1000; // Check setiap 1 detik

    let lastActivityTime = Date.now();
    let warningShown = false;
    let logoutTimer = null;
    let warningTimer = null;

    // Event yang menandakan user aktif
    const activityEvents = [
        'mousedown',
        'mousemove',
        'keypress',
        'scroll',
        'touchstart',
        'click'
    ];

    // Reset timer saat ada aktivitas
    function resetTimer() {
        lastActivityTime = Date.now();
        warningShown = false;
        
        // Hapus warning jika ada
        const warningEl = document.getElementById('auto-logout-warning');
        if (warningEl) {
            warningEl.remove();
        }

        // Clear existing timers
        if (logoutTimer) clearTimeout(logoutTimer);
        if (warningTimer) clearTimeout(warningTimer);

        // Set warning timer (2 menit sebelum logout)
        warningTimer = setTimeout(showWarning, TIMEOUT_DURATION - WARNING_DURATION);

        // Set logout timer
        logoutTimer = setTimeout(performLogout, TIMEOUT_DURATION);
    }

    // Tampilkan peringatan
    function showWarning() {
        if (warningShown) return;
        warningShown = true;

        const warningDiv = document.createElement('div');
        warningDiv.id = 'auto-logout-warning';
        warningDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            z-index: 99999;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
            font-family: 'Inter', sans-serif;
        `;

        warningDiv.innerHTML = `
            <div style="display: flex; align-items: start; gap: 12px;">
                <svg style="width: 24px; height: 24px; flex-shrink: 0; margin-top: 2px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div style="flex: 1;">
                    <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">
                        Peringatan Sesi
                    </div>
                    <div style="font-size: 14px; opacity: 0.95; line-height: 1.5;">
                        Sesi Anda akan berakhir dalam <strong id="countdown">2:00</strong> karena tidak aktif.
                        <br>Klik di mana saja untuk tetap login.
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: white;
                    width: 28px;
                    height: 28px;
                    border-radius: 6px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                    transition: background 0.2s;
                " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;

        // Add animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(warningDiv);

        // Countdown timer
        let secondsLeft = 120; // 2 menit
        const countdownEl = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            secondsLeft--;
            const minutes = Math.floor(secondsLeft / 60);
            const seconds = secondsLeft % 60;
            if (countdownEl) {
                countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
            
            if (secondsLeft <= 0 || !warningShown) {
                clearInterval(countdownInterval);
            }
        }, 1000);
    }

    // Perform logout
    function performLogout() {
        // Tampilkan loading overlay
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999999;
            font-family: 'Inter', sans-serif;
        `;
        overlay.innerHTML = `
            <div style="
                background: white;
                padding: 40px;
                border-radius: 16px;
                text-align: center;
                max-width: 400px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            ">
                <svg style="width: 64px; height: 64px; color: #f59e0b; margin: 0 auto 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 12px;">
                    Sesi Berakhir
                </h2>
                <p style="font-size: 16px; color: #6b7280; margin-bottom: 24px;">
                    Anda telah logout otomatis karena tidak aktif selama 30 menit.
                </p>
                <div style="
                    display: inline-block;
                    padding: 12px 24px;
                    background: #f3f4f6;
                    border-radius: 8px;
                    color: #6b7280;
                    font-size: 14px;
                ">
                    Mengalihkan ke halaman login...
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        // Logout via form submission
        setTimeout(() => {
            // Try to find and submit logout form
            const logoutForm = document.querySelector('form[action*="logout"]');
            if (logoutForm) {
                logoutForm.submit();
            } else {
                // Fallback: redirect to login
                window.location.href = '/admin/login?timeout=1';
            }
        }, 2000);
    }

    // Initialize
    function init() {
        // Set initial timer
        resetTimer();

        // Add event listeners untuk semua aktivitas
        activityEvents.forEach(event => {
            document.addEventListener(event, resetTimer, true);
        });

        console.log('✅ Auto-logout initialized (30 minutes timeout)');
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
