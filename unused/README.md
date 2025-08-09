# 🏟️ Docker Order Lapangan - Microservices

Sistem manajemen pemesanan lapangan olahraga menggunakan ## 🗄️ **Database Management dengan DBeaver**

Untuk mengelola dan melihat data dalam database dengan interface grafis:

📖 **[Panduan Lengkap Koneksi DBeaver](docs/DBeaver-Connection-Guide.md)**  
📋 **[Summary & Quick Start](docs/DBeaver-Setup-Summary.md)**

**Quick Setup:**
- **Lapangan DB**: `localhost:3308` → Database: `lapangan_service` → User: `lapangan_user`
- **Order DB**: `localhost:3307` → Database: `order_service` → User: `order_user`
- **Password**: Kosong/blank untuk kedua database

**File Bantuan:**
- ⚙️ `docs/dbeaver-connections.xml` - Import konfigurasi otomatis
- 📊 `docs/dbeaver-queries.sql` - 20+ query siap pakai untuk analisis dengan arsitektur microservices.

## 🚀 **Quick Start**

### **Jalankan Semua Service:**
```bash
docker-compose up -d
```

### **Akses Aplikasi:**
- **Order Service:** http://localhost:8000
- **Lapangan Service:** http://localhost:8001

## 🗄️ **Database Management**

### **DBeaver/Direct Connection:**
```
Lapangan DB: localhost:3308/lapangan_service (User: lapangan_user)
Order DB: localhost:3307/order_service (User: order_user)
```

## 📊 **Services Overview**

| Service | Port | Database | Purpose |
|---------|------|----------|---------|
| Order Service | 8000 | order_service | Manajemen pemesanan |
| Lapangan Service | 8001 | lapangan_service | Manajemen lapangan |

## 🔧 **Development**

### **Setup Environment:**
```bash
# Clone repository
git clone <repo-url>
cd docker-OrderLapangan

# Copy environment file
cp .env.example .env

# Start services
docker-compose up -d

# Run migrations
docker exec order-service php artisan migrate
docker exec lapangan-service php artisan migrate
```

## 📋 **Features**

### **✅ Order Service:**
- [x] User authentication (Sanctum)
- [x] Order management (CRUD)
- [x] Customer management
- [x] Payment status tracking
- [x] API endpoints

### **✅ Lapangan Service:**
- [x] Lapangan management
- [x] Scheduling system
- [x] Facility management
- [x] API endpoints

### **✅ Database:**
- [x] MySQL 8.0 with optimized indexes
- [x] Sample data and admin users
- [x] Cross-service data integrity

## 📚 **Documentation**

- [API Documentation](docs/API.md)
- [Database Schema](docs/Database.md)

## 🎯 **Sample Data**

**Test Users:**
```
Admin: admin@lapangan.com / password
Staff: staff@lapangan.com / password
```

**Sample Orders:** 2 confirmed bookings
**Sample Lapangan:** 3 different sports fields

## 🛠️ **Maintenance**

### **Backup Database:**
```bash
docker exec order-db mysqldump -u order_user order_service > backup.sql
```

### **View Logs:**
```bash
docker-compose logs -f order-service
docker-compose logs -f lapangan-service
```

### **Reset Data:**
```bash
docker-compose down -v
docker-compose up -d
```

## �️ **Database Management dengan DBeaver**

Untuk mengelola dan melihat data dalam database dengan interface grafis:

📖 **[Panduan Lengkap Koneksi DBeaver](docs/DBeaver-Connection-Guide.md)**

**Quick Setup:**
- **Lapangan DB**: `localhost:3308` → Database: `lapangan_service` → User: `lapangan_user`
- **Order DB**: `localhost:3307` → Database: `order_service` → User: `order_user`
- **Password**: Kosong/blank untuk kedua database

## �🔍 **Troubleshooting**

**Service not accessible:**
```bash
docker ps  # Check running containers
docker-compose restart <service-name>
```

**Database connection issues:**
```bash
docker exec -it order-db mysql -u order_user order_service
```

**View container logs:**
```bash
docker logs <container-name>
```

---

**🎉 Happy Coding!** 
Project ini siap untuk development dan production deployment.
