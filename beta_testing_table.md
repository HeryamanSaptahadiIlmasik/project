# Tabel Beta Testing - Sistem Rekomendasi Kopi

## Informasi Tester

| No | Nama Tester | Email | Role | Usia | Pengalaman dengan Aplikasi Web | Tanggal Testing |
|---|---|---|---|---|---|---|
| 1 | | | | | | |
| 2 | | | | | | |
| 3 | | | | | | |
| 4 | | | | | | |
| 5 | | | | | | |

## 1. Testing User Experience (UX)

| No | Aspek | Kriteria | Rating (1-5) | Komentar | Saran Perbaikan |
|---|---|---|---|---|---|
| 1 | Kemudahan Navigasi | Menu mudah ditemukan dan dipahami | | | |
| 2 | Desain Interface | Tampilan menarik dan modern | | | |
| 3 | Responsivitas | Berfungsi baik di berbagai device | | | |
| 4 | Loading Time | Halaman cepat dimuat | | | |
| 5 | Konsistensi UI | Elemen UI konsisten di semua halaman | | | |

## 2. Testing Fitur Utama

| No | Fitur                        | Fungsi                                      | Rating (1-5) | Komentar | Bug/Issue |
|----|------------------------------|---------------------------------------------|--------------|----------|-----------|
| 1  | Login                        | Masuk ke sistem                             |              |          |           |
| 2  | Dashboard                    | Halaman utama setelah login                 |              |          |           |
| 3  | Katalog Kopi                 | Melihat daftar kopi                         |              |          |           |
| 4  | Lihat Detail Kopi            | Melihat detail kopi                         |              |          |           |
| 5  | Sistem Rekomendasi           | Mendapat rekomendasi kopi                   |              |          |           |
| 6  | Lihat Proses Roasting        | Melihat detail proses roasting              |              |          |           |
| 7  | Riwayat                      | Melihat riwayat rekomendasi                 |              |          |           |
| 8  | Preferensi                   | Mengatur preferensi kopi                    |              |          |           |
| 9  | Logout                       | Keluar dari sistem                          |              |          |           |
| 10 | Kelola Data Kopi (Admin)     | Tambah, edit, hapus data kopi               |              |          |           |
| 11 | Kelola Aturan Rekomendasi    | Tambah, edit, hapus aturan rekomendasi      |              |          |           |
| 12 | Kelola Proses Roasting       | Tambah, edit, hapus proses roasting         |              |          |           |
| 13 | Kelola User (Admin)          | Lihat, reset password, hapus user           |              |          |           |
| 14 | Statistik & Riwayat (Admin)  | Melihat statistik dan riwayat rekomendasi   |              |          |           |
| 15 | Error Handling               | Penanganan error dan validasi input         |              |          |           |

## 3. Testing Sistem Rekomendasi

| No | Skenario Testing | Input Preferensi | Rekomendasi yang Dihasilkan | Akurasi (1-5) | Komentar |
|---|---|---|---|---|---|
| 1 | Preferensi Asam + Light Body | Rasa: Asam<br>Body: Light<br>Roast: Light | | | |
| 2 | Preferensi Pahit + Full Body | Rasa: Pahit<br>Body: Full<br>Roast: Dark | | | |
| 3 | Preferensi Seimbang | Rasa: Seimbang<br>Body: Medium<br>Roast: Medium | | | |
| 4 | Preferensi Campuran | Kombinasi berbagai preferensi | | | |
| 5 | Preferensi Kosong | Tidak memilih preferensi | | | |

## 4. Testing Performa

| No | Metrik | Hasil | Target | Status | Keterangan |
|---|---|---|---|---|---|
| 1 | Waktu Loading Halaman Utama | | < 3 detik | | |
| 2 | Waktu Loading Katalog | | < 5 detik | | |
| 3 | Waktu Generate Rekomendasi | | < 10 detik | | |
| 4 | Responsivitas UI | | < 1 detik | | |
| 5 | Konsumsi Memory | | < 100MB | | |

## 5. Testing Kompatibilitas Browser

| No | Browser | Versi | Status | Issue yang Ditemukan | Keterangan |
|---|---|---|---|---|---|
| 1 | Chrome | Latest | | | |
| 2 | Firefox | Latest | | | |
| 3 | Safari | Latest | | | |
| 4 | Edge | Latest | | | |
| 5 | Mobile Browser | Chrome Mobile | | | |

## 6. Testing Device Compatibility

| No | Device | OS | Screen Size | Status | Issue |
|---|---|---|---|---|---|
| 1 | Desktop | Windows 10 | 1920x1080 | | |
| 2 | Laptop | macOS | 1366x768 | | |
| 3 | Tablet | iPadOS | 1024x768 | | |
| 4 | Smartphone | Android | 360x640 | | |
| 5 | Smartphone | iOS | 375x667 | | |

## 7. Testing Security

| No | Aspek Keamanan | Test Case | Status | Vulnerability | Severity |
|---|---|---|---|---|---|
| 1 | SQL Injection | Input karakter khusus | | | |
| 2 | XSS | Input script HTML/JS | | | |
| 3 | Session Management | Akses tanpa login | | | |
| 4 | Password Security | Password lemah | | | |
| 5 | File Upload | Upload file berbahaya | | | |

## 8. Testing Data Integrity

| No | Aspek | Test Case | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| 1 | Simpan Preferensi | Input preferensi user | Data tersimpan dengan benar | | |
| 2 | Simpan Rekomendasi | Simpan hasil rekomendasi | Data tersimpan dengan benar | | |
| 3 | Update Data | Edit data user/kopi | Data terupdate dengan benar | | |
| 4 | Delete Data | Hapus data | Data terhapus dengan benar | | |
| 5 | Backup Data | Backup database | Data dapat dipulihkan | | |

## 9. Testing Error Handling

| No | Error Scenario | Expected Behavior | Actual Behavior | Status | Priority |
|---|---|---|---|---|---|
| 1 | Koneksi Database Error | Pesan error yang informatif | | | |
| 2 | File Not Found | Halaman 404 yang proper | | | |
| 3 | Server Error | Halaman error yang informatif | | | |
| 4 | Invalid Input | Validasi input yang jelas | | | |
| 5 | Session Timeout | Redirect ke login | | | |

## 10. Feedback dan Saran Umum

| No | Kategori | Feedback | Priority | Status | Action Plan |
|---|---|---|---|---|---|---|
| 1 | UI/UX | | | | |
| 2 | Functionality | | | | |
| 3 | Performance | | | | |
| 4 | Security | | | | |
| 5 | Documentation | | | | |

## 11. Overall Assessment

| No | Aspek | Rating (1-5) | Komentar | Rekomendasi |
|---|---|---|---|---|
| 1 | Overall Satisfaction | | | |
| 2 | Ease of Use | | | |
| 3 | Feature Completeness | | | |
| 4 | Performance | | | |
| 5 | Would Recommend | | | |

## 12. Bug Report Summary

| No | Bug ID | Description | Severity | Status | Assigned To | Due Date |
|---|---|---|---|---|---|---|
| 1 | BUG-001 | | | | | |
| 2 | BUG-002 | | | | | |
| 3 | BUG-003 | | | | | |
| 4 | BUG-004 | | | | | |
| 5 | BUG-005 | | | | | |

---
**Keterangan Rating:**
- 1: Sangat Buruk
- 2: Buruk
- 3: Cukup
- 4: Baik
- 5: Sangat Baik

**Keterangan Severity:**
- Critical: Aplikasi tidak bisa digunakan
- High: Fitur utama tidak berfungsi
- Medium: Fitur sekunder bermasalah
- Low: Masalah kosmetik/minor 