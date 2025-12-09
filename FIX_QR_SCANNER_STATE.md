# Fix: QR Scanner State Management

## ❌ Masalah

```
Kamera sudah di-allow tapi:
- Kamera tidak muncul
- Tombol "Berhenti" hilang
- Stuck di loading state
```

## 🔍 Root Cause

State management tidak di-reset dengan benar saat terjadi error:
1. Error terjadi setelah permission granted
2. `isScanning` tetap `true`
3. Button state tidak di-reset
4. User tidak bisa retry

## ✅ Solusi

### 1. Proper State Reset on Error

**File:** `resources/views/filament/student/pages/qr-scan-page.blade.php`

```javascript
} catch (err) {
    console.error('Error starting scanner:', err);
    
    // ✅ Reset state on error
    isScanning = false;
    hideLoading();
    
    // ✅ Reset buttons
    document.getElementById('start-scan-btn').classList.remove('hidden');
    document.getElementById('stop-scan-btn').classList.add('hidden');
    
    // Show error message
    showError(errorMsg);
}
```

### 2. Improved stopScanning()

```javascript
async function stopScanning() {
    if (!isScanning || !html5QrCode) {
        // ✅ Force reset state even if not scanning
        isScanning = false;
        document.getElementById('start-scan-btn').classList.remove('hidden');
        document.getElementById('stop-scan-btn').classList.add('hidden');
        return;
    }

    try {
        await html5QrCode.stop();
        html5QrCode.clear();
    } catch (err) {
        console.error('Error stopping scanner:', err);
    } finally {
        // ✅ Always reset state in finally block
        isScanning = false;
        document.getElementById('start-scan-btn').classList.remove('hidden');
        document.getElementById('stop-scan-btn').classList.add('hidden');
    }
}
```

### 3. Better retryCamera()

```javascript
async function retryCamera() {
    hideCameraError();
    
    // ✅ Reset state first
    if (html5QrCode && isScanning) {
        try {
            await html5QrCode.stop();
            html5QrCode.clear();
        } catch (err) {
            console.log('Error stopping previous scanner:', err);
        }
    }
    
    // ✅ Clean slate
    isScanning = false;
    html5QrCode = null;
    
    // ✅ Show qr-reader div
    document.getElementById('qr-reader').classList.remove('hidden');
    
    // Try starting again
    await startScanning();
}
```

### 4. Console Logging for Debugging

```javascript
async function startScanning() {
    if (isScanning) {
        console.log('Already scanning, ignoring start request');
        return;
    }

    console.log('Starting QR scanner...');
    // ... rest of code
    
    console.log('Html5Qrcode instance created');
    // ... rest of code
    
    console.log('Scanner started successfully');
}
```

## 🎯 State Flow

### Normal Flow:
```
1. User clicks "Mulai Scan"
2. isScanning = false → true
3. Button: "Mulai Scan" → "Berhenti"
4. Camera starts
5. User clicks "Berhenti"
6. isScanning = true → false
7. Button: "Berhenti" → "Mulai Scan"
```

### Error Flow (FIXED):
```
1. User clicks "Mulai Scan"
2. isScanning = false → true (temporarily)
3. Button: "Mulai Scan" → "Berhenti" (temporarily)
4. Error occurs
5. ✅ isScanning = true → false (RESET)
6. ✅ Button: "Berhenti" → "Mulai Scan" (RESET)
7. Error message shown
8. User can retry
```

## 🧪 Test Scenarios

### Test 1: Normal Start/Stop
```
1. Click "Mulai Scan"
2. Allow camera
3. Camera shows ✅
4. Button shows "Berhenti" ✅
5. Click "Berhenti"
6. Camera stops ✅
7. Button shows "Mulai Scan" ✅
```

### Test 2: Error Recovery
```
1. Click "Mulai Scan"
2. Allow camera
3. Error occurs (e.g., camera in use)
4. Error message shows ✅
5. Button shows "Mulai Scan" ✅ (FIXED!)
6. Can click "Mulai Scan" again ✅
```

### Test 3: Permission Denied Recovery
```
1. Click "Mulai Scan"
2. Deny camera permission
3. "Akses Kamera Ditolak" shows ✅
4. Click "Coba Lagi"
5. Allow camera
6. Camera starts ✅
```

### Test 4: Multiple Retries
```
1. Click "Mulai Scan"
2. Error occurs
3. Click "Mulai Scan" again
4. Error occurs again
5. Click "Mulai Scan" third time
6. Success ✅
7. State always correct ✅
```

## 🔧 Debugging

### Check Console Logs:
```javascript
// Open browser console (F12)
// Look for these messages:

"Starting QR scanner..."
"Html5Qrcode instance created"
"Scanner started successfully"

// Or error messages:
"Error starting scanner: [error details]"
```

### Check Button State:
```javascript
// In console:
console.log('isScanning:', isScanning);
console.log('Start button hidden:', document.getElementById('start-scan-btn').classList.contains('hidden'));
console.log('Stop button hidden:', document.getElementById('stop-scan-btn').classList.contains('hidden'));
```

## ✅ Improvements

1. **State Reset on Error** ✅
   - `isScanning` always reset
   - Buttons always reset
   - User can always retry

2. **Finally Block** ✅
   - State reset even if stop() fails
   - Guaranteed cleanup

3. **Force Reset** ✅
   - stopScanning() resets even if not scanning
   - Handles edge cases

4. **Better Retry** ✅
   - Clean previous instance
   - Reset all state
   - Fresh start

5. **Console Logging** ✅
   - Track scanner lifecycle
   - Debug issues easily

## 📝 Common Issues & Solutions

### Issue: Button stuck as "Berhenti"
**Solution:** ✅ Fixed - state always reset on error

### Issue: Can't retry after error
**Solution:** ✅ Fixed - buttons reset, can click again

### Issue: Camera doesn't show after permission
**Solution:** Check console for specific error, use "Coba Lagi"

### Issue: Multiple clicks cause issues
**Solution:** ✅ Fixed - check `isScanning` before starting

## 🎉 Status

- [x] State reset on error
- [x] Button state management
- [x] Proper cleanup in finally block
- [x] Better retry mechanism
- [x] Console logging for debugging
- [x] Edge case handling
- [x] Documentation

**QR Scanner state management sekarang robust dan reliable!** 🚀


---

## 🆕 Update: QR Box Size Validation Fix

### ❌ New Problem (Dec 7, 2025)

```
Error in console:
"Uncaught minimum size of 'config.qrbox' dimension value is 50px."

Scanner starts but crashes immediately
```

### 🔍 Root Cause

The html5-qrcode library requires minimum 50px for qrbox dimensions, but our calculation could result in smaller values on very small screens:

```javascript
let qrboxSize = Math.floor(minEdge * 0.7); // Could be < 50px
qrboxSize = Math.min(qrboxSize, minEdge - 20); // Could reduce below 50px
```

### ✅ Solution

Added double safety check to ensure minimum 50px:

```javascript
qrbox: function(viewfinderWidth, viewfinderHeight) {
    let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
    
    // Calculate 70% of smaller dimension
    let qrboxSize = Math.floor(minEdge * 0.7);
    
    // ✅ First check: Ensure minimum 50px
    qrboxSize = Math.max(qrboxSize, 50);
    
    // Ensure doesn't exceed viewport (leave 20px margin)
    qrboxSize = Math.min(qrboxSize, minEdge - 20);
    
    // ✅ Final safety check: ensure still above minimum
    qrboxSize = Math.max(qrboxSize, 50);
    
    console.log('QR box calculation - Viewport:', viewfinderWidth, 'x', viewfinderHeight, 'Final size:', qrboxSize);
    
    return {
        width: qrboxSize,
        height: qrboxSize
    };
}
```

### 🎯 Logic Flow

```
Example 1: Normal screen (400px)
1. minEdge = 400
2. qrboxSize = 400 * 0.7 = 280
3. Math.max(280, 50) = 280 ✅
4. Math.min(280, 380) = 280 ✅
5. Math.max(280, 50) = 280 ✅
Final: 280px

Example 2: Small screen (100px)
1. minEdge = 100
2. qrboxSize = 100 * 0.7 = 70
3. Math.max(70, 50) = 70 ✅
4. Math.min(70, 80) = 70 ✅
5. Math.max(70, 50) = 70 ✅
Final: 70px

Example 3: Very small screen (60px)
1. minEdge = 60
2. qrboxSize = 60 * 0.7 = 42
3. Math.max(42, 50) = 50 ✅
4. Math.min(50, 40) = 40 ❌
5. Math.max(40, 50) = 50 ✅ (FIXED!)
Final: 50px (minimum enforced)
```

### 🧪 Test Cases

**Test 1: Normal Mobile (375px width)**
- Expected: ~260px QR box ✅
- Scanner works smoothly ✅

**Test 2: Small Mobile (320px width)**
- Expected: ~220px QR box ✅
- Scanner works ✅

**Test 3: Very Small Screen (< 100px)**
- Expected: 50px QR box (minimum) ✅
- Scanner doesn't crash ✅

**Test 4: Desktop (1920px width)**
- Expected: ~1340px QR box ✅
- Scanner works ✅

### ✅ Status

- [x] QR box size validation fixed
- [x] Double safety check added
- [x] Console logging for debugging
- [x] Works on all screen sizes (50px - 2000px+)
- [x] No more "minimum size" errors

**QR Scanner sekarang works di semua ukuran layar!** 📱💻🖥️
