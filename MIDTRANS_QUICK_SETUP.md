# Midtrans Payment - Quick Setup Guide

**Time Required:** 15 minutes  
**Difficulty:** Easy  
**Prerequisites:** XAMPP running, MySQL database ready

---

## 🚀 3-Step Setup

### Step 1: Download Library (2 minutes)

Open Terminal/CMD in your project folder:

```bash
composer require midtrans/midtrans-php
```

If you don't have Composer, download it first: https://getcomposer.org

**Alternative (manual download):**
1. Download: https://github.com/midtrans/midtrans-php/archive/master.zip
2. Extract to: `/vendor/midtrans/`

### Step 2: Setup Database (3 minutes)

**Option A: MySQL Command Line**
```bash
mysql -u root -p db_booking_lapangan_futsal < database_payment_migration.sql
```

**Option B: phpMyAdmin**
1. Go to: http://localhost/phpmyadmin
2. Select `db_booking_lapangan_futsal` database
3. Click "SQL" tab
4. Copy-paste contents of `database_payment_migration.sql`
5. Click "Go"

**Verify tables created:**
```sql
SHOW TABLES LIKE 'tb_payment%';
SHOW COLUMNS FROM tb_booking WHERE Field IN ('total_harga', 'payment_status', 'no_hp', 'email');
```

### Step 3: Activate Midtrans Code (5 minutes)

Edit: `payment/create_transaction.php`

**Find this line (around line 160):**
```php
// Simulate Midtrans Snap token generation
// In production, uncomment below and use actual library

/*
```

**Change to:**
```php
// Now create Midtrans transaction
require_once '../vendor/autoload.php';

// Configure Midtrans
```

**Find the closing `*/` and remove it (around line 210)**

Save the file. Done!

---

## ✅ Verify Setup

### Test 1: Database Tables
```bash
mysql -u root -p db_booking_lapangan_futsal -e "SHOW TABLES;"
```

Should see:
- ✓ tb_pembayaran
- ✓ tb_payment_log
- ✓ tb_booking (with new columns)

### Test 2: PHP Can Find Midtrans
Create a test file `test_midtrans.php`:
```php
<?php
require 'vendor/autoload.php';
echo \Midtrans\Config::$version;
?>
```

Visit: http://localhost/project-client-php/website_booking_lapangan_futsal/test_midtrans.php

Should show version number (no errors).

Delete `test_midtrans.php` after testing.

### Test 3: Payment Form Works
1. Go to: http://localhost/project-client-php/website_booking_lapangan_futsal/
2. Select a lapangan
3. Click "Pesan Sekarang"
4. Fill in form
5. Click "Lanjut ke Pembayaran"
6. Should see Midtrans Snap popup

---

## 🧪 Test Payment

**Test Credentials:**
- Card: 4811 1111 1111 1114
- CVV: 123
- Date: 05/25 (or any future)
- Amount: Any

**Step-by-step:**
1. Click "Pesan Sekarang" on any lapangan
2. Fill form with your details
3. Click "Lanjut ke Pembayaran"
4. Midtrans popup opens
5. Enter test card number: 4811 1111 1111 1114
6. Enter CVV: 123
7. Enter date: 05/25
8. Click "Pay"
9. See success page!

---

## 📊 Verify Payment Recorded

After successful payment:

**Check Database:**
```sql
SELECT * FROM tb_pembayaran ORDER BY created_at DESC LIMIT 1;
SELECT * FROM tb_booking WHERE payment_status = 'paid' LIMIT 1;
```

Should see:
- ✓ Payment record with status 'settlement'
- ✓ Booking record with payment_status 'paid'

**Check Files:**
- ✓ Success page shown
- ✓ No errors in browser console
- ✓ No errors in server logs

---

## 🔧 Troubleshooting

### Problem: Composer not found
```
Solution:
1. Install Composer: https://getcomposer.org
2. Add to PATH environment variable
3. Or use: php composer.phar require midtrans/midtrans-php
```

### Problem: Database tables not created
```
Solution:
1. Check MySQL is running
2. Check database name is correct
3. Try importing again
4. Check for error messages
```

### Problem: Snap popup not showing
```
Solution:
1. Check browser console for errors
2. Check if Midtrans library is loaded
3. Verify create_transaction.php returns snap_token
4. Test with: 
   curl http://localhost/.../payment/create_transaction.php
```

### Problem: Payment not updating database
```
Solution:
1. Check webhook URL in Midtrans dashboard
2. Check notification.php is accessible
3. Check database connection
4. Monitor payment_log table
5. Check server error logs
```

---

## 📁 Expected File Structure After Setup

```
project-client-php/website_booking_lapangan_futsal/
├── vendor/
│   └── midtrans/
│       └── lib/
│           ├── Midtrans.php
│           └── ...
│
├── config/
│   ├── koneksi.php
│   └── midtrans.php
│
├── payment/
│   ├── create_transaction.php  (code uncommented)
│   ├── success.php
│   ├── failed.php
│   ├── notification.php
│   └── status.php
│
├── booking/
│   └── checkout.php
│
└── ...
```

---

## 🎯 What's Happening Behind The Scenes

### When user clicks "Lanjut ke Pembayaran":

1. ✓ checkout.php sends form data to create_transaction.php
2. ✓ create_transaction.php validates everything
3. ✓ Creates booking record in database
4. ✓ Calls Midtrans API to get snap_token
5. ✓ Snap.pay() shows payment popup
6. ✓ User enters payment info
7. ✓ Midtrans processes payment
8. ✓ Midtrans calls notification.php webhook
9. ✓ notification.php updates database
10. ✓ Page shows success!

---

## 💡 Tips

**Tip 1: Always Test in Sandbox First**
- Never use production credentials in development
- Use test cards provided by Midtrans

**Tip 2: Monitor Logs**
- Check `error_log()` entries
- Check Midtrans dashboard
- Watch database tables

**Tip 3: Keep Credentials Safe**
- Never commit config/midtrans.php to Git
- Use .gitignore to hide secrets
- In production, use environment variables

**Tip 4: Test All Payment Methods**
- Credit card
- E-wallet (OVO, Dana)
- Bank transfer
- QRIS

---

## 📞 Getting Help

If something doesn't work:

1. **Check Error Messages**
   - Browser console (F12)
   - Server error logs
   - Database error_log

2. **Verify Credentials**
   - Merchant ID: G617610329
   - Server Key: Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   - Client Key: Mid-client-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

3. **Check Documentation**
   - MIDTRANS_PAYMENT_SCHEME.md (design)
   - MIDTRANS_IMPLEMENTATION_GUIDE.md (guide)
   - MIDTRANS_IMPLEMENTATION_COMPLETE.md (full reference)

4. **Contact Midtrans Support**
   - https://midtrans.com/help
   - support@midtrans.com

---

## ⏱️ Timing

- Step 1 (Download): 2 min
- Step 2 (Database): 3 min
- Step 3 (Activate Code): 5 min
- Test Payment: 5 min
- **Total: 15 minutes**

---

## ✅ Success Criteria

After setup, you should:
- ✓ See Midtrans Snap popup when clicking "Bayar"
- ✓ Be able to test with test card
- ✓ See success page after payment
- ✓ See payment record in database
- ✓ See booking status updated to "paid"

---

**Good luck! You're all set! 🚀**

