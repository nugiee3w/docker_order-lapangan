-- ================================================
-- 📊 DBEAVER QUERY COLLECTION
-- Kumpulan Query Siap Pakai untuk Analisis Data
-- ================================================

-- ==============================================
-- 🏟️ LAPANGAN SERVICE QUERIES
-- Gunakan pada database: lapangan_service
-- ==============================================

-- 🎯 1. Overview Lapangan (Updated - menggunakan tabel lapangans)
SELECT 
    id,
    nama,
    jenis,
    harga_per_jam,
    CASE 
        WHEN status = 'tersedia' THEN '✅ Available'
        WHEN status = 'maintenance' THEN '🔧 Maintenance'
        ELSE '❌ Not Available'
    END as availability_status,
    lokasi,
    CASE 
        WHEN gambar IS NOT NULL THEN '📷 Has Image'
        ELSE '❌ No Image'
    END as image_status
FROM lapangans
ORDER BY jenis, nama;

-- ⏰ 2. Jadwal Lapangan Hari Ini (Updated)
SELECT 
    l.nama as lapangan_name,
    l.jenis,
    jl.date,
    jl.start_time,
    jl.end_time,
    CASE 
        WHEN jl.is_available = 1 THEN '🟢 Available'
        ELSE '🔴 Booked'
    END as status
FROM lapangans l
JOIN jadwal_lapangans jl ON l.id = jl.lapangan_id
WHERE jl.date = CURDATE()
ORDER BY l.nama, jl.start_time;

-- 📅 3. Ketersediaan Lapangan Minggu Ini (Updated)
SELECT 
    l.nama,
    l.jenis,
    COUNT(jl.id) as total_slots,
    SUM(CASE WHEN jl.is_available = 1 THEN 1 ELSE 0 END) as available_slots,
    SUM(CASE WHEN jl.is_available = 0 THEN 1 ELSE 0 END) as booked_slots,
    ROUND(
        (SUM(CASE WHEN jl.is_available = 0 THEN 1 ELSE 0 END) * 100.0 / COUNT(jl.id)), 2
    ) as utilization_percentage
FROM lapangans l
JOIN jadwal_lapangans jl ON l.id = jl.lapangan_id
WHERE jl.date >= CURDATE() 
    AND jl.date < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
GROUP BY l.id, l.nama, l.jenis
ORDER BY utilization_percentage DESC;

-- 👥 4. Data Users/Admins
SELECT 
    id,
    name,
    email,
    email_verified_at,
    created_at,
    updated_at
FROM users
ORDER BY created_at DESC;

-- ==============================================
-- 🛍️ ORDER SERVICE QUERIES  
-- Gunakan pada database: order_service
-- ==============================================

-- 📋 5. Overview Orders
SELECT 
    id,
    customer_name,
    customer_email,
    lapangan_id,
    booking_date,
    start_time,
    end_time,
    total_price,
    status,
    created_at
FROM orders
ORDER BY created_at DESC;

-- 💰 6. Revenue Analysis
SELECT 
    DATE(booking_date) as tanggal,
    COUNT(*) as total_orders,
    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_orders,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders,
    SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) as confirmed_revenue,
    AVG(CASE WHEN status = 'confirmed' THEN total_price END) as avg_order_value
FROM orders
WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(booking_date)
ORDER BY tanggal DESC;

-- 🏆 7. Top Customers
SELECT 
    customer_name,
    customer_email,
    customer_phone,
    COUNT(*) as total_orders,
    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_orders,
    SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) as total_spent,
    AVG(CASE WHEN status = 'confirmed' THEN total_price END) as avg_spent_per_order,
    MAX(created_at) as last_order_date
FROM orders
GROUP BY customer_email
HAVING confirmed_orders > 0
ORDER BY total_spent DESC, total_orders DESC;

-- ⏰ 8. Analisis Waktu Booking Favorit
SELECT 
    start_time,
    COUNT(*) as total_bookings,
    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_bookings,
    ROUND(AVG(total_price), 2) as avg_price,
    GROUP_CONCAT(DISTINCT customer_name ORDER BY customer_name SEPARATOR ', ') as customers
FROM orders
WHERE status = 'confirmed'
GROUP BY start_time
ORDER BY confirmed_bookings DESC;

-- 📊 9. Status Order Distribution
SELECT 
    status,
    COUNT(*) as jumlah,
    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM orders)), 2) as persentase,
    SUM(total_price) as total_value
FROM orders
GROUP BY status
ORDER BY jumlah DESC;

-- 👥 10. Data Users Order Service
SELECT 
    id,
    name,
    email,
    email_verified_at,
    created_at
FROM users
ORDER BY created_at DESC;

-- ==============================================
-- 🔗 LAPANGAN ANALYSIS (untuk gabungan manual)
-- Gunakan pada database: lapangan_service  
-- ==============================================

-- 🎯 11. Export Lapangan Data (untuk analisis gabungan)
SELECT 
    id as lapangan_id,
    nama as lapangan_name,
    jenis as lapangan_type,
    harga_per_jam as price_per_hour,
    CASE 
        WHEN status = 'tersedia' THEN 'Available'
        WHEN status = 'maintenance' THEN 'Maintenance'
        ELSE 'Not Available'
    END as availability_status,
    lokasi,
    gambar
FROM lapangans
ORDER BY jenis, nama;

-- 📊 12. Analisis Jadwal per Lapangan
SELECT 
    l.id as lapangan_id,
    l.nama,
    l.jenis,
    COUNT(jl.id) as total_time_slots,
    SUM(CASE WHEN jl.is_available = 1 THEN 1 ELSE 0 END) as available_slots,
    SUM(CASE WHEN jl.is_available = 0 THEN 1 ELSE 0 END) as booked_slots,
    ROUND(
        (SUM(CASE WHEN jl.is_available = 0 THEN 1 ELSE 0 END) * 100.0 / COUNT(jl.id)), 2
    ) as utilization_percentage
FROM lapangans l
LEFT JOIN jadwal_lapangans jl ON l.id = jl.lapangan_id
WHERE jl.date >= CURDATE()
GROUP BY l.id, l.nama, l.jenis
ORDER BY utilization_percentage DESC;

-- ==============================================
-- � ORDER ANALYSIS (untuk gabungan manual)
-- Gunakan pada database: order_service
-- ==============================================

-- 🎯 13. Orders Summary per Lapangan (untuk gabungan dengan data lapangan)
SELECT 
    lapangan_id,
    COUNT(*) as total_bookings,
    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_bookings,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings,
    SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) as total_revenue,
    AVG(CASE WHEN status = 'confirmed' THEN total_price END) as avg_revenue_per_booking,
    ROUND(
        (COUNT(CASE WHEN status = 'confirmed' THEN 1 END) * 100.0 / 
         NULLIF(COUNT(*), 0)), 2
    ) as confirmation_rate,
    MIN(booking_date) as first_booking,
    MAX(booking_date) as last_booking
FROM orders
GROUP BY lapangan_id
ORDER BY total_revenue DESC, confirmed_bookings DESC;

-- 🏅 14. Lapangan Performance Export (gabungkan dengan query 11)
-- Export hasil query 11 dan 13, lalu gabungkan di Excel/Sheets untuk analisis lengkap

-- ==============================================
-- 🔍 MONITORING & HEALTH CHECK QUERIES
-- ==============================================

-- 🏥 15. Order Service Health Check (jalankan di order_service)
SELECT 
    'Order Service' as service,
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()) as today_orders,
    (SELECT COUNT(*) FROM orders WHERE status = 'pending') as pending_orders,
    (SELECT COUNT(*) FROM orders WHERE status = 'confirmed') as confirmed_orders,
    (SELECT COUNT(*) FROM users) as total_users;

-- 🏥 16. Lapangan Service Health Check (jalankan di lapangan_service)  
SELECT 
    'Lapangan Service' as service,
    (SELECT COUNT(*) FROM lapangan) as total_lapangan,
    (SELECT COUNT(*) FROM jadwal_lapangans WHERE date = CURDATE()) as today_schedules,
    (SELECT COUNT(*) FROM jadwal_lapangans WHERE date = CURDATE() AND is_available = 1) as available_today,
    (SELECT COUNT(*) FROM users) as total_users;

-- 📊 17. Recent Activity Summary (order_service)
SELECT 
    'Recent Orders (Last 24h)' as activity_type,
    COUNT(*) as count,
    MAX(created_at) as last_activity
FROM orders 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
UNION ALL
SELECT 
    'Pending Orders' as activity_type,
    COUNT(*) as count,
    MAX(created_at) as last_activity
FROM orders 
WHERE status = 'pending'
UNION ALL
SELECT 
    'Confirmed Orders Today' as activity_type,
    COUNT(*) as count,
    MAX(created_at) as last_activity
FROM orders 
WHERE DATE(booking_date) = CURDATE() AND status = 'confirmed';

-- ==============================================
-- 🛠️ MAINTENANCE QUERIES (order_service)
-- ==============================================

-- 🧹 18. Old Pending Orders Review (VIEW ONLY - DON'T DELETE)
SELECT 
    id,
    customer_name,
    customer_email,
    booking_date,
    total_price,
    created_at,
    DATEDIFF(NOW(), created_at) as days_old
FROM orders 
WHERE status = 'pending' 
    AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at;

-- 🔄 19. Duplicate Email Check
SELECT 
    customer_email,
    COUNT(*) as order_count,
    GROUP_CONCAT(id ORDER BY created_at SEPARATOR ', ') as order_ids,
    MIN(created_at) as first_order,
    MAX(created_at) as last_order
FROM orders
GROUP BY customer_email
HAVING COUNT(*) > 1
ORDER BY order_count DESC;

-- 📅 20. Booking Conflicts Check (same lapangan, same time)
SELECT 
    o1.id as order1_id,
    o2.id as order2_id,
    o1.lapangan_id,
    o1.booking_date,
    o1.start_time,
    o1.end_time,
    o1.status as order1_status,
    o2.status as order2_status,
    o1.customer_name as customer1,
    o2.customer_name as customer2
FROM orders o1
JOIN orders o2 ON 
    o1.lapangan_id = o2.lapangan_id 
    AND o1.booking_date = o2.booking_date
    AND o1.start_time = o2.start_time
    AND o1.id < o2.id
WHERE (o1.status = 'confirmed' OR o2.status = 'confirmed')
ORDER BY o1.booking_date, o1.start_time;

-- ================================================
-- 💡 CARA MENGGUNAKAN QUERIES:
-- ================================================
-- 1. Buka DBeaver dan connect ke database yang sesuai
-- 2. Copy query yang dibutuhkan dari file ini
-- 3. Paste ke SQL Editor di DBeaver  
-- 4. Pastikan sudah terhubung ke database yang benar:
--    - lapangan_service queries: gunakan koneksi lapangan_service
--    - order_service queries: gunakan koneksi order_service
-- 5. Execute dengan Ctrl+Enter atau tombol Run
-- 6. Export hasil ke Excel/CSV jika diperlukan
-- 
-- 📊 UNTUK ANALISIS GABUNGAN:
-- 1. Jalankan query 11 di lapangan_service 
-- 2. Jalankan query 13 di order_service
-- 3. Export kedua hasil ke Excel
-- 4. Gunakan VLOOKUP/INDEX-MATCH untuk menggabungkan data
-- ================================================
