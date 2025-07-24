# Triak Coffee Database Setup Guide

## Cara Menggunakan Database di XAMPP MySQL phpMyAdmin

### 1. **Persiapan XAMPP**
- Pastikan XAMPP sudah terinstall
- Start Apache dan MySQL dari XAMPP Control Panel
- Buka browser dan akses `http://localhost/phpmyadmin`

### 2. **Import Database**
- Di phpMyAdmin, klik tab "Import"
- Pilih file `triak_coffee_database.sql`
- Klik "Go" untuk mengeksekusi

### 3. **Verifikasi Database**
Setelah import berhasil, Anda akan memiliki:

#### **Tables:**
- `users` - Data pengguna (admin & user)
- `coffee_types` - Data kopi dengan atribut rule-based
- `user_preferences` - Preferensi pengguna untuk sistem rule
- `recommendations` - Rating, review, dan rekomendasi

#### **Sample Data:**
- **Admin Account:** username: `admin`, password: `admin123`
- **12 Coffee Varieties** dengan atribut lengkap untuk rule-based system
- **Sample preferences dan recommendations** untuk testing

### 4. **Struktur Rule-Based System**

#### **Atribut Kopi:**
- **Jenis Kopi:** Arabika, Robusta
- **Profil Rasa:** Fruity, Chocolate, Nutty, Earthy, Floral, Citrus, Caramel, Bitter Chocolate, Honey, Spicy, Bitter, Sweet
- **Proses:** Washed, Honey, Natural
- **Roast Level:** Light, Medium, Medium-Dark, Dark

#### **Metode Penyajian:**
- V60, French Press, Espresso, Tubruk, Cold Brew, Moka Pot

### 5. **20 Rules Terintegrasi**
Database sudah include stored procedure `GetRuleBasedRecommendation` yang mengimplementasikan semua 20 rules yang Anda berikan.

### 6. **Views dan Functions**
- `coffee_statistics` - Statistik kopi untuk admin dashboard
- `user_statistics` - Statistik user untuk manajemen
- `GetCoffeeMatchScore()` - Function untuk menghitung match score

### 7. **Konfigurasi PHP**
Update file `config/database.php` jika diperlukan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosong untuk XAMPP default
define('DB_NAME', 'triak_coffee');
```

### 8. **Testing System**
1. Login sebagai admin: `admin` / `admin123`
2. Tambah coffee baru dengan atribut rule-based
3. Buat user account baru
4. Set preferences user
5. Lihat rekomendasi berdasarkan rules

### 9. **Sample Queries untuk Testing**

#### Test Rule-Based Recommendation:
```sql
CALL GetRuleBasedRecommendation('Arabika', 'V60', 'Fruity', 'Washed', @roast, @rule);
SELECT @roast as recommended_roast, @rule as rule_applied;
```

#### View Coffee Statistics:
```sql
SELECT * FROM coffee_statistics ORDER BY average_rating DESC;
```

#### View User Statistics:
```sql
SELECT * FROM user_statistics;
```

### 10. **Backup Database**
Untuk backup database:
- Di phpMyAdmin, pilih database `triak_coffee`
- Klik tab "Export"
- Pilih format SQL dan klik "Go"

## Troubleshooting

### Error "Table already exists":
- Drop database terlebih dahulu: `DROP DATABASE triak_coffee;`
- Kemudian import ulang

### Error koneksi PHP:
- Pastikan MySQL service running di XAMPP
- Check username/password di `config/database.php`
- Pastikan extension `php_pdo_mysql` enabled

### Error foreign key:
- Pastikan InnoDB engine digunakan
- Check referential integrity

## Features Database

✅ **Complete Rule-Based System** - 20 rules terintegrasi
✅ **Sample Data** - 12 coffee varieties dengan atribut lengkap  
✅ **Admin Account** - Siap pakai untuk testing
✅ **Stored Procedures** - Rule-based recommendation logic
✅ **Views & Functions** - Analytics dan statistics
✅ **Proper Indexing** - Optimized untuk performance
✅ **Foreign Keys** - Data integrity terjamin

Database ini siap digunakan dengan sistem PHP Triak Coffee & Roaster yang sudah dibuat!