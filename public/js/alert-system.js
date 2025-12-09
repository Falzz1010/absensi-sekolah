/**
 * Alert System - Sistem notifikasi cantik untuk semua fitur
 * Mendukung: success, error, warning, info
 */

(function() {
    'use strict';

    // Container untuk alerts
    let alertContainer = null;

    // Initialize container
    function initContainer() {
        if (alertContainer) return;

        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 420px;
            pointer-events: none;
        `;
        document.body.appendChild(alertContainer);
    }

    // Alert configurations
    const alertConfig = {
        success: {
            gradient: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            icon: `<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>`,
            sound: true
        },
        error: {
            gradient: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
            icon: `<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>`,
            sound: true
        },
        warning: {
            gradient: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            icon: `<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>`,
            sound: true
        },
        info: {
            gradient: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
            icon: `<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>`,
            sound: false
        }
    };

    // Show alert
    window.showAlert = function(type, title, message, duration = 5000) {
        initContainer();

        const config = alertConfig[type] || alertConfig.info;
        const alertId = 'alert-' + Date.now();

        const alertDiv = document.createElement('div');
        alertDiv.id = alertId;
        alertDiv.style.cssText = `
            background: ${config.gradient};
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease-out;
            font-family: 'Inter', sans-serif;
            pointer-events: auto;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
        `;

        alertDiv.innerHTML = `
            <div style="display: flex; align-items: start; gap: 12px;">
                ${config.icon}
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; font-size: 15px; margin-bottom: 4px;">
                        ${title}
                    </div>
                    <div style="font-size: 14px; opacity: 0.95; line-height: 1.4; word-wrap: break-word;">
                        ${message}
                    </div>
                </div>
                <button onclick="event.stopPropagation(); this.parentElement.parentElement.remove()" style="
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

        // Hover effects
        alertDiv.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(-5px)';
        });
        alertDiv.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });

        // Click to dismiss
        alertDiv.addEventListener('click', function() {
            this.style.opacity = '0';
            this.style.transform = 'translateX(400px)';
            setTimeout(() => this.remove(), 300);
        });

        alertContainer.appendChild(alertDiv);

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => {
                if (document.getElementById(alertId)) {
                    alertDiv.style.opacity = '0';
                    alertDiv.style.transform = 'translateX(400px)';
                    setTimeout(() => alertDiv.remove(), 300);
                }
            }, duration);
        }

        // Play sound (optional)
        if (config.sound) {
            playNotificationSound(type);
        }

        return alertId;
    };

    // Shorthand methods
    window.alertSuccess = function(title, message, duration) {
        return showAlert('success', title, message, duration);
    };

    window.alertError = function(title, message, duration) {
        return showAlert('error', title, message, duration);
    };

    window.alertWarning = function(title, message, duration) {
        return showAlert('warning', title, message, duration);
    };

    window.alertInfo = function(title, message, duration) {
        return showAlert('info', title, message, duration);
    };

    // Play notification sound
    function playNotificationSound(type) {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            // Different frequencies for different types
            const frequencies = {
                success: [523.25, 659.25], // C5, E5
                error: [392.00, 329.63],    // G4, E4
                warning: [440.00, 493.88],  // A4, B4
                info: [523.25]              // C5
            };

            const freq = frequencies[type] || frequencies.info;
            
            oscillator.frequency.value = freq[0];
            gainNode.gain.value = 0.1;

            oscillator.start(audioContext.currentTime);
            
            if (freq[1]) {
                oscillator.frequency.setValueAtTime(freq[1], audioContext.currentTime + 0.1);
            }
            
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
            oscillator.stop(audioContext.currentTime + 0.2);
        } catch (e) {
            // Silently fail if audio not supported
        }
    }

    // Add animation styles
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

        @media (max-width: 640px) {
            #alert-container {
                left: 10px;
                right: 10px;
                max-width: none;
            }
        }
    `;
    document.head.appendChild(style);

    // Listen for Livewire events (Filament integration)
    document.addEventListener('DOMContentLoaded', function() {
        // Livewire v3
        if (window.Livewire) {
            Livewire.on('alert', (data) => {
                showAlert(data.type || 'info', data.title || 'Notifikasi', data.message || '', data.duration || 5000);
            });
        }
    });

    console.log('✅ Alert System initialized');
})();
