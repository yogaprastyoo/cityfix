# CityFix - Release Notes

Format versi: `MAJOR.MINOR.PATCH` (Tahap 4 §29). Setiap release catat tanggal, perubahan, migration, bug fix, risiko, dan hasil smoke test.

## v1.0.0 - Initial CityFix production (draft, belum dirilis)

**Fitur**
- Login/logout, 4 role (admin, verifier, technician, reporter), menu sesuai role
- Buat laporan + foto kerusakan (maks 5 MB), nomor laporan `CF-YYYY-000001`
- Daftar laporan dengan search nomor, filter status/area/urgensi, pagination
- Detail laporan + progress tracker (histori status, catatan, foto bukti)
- Verifikasi & assign teknisi, update status dengan validasi transisi, foto bukti wajib saat Selesai
- Dashboard KPI realtime + SLA overdue, Area Monitoring
- Master Area, Kategori, User (admin); password sementara wajib diganti saat login pertama
- Command `php artisan cityfix:create-admin`

**Migration:** users.role, users.must_change_password, areas, categories, reports, report_histories

**Risiko:** belum ada notifikasi email; SLA Compliance & Repeat Issue belum dihitung.

**Smoke test:** - (isi setelah deployment)
