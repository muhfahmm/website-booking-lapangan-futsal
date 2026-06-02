# Skema Pembayaran dengan Midtrans
## Website Booking Lapangan Futsal

**Status:** 🔵 Design & Planning  
**Version:** 1.0  
**Date:** June 2, 2026

---

## 📋 Midtrans Credentials

**Merchant ID:** G617610329  
**Server Key:** Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  
**Client Key:** Mid-client-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  
**Environment:** Production/Sandbox (setup di dashboard)  
**Callback URL:** `/payment/success.php`

---

## 🏗️ Arsitektur Pembayaran

### 1. Flow Pembayaran Umum

```
User (Frontend)
    ↓
Pilih Lapangan + Tanggal + Jam
    ↓
Input Data Pemesan
    ↓
Lihat Total Harga (Rp X,XXX)
    ↓
Klik "Proses Pembayaran"
    ↓
Backend Create Transaction
    ↓
Midtrans Generate Token
    ↓
Snap Popup / Redirect
    ↓
User Pilih Metode Pembayaran
    ↓
Selesai Pembayaran (Success/Failed/Pending)
    ↓
Callback to Backend
    ↓
Update Booking Status
    ↓
Redirect to Success Page
```

### 2. Database Schema untuk Pembayaran

**New Table: `tb_pembayaran`**
```sql
CREATE TABLE tb_pembayaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    transaction_id VARCHAR(100) UNIQUE,
    amount INT NOT NULL,
    payment_method VARCHAR(50),
    status ENUM('pending', 'settlement', 'expire', 'cancel', 'deny') DEFAULT 'pending',
    midtrans_response JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES tb_booking(id)
);
```

**Update Table: `tb_booking`**
```sql
ALTER TABLE tb_booking ADD COLUMN (
    jam_mulai_display TIME,
    jam_selesai_display TIME,
    total_harga INT DEFAULT 0,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending'
);
```

### 3. Implementasi Files

```
config/
├── midtrans.php              ← Midtrans config & keys
├── Midtrans.php             ← Midtrans library/class

payment/
├── create_transaction.php     ← Create payment request
├── process.php               ← Handle Snap callback
├── success.php               ← Success page
├── failed.php                ← Failed page
├── notification.php          ← Webhook notification handler

booking/
├── checkout.php              ← Booking checkout form
├── process_booking.php       ← Process booking (create transaction)

admin/
├── payment_reports.php       ← Payment history & reports
```

---

## 🔄 Flow Teknologi Detail

### Step 1: User Membuat Booking Request

**File:** `booking/checkout.php`

```php
GET /booking/checkout.php?id=1&tanggal=2026-06-15&jam_mulai=18:00&jam_selesai=19:00

Data yang dihandle:
- lapangan_id
- tanggal booking
- jam mulai & selesai
- hitung total_harga (weekday/weekend auto-detect)
- hitung durasi jam
```

### Step 2: Frontend Display Booking Summary

```html
Form Checkout:
┌────────────────────────────────┐
│ RINGKASAN BOOKING              │
├────────────────────────────────┤
│ Lapangan: Lapangan A           │
│ Tanggal:  15 Juni 2026         │
│ Jam:      18:00 - 19:00 (1jam) │
│ Harga:    Rp 100,000           │
├────────────────────────────────┤
│ TOTAL:    Rp 100,000           │
├────────────────────────────────┤
│ Nama Pemesan: [Input]          │
│ No. HP: [Input]                │
│ Email: [Input]                 │
├────────────────────────────────┤
│ [Proses Pembayaran via Midtrans]
└────────────────────────────────┘
```

### Step 3: Backend Create Transaction

**File:** `payment/create_transaction.php`

```php
POST /payment/create_transaction.php

Request:
{
    "lapangan_id": 1,
    "tanggal": "2026-06-15",
    "jam_mulai": "18:00",
    "jam_selesai": "19:00",
    "nama_pemesan": "Budi Santoso",
    "no_hp": "08123456789",
    "email": "budi@example.com"
}

Process:
1. Validate input
2. Calculate total_harga
3. Create booking record (status: pending)
4. Create pembayaran record
5. Create Midtrans transaction
6. Return snapToken to frontend
```

### Step 4: Generate Midtrans Token

**Code Flow:**
```php
require_once 'config/Midtrans.php';

// Set Midtrans Config
\Midtrans\Config::$serverKey = 'Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
\Midtrans\Config::$clientKey = 'Mid-client-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
\Midtrans\Config::$isProduction = false; // true for production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Transaction Details
$transactionDetails = array(
    'order_id' => 'BOOKING-' . $booking_id,
    'gross_amount' => $total_harga,
);

// Customer Details
$customerDetails = array(
    'first_name' => $nama_pemesan,
    'phone' => $no_hp,
    'email' => $email,
);

// Create transaction
$transaction = \Midtrans\Snap::createTransaction([
    'transaction_details' => $transactionDetails,
    'customer_details' => $customerDetails,
]);

// Return snap token
$snapToken = $transaction->token;
```

### Step 5: Snap Payment Gateway Popup

**Frontend JavaScript:**
```javascript
// Snap popup on button click
document.getElementById('bayar-btn').addEventListener('click', function() {
    snap.pay(snapToken, {
        onSuccess: function(result) {
            // Payment success
            window.location.href = '/payment/success.php?order_id=' + orderId;
        },
        onPending: function(result) {
            // Payment pending
            alert('Pembayaran sedang diproses');
        },
        onError: function(result) {
            // Payment error
            alert('Pembayaran gagal');
        },
        onClose: function() {
            // User close popup
            alert('Anda menutup pembayaran');
        }
    });
});
```

### Step 6: Webhook Notification (dari Midtrans)

**File:** `payment/notification.php`

```php
POST /payment/notification.php

Midtrans akan POST notification ke URL ini:
{
    "transaction_time": "2026-06-15 18:30:00",
    "transaction_status": "settlement",
    "transaction_id": "xxxxx-xxxxx-xxxxx",
    "status_message": "midtrans payment received",
    "status_code": "200",
    "order_id": "BOOKING-123",
    "merchant_id": "G617610329",
    ...
}

Process:
1. Verify signature dari Midtrans
2. Update tb_pembayaran.status
3. Update tb_booking.payment_status
4. Send confirmation email
5. Log transaction
```

### Step 7: Redirect to Success/Failed Page

**Success Page:** `payment/success.php`
```html
┌──────────────────────────────────┐
│ ✓ PEMBAYARAN BERHASIL            │
├──────────────────────────────────┤
│ Transaction ID: xxxxx            │
│ Order ID: BOOKING-123            │
│ Tanggal: 15 Juni 2026            │
│ Total: Rp 100,000                │
│ Status: Terkonfirmasi ✓          │
├──────────────────────────────────┤
│ Email konfirmasi sudah dikirim    │
│ No. Referensi: BOOKING-123       │
├──────────────────────────────────┤
│ [Lihat Booking] [Kembali Home]   │
└──────────────────────────────────┘
```

---

## 💾 Database Integration

### 1. tb_booking (Update)

```sql
ALTER TABLE tb_booking ADD COLUMN (
    total_harga INT DEFAULT 0,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    notes TEXT
);
```

### 2. tb_pembayaran (New)

```sql
CREATE TABLE tb_pembayaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL UNIQUE,
    transaction_id VARCHAR(100) UNIQUE,
    amount INT NOT NULL,
    payment_method VARCHAR(50),
    status ENUM('pending', 'settlement', 'expire', 'cancel', 'deny') DEFAULT 'pending',
    midtrans_response JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES tb_booking(id) ON DELETE CASCADE
);
```

---

## 🔐 Security & Verification

### 1. Signature Verification (Webhook)

```php
// Verify signature dari Midtrans
$orderId = $_POST['order_id'];
$statusCode = $_POST['status_code'];
$grossAmount = $_POST['gross_amount'];
$serverKey = 'Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

$signature = $_POST['signature_key'];

$input = $orderId . $statusCode . $grossAmount . $serverKey;
$hash = hash('sha512', $input);

if($signature === $hash) {
    // Signature valid, safe to process
} else {
    // Invalid signature, reject
}
```

### 2. Payment Amount Validation

```php
// Validate amount matches booking total
$booking_total = db_get_booking_amount($booking_id);
$payment_amount = $_POST['gross_amount'];

if($booking_total !== $payment_amount) {
    // Amount mismatch, reject payment
    log_error('Amount mismatch detected');
    die('Invalid payment amount');
}
```

### 3. HTTPS & SSL

- All payment pages MUST use HTTPS
- Midtrans recommends SSL 3.3 or higher
- Production: require_once '/payment/secure_header.php'

---

## 📊 Payment Methods Supported

Midtrans supports:
- ✓ Credit Card (Visa, MasterCard, JCB, Amex)
- ✓ Debit Card (via SNAP)
- ✓ E-wallet (OVO, Dana, LinkAja, GCash, Paymaya)
- ✓ Bank Transfer (Virtual Account)
- ✓ QRIS (BI QRIS)
- ✓ Buy Now Pay Later (Kredivo, Akulaku, dll)

---

## 🔔 Notification Flow

```
User berhasil bayar
    ↓
Midtrans process pembayaran
    ↓
Payment settlement (± 1 menit)
    ↓
Midtrans POST to webhook URL
    ↓
Backend verify signature
    ↓
Update database status
    ↓
Send email konfirmasi
    ↓
Update booking status to "paid"
    ↓
Admin notifikasi booking baru
```

---

## 📧 Email Notifications

### 1. Confirmation Email (ke customer)

```
Subject: Pembayaran Berhasil - Booking Lapangan #BOOKING-123

Hi Budi,

Terima kasih! Pembayaran Anda telah diterima.

Ringkasan Booking:
- Lapangan: Lapangan A - Indoor Premium
- Tanggal: 15 Juni 2026
- Jam: 18:00 - 19:00 (1 jam)
- Total: Rp 100,000
- Status: Terkonfirmasi ✓

No. Referensi: BOOKING-123
Transaction ID: xxxxx-xxxxx

Nomor lapangan akan dikirim 30 menit sebelum jam bermain.

Terima kasih,
FutsalBook Admin
```

### 2. Admin Notification

```
Subject: Booking Baru - BOOKING-123

Booking baru diterima:

Pemesan: Budi Santoso (08123456789)
Lapangan: Lapangan A
Tanggal: 15 Juni 2026, 18:00 - 19:00
Status Pembayaran: Sukses ✓
Total: Rp 100,000

Aksi:
[Lihat Detail] [Terima Booking] [Tolak]
```

---

## 🛣️ URL Endpoints

### Payment Endpoints
- `POST /payment/create_transaction.php` - Create payment
- `POST /payment/notification.php` - Webhook handler
- `GET /payment/success.php` - Success page
- `GET /payment/failed.php` - Failed page
- `GET /payment/status.php?order_id=xxx` - Check status

### User Endpoints
- `GET /booking/checkout.php?id=1` - Checkout page
- `GET /booking/confirm.php?order_id=xxx` - Booking confirmation

### Admin Endpoints
- `GET /admin/payment_reports.php` - Payment history
- `GET /admin/transaction_detail.php?id=xxx` - Transaction detail

---

## 🧪 Testing URLs

```
Development/Sandbox:
- Midtrans Dashboard: https://dashboard.sandbox.midtrans.com
- Test Card: 4811 1111 1111 1114
- CVV: 123, Any date

Production:
- Midtrans Dashboard: https://dashboard.midtrans.com
- Real payment methods
```

---

## 📈 Implementation Timeline

**Phase 1: Setup (Week 1)**
- Install Midtrans library
- Configure credentials
- Create payment tables

**Phase 2: Backend (Week 2)**
- Create transaction endpoint
- Implement webhook handler
- Add payment verification

**Phase 3: Frontend (Week 3)**
- Create checkout page
- Add Snap integration
- Success/failed pages

**Phase 4: Testing (Week 4)**
- Test all payment methods
- Verify webhook notifications
- Production deployment

---

## ⚠️ Important Notes

1. **Server Key NEVER expose di frontend** - Hanya di backend PHP
2. **Client Key** - Boleh di frontend (untuk Snap)
3. **Webhook signature verification** - WAJIB implement untuk security
4. **HTTPS required** - Mutlak untuk production
5. **Database backup** - Penting sebelum production
6. **Testing extensively** - Test di sandbox dulu sebelum production

---

**Status: ✅ DESIGN COMPLETE - Ready for Implementation**
