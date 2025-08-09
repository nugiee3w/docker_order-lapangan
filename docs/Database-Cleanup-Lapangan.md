# 🧹 **Database Cleanup: Duplikasi Tabel Lapangan**

## 🔍 **Masalah yang Ditemukan:**

Database `lapangan_service` memiliki **2 tabel lapangan**:

### **✅ Tabel yang BENAR: `lapangans`**
- Dibuat melalui migration resmi
- Digunakan oleh model `Lapangan.php` 
- Berisi data aktual aplikasi (7 records)
- Struktur: `nama`, `jenis`, `harga_per_jam`, `status`, `fasilitas`, `lokasi`, `gambar`

### **❌ Tabel yang SALAH: `lapangan`**
- Tidak ada migration resmi
- Tidak digunakan oleh aplikasi
- Berisi data lama/testing (3 records)
- Struktur: `name`, `type`, `price_per_hour`, `status`, `facilities`, `image_url`

## 📊 **Data Comparison:**

### **Data di `lapangans` (AKTIF):**
```
ID | Nama                    | Jenis     | Harga     | Status      | Data
4  | Lapangan Futsal 2       | Futsal    | 60,000    | tersedia    | ✅ Ada gambar & lokasi
5  | Lapangan Badminton 1    | Futsal    | 40,000    | tersedia    | ✅ Ada gambar & lokasi  
8  | Lapangan Futsal 1       | Futsal    | 80,000    | tersedia    | ✅ Ada gambar & lokasi
10 | Lapangan Badminton 2    | Badminton | 40,000    | tersedia    | ✅ Ada gambar & lokasi
11 | Lapangan Basket         | Basket    | 80,000    | tersedia    | ✅ Ada gambar & lokasi
12 | Tenis Meja 1           | Tenis     | 20,000    | tersedia    | ✅ Ada gambar & lokasi
13 | Meja Tenis Meja 2      | Tenis     | 20,000    | maintenance | ✅ Ada gambar & lokasi
```

### **Data di `lapangan` (TIDAK AKTIF):**
```
ID | Name                    | Type       | Price      | Status    | Data
1  | Lapangan Futsal A       | Futsal     | 100,000    | available | ❌ Tidak ada gambar
2  | Lapangan Badminton 1    | Badminton  | 50,000     | available | ❌ Tidak ada gambar
3  | Lapangan Basket Outdoor | Basketball | 75,000     | available | ❌ Tidak ada gambar
```

## ⚠️ **Dampak pada Order Service:**

Mari periksa apakah order service mereferensi ID dari tabel yang salah:

### **Data Orders Saat Ini:**
```sql
SELECT lapangan_id, customer_name, total_price, status FROM orders;
```
**Hasil:**
- `lapangan_id: 5` → Berkorelasi dengan `lapangans.id=5` ✅
- `lapangan_id: 13` → Berkorelasi dengan `lapangans.id=13` ✅

**✅ TIDAK ADA MASALAH** - Orders sudah mereferensi tabel yang benar (`lapangans`)

## 🔧 **Solusi Recommended:**

### **Option 1: Hapus Tabel `lapangan` (RECOMMENDED)**
```sql
-- BACKUP dulu sebelum hapus
SELECT * FROM lapangan;

-- Hapus tabel yang tidak terpakai
DROP TABLE lapangan;
```

### **Option 2: Rename untuk Clarity**
```sql
-- Rename tabel lama jadi backup
RENAME TABLE lapangan TO lapangan_backup_old;
```

### **Option 3: Migrasi Data (jika diperlukan)**
```sql
-- Jika ada data penting di tabel lama yang perlu dipindah
-- (TIDAK DIPERLUKAN karena data sudah ada di lapangans)
```

## 🎯 **Langkah Cleanup:**

### **1. Backup Data Lama**
```bash
docker exec order-db mysqldump -u lapangan_user lapangan_service lapangan > lapangan_backup.sql
```

### **2. Verify No Dependencies**
```sql
-- Cek apakah ada foreign key references
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_NAME = 'lapangan';
```

### **3. Drop Tabel Tidak Terpakai**
```sql
DROP TABLE lapangan;
```

### **4. Verify Cleanup**
```sql
SHOW TABLES;
-- Pastikan hanya ada 'lapangans', bukan 'lapangan'
```

## ✅ **Expected Result After Cleanup:**

- ✅ Hanya ada 1 tabel: `lapangans`
- ✅ Aplikasi web tetap berfungsi normal
- ✅ Order service tetap berfungsi normal  
- ✅ Database lebih bersih dan tidak membingungkan
- ✅ DBeaver queries lebih akurat

## 🚨 **PENTING - Sebelum Cleanup:**

1. **Backup database** terlebih dahulu
2. **Test aplikasi** masih berfungsi
3. **Verify orders** masih bisa dibuat
4. **Confirm** tidak ada dependency ke tabel `lapangan`

---

**📝 Next Action**: Execute cleanup script setelah backup dan verification selesai.
