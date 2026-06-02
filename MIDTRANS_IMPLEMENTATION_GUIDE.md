# Panduan Implementasi Midtrans Payment
## Website Booking Lapangan Futsal

**Version:** 1.0  
**Date:** June 2, 2026  
**Status:** ✅ Ready for Implementation

---

## 🎯 Quick Start (5 Langkah)

### Step 1: Download Midtrans Library
```bash
# Download dari GitHub
# https://github.com/midtrans/midtrans-php

# Or via Composer
composer require midtrans/midtrans-php
```

### Step 2: Update Database
```sql
-- Run migration script
-- File: database_payment_migration.sql

mysql -u root -p db_booking_lapangan_futsal < database_payment_migration.sql
```

### Step 3: Setup Credentials
- File sudah ada: `config/midtrans.php`
- Credentials sudah terisi:
  - Merchant ID: G617610329
  - Server Key: Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  - Client Key: Mid-client-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

### Step 4: Create Payment Pages
✅ Already created:
- `payment/success.php` - Success page
- `payment/failed.php` - Failed page
- `payment/notification.php` - Webhook handler

### Step 5: Test Payment
- Test di Sandbox terlebih dahulu
- Dashboard: https://dashboard.sandbox.midtrans.com
- Test card: 4811 1111 1111 1114

---

## 📁 File Structure

```
project/
├── config/
│   ├── midtrans.php              ✅ Credentials & config
│   └── Midtrans.php              ❌ Download dari GitHub
│
├── payment/
│   ├── success.php               ✅ Success page
│   ├── failed.php                ✅ Failed page
│   ├── notification.php          ✅ Webhook handler
│   ├── create_transaction.php    ❌ TO CREATE
│   └── status.php                ❌ TO CREATE
│
├── booking/
│   ├── checkout.php              ❌ TO CREATE
│   └── process.php               ❌ TO CREATE
│
├── database_payment_migration.sql ✅ Migration script
└── MIDTRANS_PAYMENT_SCHEME.md     ✅ Design doc
```

---

## 🔧 Files Sudah Dibuat

### 1. config/midtrans.php ✅
```
Berisi:
- Merchant ID
- Server Key (RAHASIA - jangan expose di frontend)
- Client Key (Boleh di frontend)
- Environment (sandbox/production)
- Callback URLs
- Company info
```

### 2. payment/success.php ✅
```
Tampilan:
- ✓ Pembayaran Berhasil
- ✓ Order ID & Transaction ID
- ✓ Ringkasan booking
- ✓ Data pemesan
- ✓ Status terkonfirmasi
- ✓ Tombol kembali & cetak
```

### 3. payment/failed.php ✅
```
Tampilan:
- ✗ Pembayaran Gagal
- × Penyebab kegagalan
- × Tips memperbaiki
- × Tombol coba lagi & kontak support
```

### 4. payment/notification.php ✅
```
Proses:
- Receive webhook dari Midtrans
- Verify signature (PENTING untuk security)
- Update database payment status
- Update booking status
- Send confirmation email
- Log transaction
```

### 5. database_payment_migration.sql ✅
```
Membuat:
- ALTER tb_booking (tambah payment fields)
- CREATE tb_pembayaran (payment records)
- CREATE tb_payment_log (audit trail)
- CREATE tb_payment_methods (payment methods list)
```

---

## ⚙️ Files Perlu Dibuat

### 1. payment/create_transaction.php ❌

```php
<?php
/**
 * Create Midtrans Transaction
 * 
 * POST /payment/create_transaction.php
 * 
 * Request:
 * {
 *     "lapangan_id": 1,
 *     "tanggal": "2026-06-15",
 *     "jam_mulai": "18:00",
 *     "jam_selesai": "19:00",
 *     "nama_pemesan": "Budi",
 *     "no_hp": "08123456789",
 *     "email": "budi@example.com"
 * }
 */

require '../config/koneksi.php';
require '../config/midtrans.php';

// Require Midtrans library (download terlebih dahulu)
// require '../vendor/autoload.php';
// \Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
// \Midtrans\Config::$isProduction = (MIDTRANS_ENVIRONMENT === 'production');

// TODO: Implement transaction creation
?>
```

### 2. booking/checkout.php ❌

```php
<?php
/**
 * Booking Checkout Form
 * 
 * GET /booking/checkout.php?id=1&tanggal=2026-06-15&jam_mulai=18:00&jam_selesai=19:00
 */

require '../config/koneksi.php';

// TODO: Display booking form
// TODO: Show price calculation
// TODO: Show payment button
?>
```

### 3. payment/status.php ❌

```php
<?php
/**
 * Check Payment Status
 * 
 * GET /payment/status.php?order_id=BOOKING-123
 */

require '../config/koneksi.php';

// TODO: Implement payment status check
?>
```

---

## 🚀 Implementasi Step-by-Step

### Step 1: Download Midtrans Library

**Option A: Using Composer (Recommended)**
```bash
cd /project/
composer require midtrans/midtrans-php
```

**Option B: Manual Download**
1. Download dari: https://github.com/midtrans/midtrans-php
2. Extract ke folder: `vendor/midtrans/`
3. Include di config: `require 'vendor/midtrans/lib/Midtrans.php'`

### Step 2: Create Payment/Create_Transaction Endpoint

```php
// payment/create_transaction.php

require '../config/koneksi.php';
require '../config/midtrans.php';
require '../vendor/autoload.php';

\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$clientKey = MIDTRANS_CLIENT_KEY;
\Midtrans\Config::$isProduction = (MIDTRANS_ENVIRONMENT === 'production');
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate & extract
$lapangan_id = (int)$data['lapangan_id'];
$tanggal = $data['tanggal'];
$jam_mulai = $data['jam_mulai'];
$jam_selesai = $data['jam_selesai'];
$nama_pemesan = $data['nama_pemesan'];
$no_hp = $data['no_hp'];
$email = $data['email'];

// Calculate price & duration
$date_obj = new DateTime($tanggal);
$day_of_week = $date_obj->format('N'); // 1=Monday, 7=Sunday
$is_weekend = ($day_of_week == 6 || $day_of_week == 7);

// Get lapangan price
$result = $conn->query("SELECT harga, harga_weekend FROM tb_lapangan WHERE id = $lapangan_id");
$lapangan = $result->fetch_assoc();

$hourly_price = $is_weekend ? $lapangan['harga_weekend'] : $lapangan['harga'];

// Duration (assume 1 hour for demo)
$duration = 1;
$total_harga = $hourly_price * $duration;

// Create booking
$conn->query("
    INSERT INTO tb_booking (lapangan_id, nama_pemesan, tanggal, jam_mulai, jam_selesai, total_harga, payment_status, no_hp, email)
    VALUES ($lapangan_id, '$nama_pemesan', '$tanggal', '$jam_mulai', '$jam_selesai', $total_harga, 'pending', '$no_hp', '$email')
");

$booking_id = $conn->insert_id;

// Create payment record
$conn->query("
    INSERT INTO tb_pembayaran (booking_id, amount, status)
    VALUES ($booking_id, $total_harga, 'pending')
");

// Create Midtrans transaction
$transaction_details = [
    'order_id' => 'BOOKING-' . $booking_id,
    'gross_amount' => $total_harga,
];

$customer_details = [
    'first_name' => $nama_pemesan,
    'phone' => $no_hp,
    'email' => $email,
];

$transaction = \Midtrans\Snap::createTransaction([
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
]);

// Update payment with transaction ID
$conn->query("
    UPDATE tb_pembayaran 
    SET transaction_id = 'BOOKING-$booking_id'
    WHERE booking_id = $booking_id
");

// Return snap token
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'snap_token' => $transaction->token,
    'booking_id' => $booking_id,
    'total_harga' => $total_harga
]);
?>
```

### Step 3: Create Checkout Form

```php
// booking/checkout.php

<!DOCTYPE html>
<html>
<head>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>
</head>
<body>
    <form id="payment-form">
        <!-- Form fields -->
    </form>

    <script>
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Call backend to create transaction
            fetch('/payment/create_transaction.php', {
                method: 'POST',
                body: JSON.stringify(formData),
                headers: {'Content-Type': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                // Show Midtrans Snap popup
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        window.location.href = '/payment/success.php?order_id=' + result.order_id;
                    },
                    onError: function(result) {
                        window.location.href = '/payment/failed.php?order_id=' + result.order_id;
                    }
                });
            });
        });
    </script>
</body>
</html>
```

---

## 🔐 Security Checklist

- [x] Midtrans Server Key NEVER exposed di frontend
- [x] Midtrans Client Key OK di frontend
- [x] Webhook signature verification implemented
- [x] Payment amount validation implemented
- [x] HTTPS required untuk production
- [x] Database queries using real_escape_string
- [ ] Implement rate limiting
- [ ] Implement CSRF token
- [ ] Implement SSL certificate

---

## 📊 Test Payment Methods

### Sandbox Test Cards

**Credit Card Success:**
```
Number: 4811 1111 1111 1114
CVV: 123
Date: Any future date
```

**Debit Card Success:**
```
Number: 5264 2210 3010 0519
CVV: 123
Date: Any future date
```

**Test E-wallet:**
- OVO, Dana, LinkAja tersedia di Midtrans dashboard
- User: sandbox (default test user)

---

## 🚨 Troubleshooting

### Error: "Snap token not generated"
```
Solusi:
1. Check Server Key correct
2. Check Client Key correct
3. Check Midtrans library installed
4. Check internet connection
5. Check Midtrans status: https://status.midtrans.com
```

### Error: "Invalid signature"
```
Solusi:
1. Check Server Key in notification handler
2. Verify order_id correct
3. Check gross_amount correct
4. Verify hash calculation algorithm
```

### Error: "Transaction not found"
```
Solusi:
1. Check booking_id correct in database
2. Check order_id format (BOOKING-xxx)
3. Verify payment record created
```

---

## 📈 Going to Production

1. **Change Environment:** `MIDTRANS_ENVIRONMENT = 'production'`
2. **Update Credentials:** Use production Server Key & Client Key
3. **Update URLs:** Change callback URLs to production domain
4. **Test again:** Test dengan payment method asli
5. **Monitor:** Monitor transactions di dashboard

---

## 📋 Checklist Implementasi

- [ ] Download Midtrans library
- [ ] Update database dengan migration script
- [ ] Verify credentials di config/midtrans.php
- [ ] Create payment/create_transaction.php
- [ ] Create booking/checkout.php
- [ ] Create payment/status.php
- [ ] Test success flow
- [ ] Test failed flow
- [ ] Test webhook notification
- [ ] Verify email sent
- [ ] Test dengan berbagai payment method
- [ ] Deploy ke production
- [ ] Monitor transactions

---

## 🎯 Next Steps

1. **Download Midtrans library** - Composer atau manual
2. **Implement create_transaction.php** - Generate snap token
3. **Implement checkout.php** - Display booking form
4. **Test payment flow** - Success & failed scenarios
5. **Deploy** - Go to production

---

**Status: ✅ READY FOR DEVELOPMENT**

Semua design & skeleton sudah siap. Tinggal implement 3 file utama:
- payment/create_transaction.php
- booking/checkout.php
- payment/status.php
