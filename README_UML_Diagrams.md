# 📊 Diagram UML - Sistem Rekomendasi Kopi Triak Coffee

## 📋 Daftar Diagram

### 1. Use Case Diagram (`use_case_diagram.puml`)
### 2. Class Diagram (`class_diagram.puml`)

## 🛠️ Cara Menggunakan Diagram PlantUML

### **Opsi 1: Online PlantUML Editor**
1. Buka [PlantUML Online Editor](http://www.plantuml.com/plantuml/uml/)
2. Copy-paste kode dari file `.puml`
3. Diagram akan otomatis di-generate
4. Download sebagai PNG, SVG, atau format lainnya

### **Opsi 2: VS Code Extension**
1. Install extension "PlantUML" di VS Code
2. Buka file `.puml`
3. Tekan `Alt+Shift+D` untuk preview
4. Export ke format yang diinginkan

### **Opsi 3: Command Line**
```bash
# Install PlantUML (Java required)
java -jar plantuml.jar use_case_diagram.puml
java -jar plantuml.jar class_diagram.puml
```

## 🔧 Perbaikan yang Dilakukan

### **Use Case Diagram:**
✅ **Masalah yang Diperbaiki:**
- ❌ Hapus include relationship yang bermasalah
- ❌ Perbaiki typo "Coffeee" → "Coffee"
- ✅ Tambah use case yang kurang (Atur Preferensi, Lihat Katalog, dll)
- ✅ Gunakan extend relationship untuk fitur opsional
- ✅ Tambah relationship yang logis

**Perubahan:**
```
SEBELUM:
- Riwayat Rekomendasi <<include>> Lihat Detail Proses Roasting (SALAH)
- Lihat Detail Proses Roasting <<include>> Mengelola Riwayat Rekomendasi (SALAH)

SESUDAH:
- Riwayat Rekomendasi <<extend>> Lihat Detail Proses Roasting (BENAR)
- Lihat Katalog Kopi <<extend>> Lihat Detail Kopi (BENAR)
- Mengelola Users <<extend>> Reset Password User (BENAR)
```

### **Class Diagram:**
✅ **Masalah yang Diperbaiki:**
- ❌ Hapus field `roasting_temperature` dan `roasting_time` (sesuai implementasi)
- ❌ Perbaiki typo dan konsistensi enum values
- ✅ Tambah missing relationships
- ✅ Tambah methods untuk setiap class
- ✅ Tambah notes penjelasan

**Perubahan:**
```
SEBELUM:
RoastingRules:
+ roasting_temperature: int  // SUDAH DIHAPUS
+ roasting_time: int         // SUDAH DIHAPUS

SESUDAH:
RoastingRules:
+ roasting_notes: text       // TAMBAHAN
+ is_active: tinyint(1)      // TAMBAHAN
```

## 📊 Struktur Diagram

### **Use Case Diagram:**
- **2 Actors:** User dan Admin
- **15 Use Cases:** 6 untuk User, 9 untuk Admin
- **3 Extend Relationships:** Fitur opsional
- **2 Include Relationships:** Fitur wajib

### **Class Diagram:**
- **7 Entities:** Users, UserPreferences, CoffeeTypes, RoastingRules, ProsesRoasting, RecommendationHistory, Recommendations
- **8 Relationships:** One-to-One, One-to-Many
- **Methods:** CRUD operations untuk setiap entity
- **Notes:** Penjelasan perubahan dan implementasi

## 🎯 Fitur Utama yang Ditampilkan

### **User Features:**
- ✅ Login/Logout
- ✅ Sistem Rekomendasi
- ✅ Riwayat Rekomendasi
- ✅ Atur Preferensi
- ✅ Lihat Katalog Kopi
- ✅ Lihat Detail Kopi
- ✅ Lihat Detail Proses Roasting

### **Admin Features:**
- ✅ Mengelola Users
- ✅ Mengelola Aturan Rekomendasi
- ✅ Mengelola Data Coffee
- ✅ Mengelola Proses Roasting
- ✅ Mengelola Riwayat Rekomendasi
- ✅ Lihat Statistik
- ✅ Reset Password User

## 📝 Catatan untuk Laporan Skripsi

### **Use Case Diagram:**
1. **Jelaskan alasan perubahan relationship** dari include ke extend
2. **Sebutkan bahwa diagram sudah disesuaikan** dengan implementasi aktual
3. **Highlight fitur utama** sistem rekomendasi

### **Class Diagram:**
1. **Jelaskan penghapusan roasting fields** sesuai implementasi
2. **Sebutkan rule-based system** dengan forward chaining
3. **Highlight normalisasi database** yang baik

## 🔄 Versi Control

| Versi | Tanggal | Perubahan |
|-------|---------|-----------|
| 1.0 | 20 Juli 2025 | Diagram awal dengan masalah |
| 2.0 | 20 Juli 2025 | Perbaikan relationship dan typo |
| 2.1 | 20 Juli 2025 | Penyesuaian dengan implementasi aktual |

## 📞 Support

Jika ada masalah dengan diagram atau perlu penyesuaian lebih lanjut, silakan hubungi developer.

---
**Note:** Diagram ini sudah disesuaikan dengan implementasi aktual sistem dan siap untuk digunakan dalam laporan skripsi. 