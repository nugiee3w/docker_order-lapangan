# 🗄️ PhpMyAdmin Database Management Guide

## 🌐 **Akses PhpMyAdmin**

**URL:** http://localhost:8080

## 🔐 **Login Credentials**

### **🏟️ Database Lapangan Service:**
```
Server: lapangan-db
Username: lapangan_user
Password: (kosong/blank)
Database: lapangan_service
```

### **🛍️ Database Order Service:**
```
Server: order-db  
Username: order_user
Password: (kosong/blank)
Database: order_service
```

### **🔑 Root Access (Full Access):**
```
Server: order-db atau lapangan-db
Username: root
Password: (kosong/blank)
```

## 📊 **Apa yang Bisa Anda Lakukan di PhpMyAdmin:**

### **1. 📋 Browse Data**
- Klik database → Table → Browse
- Filter dan sort data
- Export data ke Excel/CSV

### **2. 🔍 SQL Query**
- Tab "SQL" untuk menjalankan custom queries
- Import file `dbeaver-queries.sql` untuk query siap pakai

### **3. 📈 Generate Reports**
- Pilih tabel → Operations → Print view
- Export selected rows
- Create charts dari data

### **4. 🛠️ Database Administration**
- Create/Drop tables
- Modify table structure
- Manage indexes
- User management

### **5. 📤 Import/Export**
- Export database backup
- Import SQL files
- Bulk data operations

## 🎯 **Quick Actions:**

### **📊 View All Orders:**
1. Select `order_service` database
2. Click `orders` table
3. Click "Browse" tab

### **🏟️ View All Lapangan:**
1. Select `lapangan_service` database  
2. Click `lapangan` table
3. Click "Browse" tab

### **💰 Revenue Report:**
1. Go to SQL tab
2. Run query:
```sql
SELECT 
    DATE(booking_date) as tanggal,
    COUNT(*) as total_orders,
    SUM(total_price) as revenue
FROM order_service.orders 
WHERE status = 'confirmed'
GROUP BY DATE(booking_date)
ORDER BY tanggal DESC;
```

### **🔗 Cross-Database Query:**
```sql
SELECT 
    l.name as lapangan_name,
    COUNT(o.id) as total_bookings,
    SUM(o.total_price) as revenue
FROM lapangan_service.lapangan l
LEFT JOIN order_service.orders o ON l.id = o.lapangan_id
GROUP BY l.id
ORDER BY revenue DESC;
```

## 💡 **Tips PhpMyAdmin:**

### **🚀 Performance:**
- Use LIMIT in queries for large tables
- Create bookmarks for frequent queries
- Use search functionality

### **🔒 Security:**
- Only use root for administrative tasks
- Use specific user accounts for daily operations
- Regular backup exports

### **📱 Mobile-Friendly:**
- PhpMyAdmin responsive untuk mobile
- Touch-friendly interface
- Swipe navigation

### **⚡ Shortcuts:**
- `Ctrl + Enter` → Execute SQL
- `Ctrl + S` → Save query
- `F1` → Help

## 🔄 **Database Maintenance:**

### **Backup Database:**
1. Select database
2. Export tab
3. Choose format (SQL recommended)
4. Download

### **Monitor Performance:**
1. Status tab → View server status
2. Check slow queries
3. Monitor connection usage

## 🎨 **Customization:**

### **Theme:**
- Settings → Themes
- Choose from multiple themes

### **Language:**
- Settings → Language
- Support untuk bahasa Indonesia

## 📞 **Troubleshooting:**

### **Cannot Connect:**
- Check if containers are running: `docker ps`
- Verify database credentials
- Restart PhpMyAdmin: `docker-compose restart phpmyadmin`

### **Slow Performance:**
- Add LIMIT to queries
- Check database indexes
- Monitor server resources

---

**🎉 Happy Database Managing!** 
PhpMyAdmin memberikan interface yang user-friendly untuk mengelola database project Order Lapangan Anda.
