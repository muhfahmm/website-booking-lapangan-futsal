# Integration Booking ke Payment Gateway
## Website Booking Lapangan Futsal

**Date:** June 2, 2026  
**Status:** ✅ COMPLETE - Button "Booking Sekarang" terhubung ke Payment Gateway  

---

## 📋 Perubahan yang Dilakukan

### 1. File index.php (Homepage - Daftar Lapangan)
**Lokasi:** `/index.php`

**Perubahan:**
- Button "Booking" pada lapangan card sebelumnya tidak memiliki function
- Sekarang button "Booking" memanggil function `openBookingModal(lapanganId, lapanganName)`
- Function ini membuka modal dialog untuk memilih tanggal dan jam
- User input divalidasi sebelum dikirim ke checkout

**Code yang ditambahkan:**
```javascript
// Modal booking form dengan input validation
function openBookingModal(lapanganId, lapanganName) {
    // Menampilkan modal dengan form
    // - Input tanggal (date picker)
    // - Input jam mulai (time picker)
    // - Input jam selesai (time picker)
}

function closeBookingModal() {
    // Menutup modal
}

function submitBooking(lapanganId) {
    // Validasi input
    // Redirect ke booking/checkout.php
}
```

**Flow:**
```
User click "Booking" button → openBookingModal() dipanggil 
→ Modal muncul dengan form input 
→ User isi tanggal dan jam 
→ Click "Lanjut ke Checkout" 
→ submitBooking() validasi input 
→ Redirect ke booking/checkout.php dengan parameter
```

---

### 2. File detail-lapangan.php (Halaman Detail)
**Lokasi:** `/detail-lapangan.php`

**Perubahan:**
- Button "Booking Sekarang" sebelumnya adalah link kosong (`href="#"`)
- Sekarang button memanggil function `openBookingForm()`
- Function ini mengambil tanggal yang sudah dipilih user di form
- Langsung redirect ke checkout dengan parameter

**Code yang ditambahkan:**
```javascript
// Function untuk membuka form booking
function openBookingForm() {
    const selectedDate = document.getElementById('selectedDate').value;
    
    if (!selectedDate) {
        alert('Silakan pilih tanggal terlebih dahulu');
        return;
    }

    // Redirect ke booking checkout dengan parameter
    window.location.href = `booking/checkout.php?lapangan_id=<?php echo $lapangan_id; ?>&tanggal=${selectedDate}&jam_mulai=09:00&jam_selesai=10:00`;
}
```

**Flow:**
```
User click "Booking Sekarang" button → openBookingForm() dipanggil 
→ Validasi tanggal sudah dipilih 
→ Redirect ke booking/checkout.php dengan:
   - lapangan_id (dari URL parameter)
   - tanggal (dari date picker input)
   - jam_mulai (default: 09:00, bisa diubah di checkout)
   - jam_selesai (default: 10:00, bisa diubah di checkout)
```

---

## 🔄 Complete Payment Flow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER SELECTS LAPANGAN ON HOMEPAGE (index.php)           │
├─────────────────────────────────────────────────────────────┤
│   - User sees lapangan cards                                │
│   - Click "Booking" button                                  │
│   - openBookingModal() shows modal form                      │
└──────────────────┬──────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. BOOKING MODAL FORM (Modal Dialog)                        │
├─────────────────────────────────────────────────────────────┤
│   - Input Tanggal (date picker)                             │
│   - Input Jam Mulai (time picker)                           │
│   - Input Jam Selesai (time picker)                         │
│   - Click "Lanjut ke Checkout"                              │
│   - submitBooking() validates & redirects                   │
└──────────────────┬──────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. CHECKOUT FORM (booking/checkout.php)                     │
├─────────────────────────────────────────────────────────────┤
│   ✓ Lapangan details fetched from DB                        │
│   ✓ Price calculated (weekday vs weekend)                   │
│   ✓ Booking summary displayed                               │
│   ✓ User fills: Nama, No HP, Email                          │
│   ✓ Click "Lanjut ke Pembayaran"                            │
│   ✓ POST to payment/create_transaction.php                  │
└──────────────────┬──────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. CREATE TRANSACTION (payment/create_transaction.php)      │
├─────────────────────────────────────────────────────────────┤
│   ✓ Validate input                                          │
│   ✓ Calculate final price                                   │
│   ✓ Check booking conflicts                                 │
│   ✓ Create booking record (pending)                         │
│   ✓ Create payment record                                   │
│   ✓ Call Midtrans API → get snap_token                      │
│   ✓ Return snap_token to frontend                           │
└──────────────────┬──────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. MIDTRANS SNAP POPUP (booking/checkout.php)               │
├─────────────────────────────────────────────────────────────┤
│   ✓ Show Snap popup (Midtrans payment gateway)              │
│   ✓ User select payment method                              │
│   ✓ User enters payment details                             │
│   ✓ Click "Pay"                                             │
└──────────────────┬──────────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. PAYMENT PROCESSING                                       │
├─────────────────────────────────────────────────────────────┤
│   Payment Success:                                          │
│   ✓ Redirect to payment/success.php                         │
│   ✓ Show confirmation with booking details                  │
│                                                             │
│   Payment Pending:                                          │
│   ✓ Redirect to payment/status.php                          │
│   ✓ Auto-refresh every 5 seconds                            │
│   ✓ Auto-redirect when status changes                       │
│                                                             │
│   Payment Failed:                                           │
│   ✓ Redirect to payment/failed.php                          │
│   ✓ Show error message & retry option                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 📱 User Experience

### Scenario 1: Booking from Homepage (index.php)
```
1. User sees lapangan card
2. Click "Booking" button
3. Beautiful modal dialog appears
   - Select tanggal
   - Select jam_mulai
   - Select jam_selesai
4. Click "Lanjut ke Checkout"
5. Redirected to checkout form
6. See price preview & lapangan details
7. Fill customer info
8. Click "Lanjut ke Pembayaran"
9. Midtrans Snap popup appears
10. Complete payment
11. See success page ✓
```

### Scenario 2: Booking from Detail Page (detail-lapangan.php)
```
1. User clicks on lapangan detail link
2. See detail page with images & facilities
3. Scroll to right side card
4. Select tanggal di date picker
5. Click "Booking Sekarang" button
6. Redirected directly to checkout (tanggal sudah terisi)
7. Can adjust jam_mulai & jam_selesai at checkout
8. Fill customer info
9. Click "Lanjut ke Pembayaran"
10. Midtrans Snap popup appears
11. Complete payment
12. See success page ✓
```

---

## 🔧 Technical Details

### Modal Form HTML Structure
```html
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full shadow-2xl">
        <!-- Header -->
        <!-- Form with inputs -->
        <!-- Buttons: Lanjut ke Checkout, Batal -->
    </div>
</div>
```

### Parameter Passed to Checkout
```
GET /booking/checkout.php?
    lapangan_id=1
    &tanggal=2026-06-15
    &jam_mulai=18:00
    &jam_selesai=19:00
```

### Form Validation
- ✅ Tanggal required
- ✅ Jam mulai required & valid format (HH:MM)
- ✅ Jam selesai required & valid format (HH:MM)
- ✅ Jam selesai > jam mulai (validasi di both frontend & backend)
- ✅ Tanggal tidak bisa masa lalu (HTML5 min attribute)

---

## 🎨 UI/UX Features

### Modal Dialog
- Responsive design (mobile-friendly)
- Clean, modern styling (Tailwind CSS)
- Easy to use date/time pickers (HTML5 inputs)
- Clear call-to-action buttons
- Close button (X icon)
- Overlay background for focus

### Input Validation
```javascript
// Frontend validation before submit
if (!tanggal || !jam_mulai || !jam_selesai) {
    alert('Silakan isi semua field');
    return;
}

if (jam_selesai <= jam_mulai) {
    alert('Jam selesai harus lebih besar dari jam mulai');
    return;
}
```

### Backend Validation (create_transaction.php)
- Midtrans library akan validate jumlah pembayaran
- Check booking conflict di database
- Check lapangan exist di database
- Validasi semua required fields

---

## 📊 Data Flow

```
Client (Frontend)
    ↓
index.php / detail-lapangan.php (Display)
    ↓
Modal Form / Detail Page Date Input
    ↓
booking/checkout.php (Collect Customer Info)
    ↓
payment/create_transaction.php (Backend API)
    ├─ Validate
    ├─ Create records
    └─ Call Midtrans
    ↓
Midtrans Snap Popup (Payment)
    ↓
payment/notification.php (Webhook)
    ├─ Verify signature
    └─ Update database
    ↓
payment/success.php or payment/failed.php (Result)
```

---

## ✅ Files Modified

| File | Changes | Status |
|------|---------|--------|
| index.php | Added openBookingModal() function | ✅ Complete |
| detail-lapangan.php | Changed button to openBookingForm() | ✅ Complete |
| booking/checkout.php | Already exists (no changes) | ✅ Ready |
| payment/create_transaction.php | Already exists (no changes) | ✅ Ready |
| payment/success.php | Already exists (no changes) | ✅ Ready |
| payment/failed.php | Already exists (no changes) | ✅ Ready |
| payment/notification.php | Already exists (no changes) | ✅ Ready |

---

## 🧪 Testing

### Test Scenario 1: Homepage Booking
```
1. Visit: http://localhost/.../index.php
2. Scroll to lapangan section
3. Click "Booking" button on any lapangan
4. Modal should appear
5. Select tanggal (today or future)
6. Select jam_mulai (e.g., 18:00)
7. Select jam_selesai (e.g., 19:00)
8. Click "Lanjut ke Checkout"
9. Should redirect to: booking/checkout.php?lapangan_id=X&tanggal=Y&jam_mulai=18:00&jam_selesai=19:00
10. See checkout form with prices
```

### Test Scenario 2: Detail Page Booking
```
1. Visit: http://localhost/.../detail-lapangan.php?id=1
2. Scroll to right side pricing card
3. Select tanggal di date picker
4. Click "Booking Sekarang" button
5. Should redirect to: booking/checkout.php?lapangan_id=1&tanggal=Y&jam_mulai=09:00&jam_selesai=10:00
6. See checkout form with preselected date
7. Can change jam_mulai & jam_selesai at checkout
```

### Test Scenario 3: Form Validation
```
1. Open modal on homepage
2. Try to submit without filling fields
3. Should show alert: "Silakan isi semua field"
4. Fill tanggal
5. Fill jam_mulai = 19:00
6. Fill jam_selesai = 18:00 (less than jam_mulai)
7. Try to submit
8. Should show alert: "Jam selesai harus lebih besar dari jam mulai"
```

---

## 📝 Summary

**What's Working:**
- ✅ Homepage booking button → Opens modal with form
- ✅ Detail page booking button → Redirects to checkout
- ✅ Modal form validation
- ✅ Parameter passing to checkout
- ✅ All checkout & payment flow ready
- ✅ Payment gateway integration complete

**User Journey:**
1. Select lapangan (homepage or detail)
2. Select date & time (via modal or detail page)
3. Fill customer info (checkout form)
4. Make payment (Midtrans Snap)
5. See confirmation (success page)

**Status:** ✅ COMPLETE - User can now click "Booking Sekarang" to enter payment gateway

---

Generated: June 2, 2026

