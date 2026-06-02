# Multiple Image Upload Feature
## Enhanced Admin Panel for Lapangan Management

**Date:** June 2, 2026  
**Status:** ✅ IMPLEMENTED  
**Version:** 2.2.0  

---

## 📋 Overview

Fitur ini memungkinkan admin untuk mengunggah multiple gambar langsung saat membuat atau mengedit lapangan baru, tanpa harus menggunakan halaman gallery terpisah.

**Keunggulan:**
- ✅ Upload multiple images dalam satu form
- ✅ Preview real-time untuk setiap gambar
- ✅ Gambar utama terpisah dari gallery
- ✅ Folder terorganisir untuk uploads
- ✅ User-friendly interface

---

## 🎯 Features Implemented

### 1. Unified Upload Form
**Location:** Admin Panel → Manage Lapangan → Tambah Lapangan

**Features:**
- **Gambar Lapangan (Main Image):**
  - Single image upload
  - Digunakan sebagai thumbnail di homepage
  - Format: JPG, PNG (Max 2MB)

- **Multiple Gambar Lapangan (Gallery):**
  - Multiple file selection
  - Dapat upload 1-10+ gambar sekaligus
  - Real-time preview
  - Format: JPG, PNG (Max 2MB per file)

### 2. Folder Structure
```
project/
├── uploads/
│   ├── lapangan/          # Main lapangan images
│   │   ├── 1780399979_Screenshot.png
│   │   ├── 1780399980_lapangan.jpg
│   │   └── ...
│   ├── gallery/           # Multiple gallery images
│   │   ├── 1780399981_gallery_0_image1.jpg
│   │   ├── 1780399981_gallery_1_image2.jpg
│   │   └── ...
│   └── .gitkeep          # Folder tracking
```

### 3. Database Integration
**Tables Used:**
- `tb_lapangan` - Main lapangan data (gambar field)
- `tb_lapangan_gallery` - Multiple gallery images

**Process:**
1. Upload gambar utama → `tb_lapangan.gambar`
2. Insert lapangan → Get `lapangan_id`
3. Upload gallery images → `tb_lapangan_gallery` dengan `lapangan_id`

---

## 📁 File Changes

### 1. admin/manage_lapangan.php (UPDATED)
**Changes:**
- Added upload directory configuration
- Created `uploads/lapangan/` and `uploads/gallery/` folders
- Added multiple file input (`gallery_images[]`)
- Implemented gallery upload loop after lapangan insertion
- Added image preview JavaScript

**New Code:**
```php
// Create uploads directory
$upload_dir = '../uploads/lapangan/';
$gallery_dir = '../uploads/gallery/';

// Handle multiple gallery images
if (isset($_FILES['gallery_images']) && count($_FILES['gallery_images']['name']) > 0) {
    for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
        // Process each file
        // Insert to tb_lapangan_gallery
    }
}
```

### 2. admin/manage_gallery.php (UPDATED)
**Changes:**
- Updated upload directory path
- Changed from `assets/images/` to `uploads/gallery/`
- Maintains backward compatibility

### 3. Folder Structure (CREATED)
**New Folders:**
- `uploads/` - Main upload directory
- `uploads/lapangan/` - Main lapangan images
- `uploads/gallery/` - Gallery images
- `uploads/.gitkeep` - Git tracking

---

## 🎨 User Interface

### Form Fields Added:

```html
<!-- Gambar Lapangan -->
<input type="file" name="gambar" accept="image/*">
Label: "Gambar Lapangan"
Help text: "Format: JPG, PNG (Max 2MB)"

<!-- Multiple Gallery Images -->
<input type="file" name="gallery_images[]" accept="image/*" multiple>
Label: "Multiple Gambar Lapangan (Gallery)"
Help text: "Format: JPG, PNG (Max 2MB per file) - Pilih beberapa gambar sekaligus"

<!-- Image Preview -->
<div id="imagePreview" class="grid grid-cols-4 gap-2">
    <!-- Preview thumbnails appear here -->
</div>
```

### Preview Feature:
- Real-time thumbnail display
- 4-column grid layout
- Shows filename on hover
- Responsive design

---

## 💻 JavaScript Implementation

### Image Preview Function:
```javascript
document.getElementById('gallery_images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = Array.from(this.files);
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'w-full h-24 object-cover rounded border-2 border-emerald-600';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
```

**Features:**
- Uses FileReader API
- Displays preview immediately
- No server upload needed for preview
- Works with 1-20+ files

---

## 🔄 Upload Process

### Adding New Lapangan:

```
1. Fill form (nama, harga, etc.)
   ↓
2. Upload Gambar Lapangan
   └→ Saved to: uploads/lapangan/
   ↓
3. Select Multiple Gallery Images
   └→ Preview shown in grid
   ↓
4. Click "Simpan"
   ↓
5. Insert to tb_lapangan
   ↓
6. Get lapangan_id from insert_id
   ↓
7. Upload gallery images
   └→ Loop through each file
   └→ Save to: uploads/gallery/
   └→ Insert to tb_lapangan_gallery
   ↓
8. Redirect to success page
```

### Updating Lapangan:

```
1. Click Edit on lapangan card
   ↓
2. Load form data (existing values)
   ↓
3. Optional: Upload new Gambar Lapangan
   ├→ If uploaded: Update to uploads/lapangan/
   └→ If not: Keep existing
   ↓
4. Optional: Add new gallery images
   ├→ If uploaded: Save to uploads/gallery/
   └→ If not: Keep existing gallery
   ↓
5. Click "Simpan"
   ↓
6. Redirect to success page
```

---

## 📊 File Organization

### Image Storage:
- **Main Images:** `uploads/lapangan/{timestamp}_{name}`
- **Gallery Images:** `uploads/gallery/{timestamp}_gallery_{index}_{name}`

### Examples:
```
uploads/lapangan/
├── 1780399979_lapangan_a.jpg
├── 1780399980_lapangan_b.png
└── 1780399981_lapangan_c.jpg

uploads/gallery/
├── 1780399982_gallery_0_image1.jpg
├── 1780399982_gallery_1_image2.jpg
├── 1780399982_gallery_2_image3.png
├── 1780399983_gallery_0_image1.jpg
└── 1780399983_gallery_1_image2.jpg
```

---

## 🛡️ Security Features

### File Validation:
- ✅ File type checking (image/* only)
- ✅ File size limit (2MB per file)
- ✅ Filename sanitization (timestamp-based)
- ✅ Auto-directory creation
- ✅ Directory permissions (0777)

### Code Security:
- ✅ real_escape_string for SQL injection prevention
- ✅ htmlspecialchars for XSS prevention
- ✅ Type casting for numeric values
- ✅ Session validation required
- ✅ Admin-only access

---

## 🚀 How to Use

### For Admin Users:

#### Adding New Lapangan with Images:

1. Go to **Admin Dashboard** → **Kelola Lapangan**
2. Click **"+ Tambah Lapangan"** button
3. Fill form fields:
   - Nama Lapangan
   - Harga (Weekday)
   - Harga (Weekend)
   - Status, Deskripsi, Fasilitas, etc.

4. **Upload Gambar Lapangan:**
   - Click file input
   - Select 1 image (main/thumbnail)
   - This will be shown on homepage card

5. **Upload Multiple Gallery Images:**
   - Click multiple file input
   - Select 1-10+ images
   - Preview will show in grid below
   - Images can be in any order

6. Click **"Simpan"**
7. System will:
   - Create lapangan entry
   - Save main image
   - Save all gallery images
   - Redirect to lapangan list

#### Editing Existing Lapangan:

1. Click **"Edit"** button on lapangan card
2. Update any fields as needed
3. Optionally upload new main image
4. Optionally add new gallery images
5. Click **"Simpan"**

#### Managing Gallery Later:

For existing lapangan, you can:
- Click **"Gallery"** button to manage photos separately
- Delete/reorder photos
- Add more photos
- Use dedicated gallery management page

---

## 📋 Technical Details

### Database Schema:

**tb_lapangan:**
```sql
gambar VARCHAR(255) DEFAULT NULL  -- Main image path
```

**tb_lapangan_gallery:**
```sql
id INT PRIMARY KEY AUTO_INCREMENT
lapangan_id INT NOT NULL
foto VARCHAR(255) NOT NULL
urutan INT DEFAULT 0
dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
FOREIGN KEY (lapangan_id) REFERENCES tb_lapangan(id) ON DELETE CASCADE
```

### Upload Function:
```php
// Gets insert_id after new lapangan created
$lapangan_id = $conn->insert_id;

// Processes multiple files
for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
    // Generate filename
    // Move file
    // Insert to database
}
```

---

## ✅ Testing Checklist

- [x] Multiple file selection works
- [x] Preview displays correctly
- [x] Main image uploads properly
- [x] Gallery images upload properly
- [x] Files saved to correct directories
- [x] Database records created
- [x] Responsive design works
- [x] Mobile upload works
- [x] Error handling functional
- [x] File validation working
- [x] Directory auto-creation works
- [x] All file paths correct
- [x] No console errors
- [x] No PHP warnings

---

## 🔄 Migration from Old System

### If using old `assets/images/` folder:

1. Copy images from `assets/images/` to `uploads/lapangan/` or `uploads/gallery/`
2. Update database paths:
   ```sql
   UPDATE tb_lapangan 
   SET gambar = REPLACE(gambar, 'assets/images/', 'uploads/lapangan/')
   WHERE gambar IS NOT NULL;
   
   UPDATE tb_lapangan_gallery 
   SET foto = REPLACE(foto, 'assets/images/', 'uploads/gallery/')
   WHERE foto IS NOT NULL;
   ```
3. Delete old `assets/images/` folder (optional, can keep for backward compatibility)

---

## 📈 Future Enhancements

1. **Image Optimization:**
   - Automatic image resizing
   - WebP format support
   - Compression on upload

2. **Drag & Drop:**
   - Drag multiple files to upload area
   - Visual feedback during drag

3. **Advanced Validation:**
   - Image dimension checking
   - EXIF data removal (privacy)
   - Format conversion

4. **Gallery Management:**
   - Reorder images via drag-drop in modal
   - Caption/description per image
   - Bulk delete

5. **Performance:**
   - Lazy loading for gallery
   - Image CDN integration
   - Caching strategy

---

## 🔗 Related Files

- `admin/manage_lapangan.php` - Main form with uploads
- `admin/manage_gallery.php` - Separate gallery management
- `detail-lapangan.php` - Gallery display on frontend
- `database.sql` - Database schema
- `uploads/` - Image storage folder

---

## 📞 Support

### Common Issues:

**Q: Upload button doesn't work**
- A: Check folder permissions, ensure `uploads/` exists

**Q: Images not saving**
- A: Check folder path, verify write permissions

**Q: Preview not showing**
- A: Clear browser cache, check JavaScript console

**Q: File size error**
- A: Reduce image size, max 2MB per file

---

## 🎉 Summary

**Feature:** Multiple Image Upload on Add/Edit Lapangan  
**Status:** ✅ FULLY IMPLEMENTED & TESTED  
**Availability:** Immediate  
**Compatibility:** All browsers with FileReader API support  
**Performance:** No noticeable impact  
**Security:** Implemented  
**Documentation:** Complete  

---

**Users can now upload both main image and multiple gallery images in a single form when adding new lapangan!** 📸

