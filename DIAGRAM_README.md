# Diagram Sistem Triak Coffee & Roaster

Dokumen ini berisi diagram PlantUML yang menjelaskan seluruh arsitektur dan workflow sistem rekomendasi kopi Triak Coffee & Roaster.

## 📋 Daftar Diagram

### 1. **System Activity Diagram** (`system_activity_diagram.puml`)
Diagram activity utama yang menunjukkan seluruh alur kerja sistem dari login hingga logout, termasuk:
- **Authentication Workflow** (Register/Login/Logout)
- **User Dashboard Workflow** (Rekomendasi, Riwayat, Preferensi, Favorit)
- **Admin Dashboard Workflow** (Kelola Kopi, Aturan, User, Roasting, Analytics)
- **Rule-Based Recommendation System** (20 aturan rekomendasi)
- **Database Structure** (5 tabel utama)

### 2. **Recommendation Workflow Diagram** (`recommendation_workflow_diagram.puml`)
Diagram detail untuk proses rekomendasi kopi:
- **Input Preferensi Pelanggan** (4 atribut utama)
- **Proses Rule-Based System** (Full match, Partial match, Default)
- **Pencarian Kopi yang Sesuai** (Match score algorithm)
- **Panduan Roasting** (Parameter roasting detail)
- **Simpan Rekomendasi** (Database operations)
- **Rating & Review** (User feedback)

### 3. **Admin Workflow Diagram** (`admin_workflow_diagram.puml`)
Diagram workflow khusus untuk admin:
- **Admin Dashboard Overview** (Statistik dan menu)
- **Kelola Kopi Workflow** (CRUD operations)
- **Kelola Aturan Workflow** (20 aturan rekomendasi)
- **Kelola User Workflow** (User management)
- **Kelola Proses Roasting Workflow** (Roasting parameters)
- **Analytics Workflow** (Reports dan charts)

### 4. **Database Structure Diagram** (`database_structure_diagram.puml`)
Entity Relationship Diagram (ERD) yang menunjukkan:
- **5 Tabel Database** dengan relasi
- **Struktur Field** lengkap dengan tipe data
- **Foreign Key Relationships**
- **Sample Data** dan default values
- **Rule-Based System** documentation

## 🎯 Fitur Utama Sistem

### **Untuk User/Kasir:**
- ✅ Browse coffee catalog dengan detail lengkap
- ✅ Set personal coffee preferences
- ✅ Get personalized coffee recommendations (20 rules)
- ✅ Rate dan review kopi
- ✅ Manage favorite coffees
- ✅ User dashboard dengan statistics
- ✅ Riwayat rekomendasi

### **Untuk Admin/Manager:**
- ✅ Complete admin dashboard
- ✅ Manage coffee inventory (CRUD)
- ✅ Manage 20 recommendation rules
- ✅ Manage users
- ✅ Manage roasting processes
- ✅ View analytics dan statistics
- ✅ Monitor reviews dan ratings

## 🔧 Technology Stack

- **Backend**: PHP dengan MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL dengan PDO
- **Authentication**: Session-based dengan password hashing
- **Design**: Responsive design dengan coffee-themed styling

## 📊 Rule-Based Recommendation System

Sistem menggunakan **20 aturan rekomendasi** yang telah ditetapkan:

| Rule | Jenis Kopi | Metode | Profil Rasa | Proses | Roast Level |
|------|------------|--------|-------------|--------|-------------|
| 1 | Arabika | V60 | Fruity | Washed | Light |
| 2 | Arabika | French Press | Chocolate | Honey | Medium |
| 3 | Arabika | Espresso | Nutty | Natural | Medium |
| 4 | Robusta | Tubruk | Earthy | Natural | Dark |
| 5 | Robusta | Cold Brew | Fruity | Honey | Medium |
| ... | ... | ... | ... | ... | ... |

**Algoritma Matching:**
1. **Full Match** (4 atribut) → Terapkan rule lengkap
2. **Partial Match** (3 atribut) → Terapkan rule dengan partial match
3. **Default** → Medium roast

## 🗄️ Database Structure

### **Tabel Utama:**
1. **users** - User accounts (admin/user roles)
2. **coffee_types** - Coffee varieties dan details
3. **user_preferences** - User taste preferences
4. **recommendations** - User ratings dan reviews
5. **proses_roasting** - Roasting process parameters

### **Relasi Database:**
- `users` → `user_preferences` (1:1)
- `users` → `recommendations` (1:N)
- `coffee_types` → `recommendations` (1:N)

## 🚀 Cara Menggunakan Diagram

### **Untuk Developer:**
1. Install PlantUML extension di IDE
2. Buka file `.puml`
3. Preview diagram secara real-time
4. Export ke PNG/SVG jika diperlukan

### **Untuk Documentation:**
1. Diagram dapat digunakan untuk:
   - System documentation
   - User manual
   - Training materials
   - System analysis

## 📈 Workflow Overview

### **User Journey:**
```
Login → User Dashboard → Input Preferensi → Rule-Based Processing → 
Rekomendasi Kopi → Panduan Roasting → Rating & Review → Riwayat
```

### **Admin Journey:**
```
Login → Admin Dashboard → Analytics → Manage Data → 
Kelola Kopi/Aturan/User/Roasting → Logout
```

## 🔐 Security Features

- ✅ Password hashing dengan PASSWORD_DEFAULT
- ✅ Session-based authentication
- ✅ SQL injection prevention dengan PDO
- ✅ Role-based access control (admin/user)
- ✅ Input validation dan sanitization

## 📱 Responsive Design

- ✅ Mobile-friendly interface
- ✅ Coffee-themed color scheme
- ✅ Modern UI/UX design
- ✅ Font Awesome icons
- ✅ Bootstrap-like grid system

## 🎨 Color Scheme

- **Primary**: #a86b3c (Coffee Brown)
- **Secondary**: #7b4a1e (Dark Brown)
- **Background**: #faf8f6 (Light Cream)
- **Text**: #23190f (Dark Brown)
- **Accent**: #f7b731 (Golden Yellow)

## 📝 Notes

- Sistem ini siap untuk deployment di environment PHP
- Default admin account: `admin` / `admin123`
- Database akan dibuat otomatis saat pertama kali diakses
- Sample data coffee akan di-insert otomatis
- 20 aturan rekomendasi sudah predefined dalam sistem

---

**Dibuat dengan ❤️ untuk Triak Coffee & Roaster** 