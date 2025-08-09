# 🗄️ Panduan Koneksi Database dengan DBeaver

## 📋 **Informasi Koneksi Database**

### **🏟️ Database Lapangan Service:**
```
Host: localhost
Port: 3308
Database: lapangan_service
Username: lapangan_user
Password: (kosong/blank)
```

### **🛍️ Database Order Service:**
```
Host: localhost
Port: 3307
Database: order_service
Username: order_user
Password: (kosong/blank)
```

## 📥 **1. Download dan Install DBeaver**

1. **Download DBeaver Community:**
   - Kunjungi: https://dbeaver.io/download/
   - Pilih **DBeaver Community Edition** (gratis)
   - Download sesuai OS Anda (Windows/Mac/Linux)

2. **Install DBeaver:**
   - Jalankan installer yang sudah didownload
   - Ikuti wizard instalasi standar
   - Launch DBeaver setelah instalasi selesai

## 🔗 **2. Setup Koneksi Database Lapangan Service**

### **Step 1: Buat Koneksi Baru**
1. Buka DBeaver
2. Klik ikon **"New Database Connection"** (⚡) di toolbar
3. Atau menu **Database → New Database Connection**

### **Step 2: Pilih Database Type**
1. Pilih **MySQL** dari daftar database
2. Klik **Next**

### **Step 3: Konfigurasi Connection Settings**
```
┌─────────────────────────────────────┐
│ Connection Settings                 │
├─────────────────────────────────────┤
│ Server Host: localhost              │
│ Port: 3308                          │
│ Database: lapangan_service          │
│ Username: lapangan_user             │
│ Password: (biarkan kosong)          │
│                                     │
│ ☑ Save password                     │
└─────────────────────────────────────┘
```

### **Step 4: Test dan Finish**
1. Klik **Test Connection**
2. Jika berhasil, akan muncul "Connected" ✅
3. Klik **Finish**

## 🔗 **3. Setup Koneksi Database Order Service**

### **Ulangi Langkah yang Sama:**
1. **New Database Connection** → **MySQL** → **Next**
2. **Konfigurasi:**
```
┌─────────────────────────────────────┐
│ Connection Settings                 │
├─────────────────────────────────────┤
│ Server Host: localhost              │
│ Port: 3307                          │
│ Database: order_service             │
│ Username: order_user                │
│ Password: (biarkan kosong)          │
│                                     │
│ ☑ Save password                     │
└─────────────────────────────────────┘
```
3. **Test Connection** → **Finish**

## 🔧 **4. Konfigurasi Advanced (Opsional)**

### **Driver Properties (untuk performa optimal):**
1. Dalam dialog Connection Settings
2. Klik tab **Driver properties**
3. Tambahkan properties ini:
```
allowMultiQueries=true
useSSL=false
allowPublicKeyRetrieval=true
serverTimezone=Asia/Jakarta
useUnicode=true
characterEncoding=UTF8
```

## 📊 **5. Menggunakan DBeaver**

### **📋 Browse Data:**
1. **Expand database** di Database Navigator
2. **Expand Tables**
3. **Double-click tabel** untuk melihat data
4. Atau **Right-click → Open Data**

### **🔍 Menjalankan Query:**
1. **Right-click database** → **SQL Editor → Open SQL Script**
2. Tulis query, contoh:
```sql
-- Lihat semua orders
SELECT * FROM orders LIMIT 10;

-- Lihat revenue hari ini
SELECT SUM(total_price) as revenue_today 
FROM orders 
WHERE DATE(booking_date) = CURDATE() 
AND status = 'confirmed';
```
3. **Ctrl+Enter** atau klik **Execute** ▶️

### **📤 Export Data:**
1. **Right-click tabel** → **Export Data**
2. Pilih format: **Excel, CSV, JSON, SQL**
3. Konfigurasi export settings
4. **Start** export

## 🚨 **6. Troubleshooting**

### **❌ "Cannot connect to database"**
**Solusi:**
```bash
# 1. Pastikan containers berjalan
docker ps

# 2. Restart database jika perlu
docker-compose restart order-db lapangan-db

# 3. Test koneksi manual
docker exec -it order-db mysql -u order_user order_service
```

### **❌ "Access denied for user"**
**Solusi:**
- Pastikan username benar: `lapangan_user` atau `order_user`
- Password biarkan kosong (blank)
- Cek di tab **Authentication**

### **❌ "Driver not found"**
**Solusi:**
1. DBeaver akan otomatis download MySQL driver
2. Jika gagal, klik **Download** di dialog driver
3. Atau **Database → Driver Manager → MySQL → Download**

### **❌ "Cross-database query denied"**
**Masalah:** User tidak punya permission untuk query antar database
**Solusi:**
1. **Gunakan 2 koneksi terpisah** untuk setiap database
2. **Export data** dari satu database, import ke yang lain
3. **Buat view** di masing-masing database untuk data yang diperlukan
4. **Atau gunakan query terpisah** dan gabungkan hasil di Excel/aplikasi lain

**Alternative untuk analisis cross-database:**
```sql
-- Di lapangan_service: Export lapangan data
SELECT id, name, type, price_per_hour FROM lapangan;

-- Di order_service: Analisis orders per lapangan_id  
SELECT lapangan_id, COUNT(*) as orders, SUM(total_price) as revenue 
FROM orders WHERE status = 'confirmed' GROUP BY lapangan_id;
```

## 🎨 **7. Tips Optimasi DBeaver**

### **🎯 Bookmark Queries Berguna:**
```sql
-- 1. Daily Revenue Report
SELECT 
    DATE(booking_date) as tanggal,
    COUNT(*) as total_orders,
    SUM(total_price) as revenue
FROM orders 
WHERE status = 'confirmed'
GROUP BY DATE(booking_date)
ORDER BY tanggal DESC;

-- 2. Top Customers
SELECT 
    customer_name,
    customer_email,
    COUNT(*) as total_orders,
    SUM(total_price) as total_spent
FROM orders
GROUP BY customer_email
ORDER BY total_spent DESC;

-- 3. Lapangan Utilization
SELECT 
    l.name,
    l.type,
    COUNT(o.id) as bookings,
    SUM(o.total_price) as revenue
FROM lapangan_service.lapangan l
LEFT JOIN order_service.orders o ON l.id = o.lapangan_id
GROUP BY l.id;
```

### **🔄 Auto-refresh:**
1. **Right-click connection** → **Edit Connection**
2. **Connection Settings** → **Advanced**
3. Set **Keep-alive interval**: 300 seconds

### **🎨 Color Coding:**
1. **Right-click connection** → **Edit Connection** 
2. **Connection Details** → **Connection color**
3. Set warna berbeda untuk setiap database:
   - 🟢 Green untuk Lapangan Service
   - 🔵 Blue untuk Order Service

## ✅ **8. Verifikasi Setup Berhasil**

Setelah setup selesai, Anda akan melihat:

```
Database Navigator:
├── 📁 lapangan_service (localhost:3308)
│   ├── 📋 Tables
│   │   ├── lapangan (3 rows)
│   │   ├── jadwal_lapangans (392 rows)
│   │   ├── users (2 rows)
│   │   └── migrations
│   └── 🔍 SQL Scripts
│
└── 📁 order_service (localhost:3307)
    ├── 📋 Tables
    │   ├── orders (2 rows)
    │   ├── users (5 rows)
    │   └── migrations
    └── 🔍 SQL Scripts
```

## 🎯 **9. Query Siap Pakai**

Copy-paste query ini ke SQL Editor:

```sql
-- Cross-database analysis (gunakan di order_service)
SELECT 
    l.name as lapangan_name,
    l.type,
    COUNT(o.id) as total_bookings,
    SUM(o.total_price) as total_revenue,
    AVG(o.total_price) as avg_price_per_booking
FROM lapangan_service.lapangan l
LEFT JOIN orders o ON l.id = o.lapangan_id AND o.status = 'confirmed'
GROUP BY l.id, l.name, l.type
ORDER BY total_revenue DESC;
```

## 📁 **10. File Bantuan Tersedia**

### **🔗 Import Konfigurasi Otomatis:**
File: `docs/dbeaver-connections.xml`

**Cara Import:**
1. **File** → **Import** → **General** → **Projects from Folder or Archive**
2. Atau copy-paste connection settings dari file XML
3. Restart DBeaver untuk melihat koneksi baru

### **📊 Query Collection:**
File: `docs/dbeaver-queries.sql`

**17+ Query Siap Pakai:**
- 📋 Overview data lapangan dan orders
- 💰 Analisis revenue dan performa
- 🏆 Top customers dan lapangan terpopuler  
- 📈 Cross-database analytics
- 🔍 Monitoring dan health checks
- 🛠️ Maintenance queries

**Cara Menggunakan:**
1. Buka file `dbeaver-queries.sql` di DBeaver
2. Copy query yang dibutuhkan
3. Paste ke SQL Editor dan execute

## 🚀 **11. Tips Advanced DBeaver**

### **📈 Dashboard Custom:**
1. **Window** → **Show View** → **Dashboard**
2. Add widgets untuk monitoring real-time
3. Pin queries yang sering digunakan

### **🔄 Scheduled Tasks:**
1. **Tools** → **Task Management**
2. Schedule export/backup otomatis
3. Set up alerts untuk perubahan data

### **📱 Mobile Access:**
- Install DBeaver CloudBeaver untuk akses web
- Share dashboard dengan team
- Remote database management

---

**🎉 Selamat!** Database Anda sekarang terhubung dengan DBeaver dan siap untuk analisis data! 📊

**📚 File Bantuan:**
- � **Setup Guide**: `docs/DBeaver-Connection-Guide.md` (file ini)
- ⚙️ **Config Import**: `docs/dbeaver-connections.xml`
- �📊 **Query Collection**: `docs/dbeaver-queries.sql`
