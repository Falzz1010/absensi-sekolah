# ✅ Testing Checklist - Real-Time Features

## Pre-Testing Setup

### 1. Environment Check
- [ ] `.env` file configured dengan Reverb settings
- [ ] Database migrated (`php artisan migrate`)
- [ ] Seeder run (`php artisan db:seed`)
- [ ] Assets built (`npm run build` atau `npm run dev`)

### 2. Services Running
- [ ] Reverb Server: `php artisan reverb:start` (port 8080)
- [ ] Queue Worker: `php artisan queue:work`
- [ ] Laravel Server: `php artisan serve` (port 8000)
- [ ] Vite Dev: `npm run dev` (port 5173) - optional untuk dev

### 3. Browser Setup
- [ ] Clear browser cache
- [ ] Open Developer Tools (F12)
- [ ] Check Console for errors
- [ ] Check Network tab for WebSocket connection

---

## 🧪 Test Cases

### Test 1: Reverb Server Connection

**Steps:**
1. Start Reverb server: `php artisan reverb:start`
2. Open browser to `http://localhost:8080`

**Expected:**
- [ ] Server responds with "OK" or status page
- [ ] No errors in terminal

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 2: WebSocket Connection

**Steps:**
1. Login to admin panel: `http://localhost:8000/admin`
2. Open browser console (F12)
3. Look for Echo connection logs

**Expected:**
- [ ] Console shows: "Echo connected" or similar
- [ ] Network tab shows WebSocket connection (ws://localhost:8080)
- [ ] Connection status: 101 Switching Protocols

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 3: Dashboard Auto-Refresh

**Steps:**
1. Open dashboard
2. Note current statistics
3. Wait 30 seconds
4. Check if stats updated

**Expected:**
- [ ] Stats widget refreshes after 30s
- [ ] No page reload
- [ ] Console shows polling activity

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 4: Table Auto-Refresh

**Steps:**
1. Open Absensi table
2. Open new tab, create new absensi
3. Return to first tab
4. Wait 30 seconds

**Expected:**
- [ ] Table refreshes automatically
- [ ] New absensi appears without manual refresh
- [ ] Pagination maintained

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 5: Multi-Tab Sync

**Steps:**
1. Open dashboard in 2 browser tabs
2. In tab 1: create new absensi
3. In tab 2: observe changes

**Expected:**
- [ ] Tab 2 updates within 30-60 seconds
- [ ] Both tabs show same data
- [ ] No conflicts or errors

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 6: QR Code Scan API

**Steps:**
1. Get a valid QR code from database
2. Use Postman/cURL to scan:
```bash
curl -X POST http://localhost:8000/api/qr-scan \
  -H "Content-Type: application/json" \
  -d '{"code":"YOUR_QR_CODE"}'
```

**Expected:**
- [ ] API returns success response
- [ ] Absensi created in database
- [ ] Response includes murid data

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 7: Real-Time QR Scan Notification

**Steps:**
1. Open dashboard in browser
2. Scan QR code via API (Test 6)
3. Observe dashboard

**Expected:**
- [ ] Notification appears instantly
- [ ] Notification shows: "QR Code Scanned - [Murid Name] - Hadir"
- [ ] Dashboard stats update
- [ ] Console shows event received

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 8: Absensi Created Event

**Steps:**
1. Open dashboard
2. Create absensi via Filament form
3. Check console logs

**Expected:**
- [ ] Console shows: "Absensi created: ..."
- [ ] Event data includes murid_id, status, kelas
- [ ] Dashboard refreshes automatically

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 9: Absensi Updated Event

**Steps:**
1. Open Absensi table
2. Edit existing absensi
3. Check console logs

**Expected:**
- [ ] Console shows: "Absensi updated: ..."
- [ ] Event data includes updated fields
- [ ] Table refreshes automatically

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 10: SPA Mode Navigation

**Steps:**
1. Navigate between pages (Dashboard → Absensi → Murid)
2. Observe page transitions

**Expected:**
- [ ] No full page reload
- [ ] Smooth transitions
- [ ] URL updates correctly
- [ ] Back button works

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 11: Database Notifications

**Steps:**
1. Create notification (if implemented)
2. Check notification bell icon
3. Wait 30 seconds

**Expected:**
- [ ] Notification appears
- [ ] Badge count updates
- [ ] Polling works (30s interval)

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 12: Widget Polling Intervals

**Steps:**
1. Open dashboard
2. Monitor console for polling logs
3. Verify intervals

**Expected:**
- [ ] StatsOverview: 30s
- [ ] AbsensiChart: 60s
- [ ] RekapMingguan: 120s
- [ ] RekapBulanan: 120s

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 13: Queue Worker

**Steps:**
1. Stop queue worker
2. Scan QR code via API
3. Check if event broadcasts

**Expected:**
- [ ] Without queue: events don't broadcast
- [ ] With queue: events broadcast successfully
- [ ] Queue worker processes jobs

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 14: Error Handling

**Steps:**
1. Stop Reverb server
2. Try to use application
3. Restart Reverb

**Expected:**
- [ ] App still works (graceful degradation)
- [ ] Console shows connection error
- [ ] Reconnects automatically when Reverb restarts

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### Test 15: Performance

**Steps:**
1. Open dashboard
2. Monitor network tab
3. Check resource usage

**Expected:**
- [ ] WebSocket connection stable
- [ ] Low bandwidth usage
- [ ] No memory leaks
- [ ] Smooth performance

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

## 🐛 Common Issues & Solutions

### Issue: Reverb tidak connect
**Solution:**
```bash
php artisan config:clear
php artisan reverb:start
```

### Issue: Events tidak trigger
**Solution:**
```bash
# Check queue worker
php artisan queue:work

# Check broadcasting config
php artisan config:clear
```

### Issue: Console errors
**Solution:**
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan cache:clear
php artisan view:clear
```

### Issue: Port already in use
**Solution:**
```bash
# Windows
netstat -ano | findstr :8080
taskkill /PID <PID> /F

# Or use different port
php artisan reverb:start --port=8081
```

---

## 📊 Test Results Summary

| Test | Status | Notes |
|------|--------|-------|
| 1. Reverb Connection | ⬜ | |
| 2. WebSocket Connection | ⬜ | |
| 3. Dashboard Auto-Refresh | ⬜ | |
| 4. Table Auto-Refresh | ⬜ | |
| 5. Multi-Tab Sync | ⬜ | |
| 6. QR Scan API | ⬜ | |
| 7. QR Scan Notification | ⬜ | |
| 8. Absensi Created Event | ⬜ | |
| 9. Absensi Updated Event | ⬜ | |
| 10. SPA Navigation | ⬜ | |
| 11. Database Notifications | ⬜ | |
| 12. Widget Polling | ⬜ | |
| 13. Queue Worker | ⬜ | |
| 14. Error Handling | ⬜ | |
| 15. Performance | ⬜ | |

**Legend:**
- ⬜ Not Tested
- ✅ Passed
- ❌ Failed
- ⚠️ Partial

---

## 📝 Testing Notes

**Date:** _____________  
**Tester:** _____________  
**Environment:** Development / Staging / Production  
**Browser:** Chrome / Firefox / Safari / Edge  
**OS:** Windows / macOS / Linux  

**Overall Status:** ⬜ All Passed | ⬜ Some Failed | ⬜ Not Complete

**Additional Notes:**
```
[Write any additional observations or issues here]
```

---

## ✅ Sign-Off

**Tested By:** _____________  
**Date:** _____________  
**Signature:** _____________  

**Approved By:** _____________  
**Date:** _____________  
**Signature:** _____________  

---

## 🚀 Production Readiness Checklist

Before deploying to production:

- [ ] All tests passed
- [ ] Performance acceptable
- [ ] Error handling tested
- [ ] Security reviewed
- [ ] Documentation complete
- [ ] Backup strategy in place
- [ ] Monitoring configured
- [ ] SSL/TLS configured for WebSocket
- [ ] Environment variables secured
- [ ] Rate limiting configured
- [ ] Load testing completed
- [ ] Rollback plan prepared

**Production Ready:** ⬜ Yes | ⬜ No | ⬜ With Conditions

**Conditions/Notes:**
```
[List any conditions or notes for production deployment]
```
