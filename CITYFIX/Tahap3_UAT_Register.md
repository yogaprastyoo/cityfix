# CityFix - UAT, Bug Tracker & Improvement Register (Tahap 3)

## Akun UAT (development lokal saja)

Dibuat oleh `php artisan db:seed` hanya saat `APP_ENV=local/testing` (`DevelopmentUserSeeder`).

| Role | Email | Password |
|---|---|---|
| Admin | admin@cityfix.local | admin123 |
| Verifier | verifier@cityfix.local | password |
| Technician | technician@cityfix.local | password |
| Reporter | reporter@cityfix.local | password |

## Format UAT (§16)

| ID | Modul | Skenario | Expected Result | Automated test | Status UAT |
|---|---|---|---|---|---|
| UAT-001 | Login | Login valid | Dashboard tampil | AuthenticationTest | [ ] |
| UAT-002 | Login | Password salah | Error tampil | AuthenticationTest | [ ] |
| UAT-003 | Report | Buat laporan | Data tersimpan | ReportTest | [ ] |
| UAT-004 | Photo | Upload JPG | Foto tersimpan | ReportTest | [ ] |
| UAT-005 | Role | Reporter akses Area | 403 | MasterDataTest | [ ] |
| UAT-006 | Assign | Assign Technician | PIC tersimpan | ReportWorkflowTest | [ ] |
| UAT-007 | Status | Start work | In Progress | ReportWorkflowTest | [ ] |
| UAT-008 | Complete | Upload hasil | Completed | ReportWorkflowTest | [ ] |
| UAT-009 | Timeline | Buka report | History tampil | ReportWorkflowTest | [ ] |
| UAT-010 | Dashboard | Report selesai | KPI berubah | DashboardTest | [ ] |

Kolom "Status UAT" diisi oleh pengguna nyata per role (§15): Reporter, Verifier/Sarpras, Technician, Admin.

## Checklist UAT per Role (§15)

- **Reporter:** Login, Buat laporan, Upload foto, Pilih area/kategori/urgency, Submit, Buka laporan, Cek progress, Logout
- **Verifier:** Login, Buka laporan baru, Verifikasi, Assign technician, Lihat histori, Monitoring laporan
- **Technician:** Login, Buka My Task, Buka detail, Update In Progress, Waiting Material bila perlu, Upload foto selesai, Update Completed
- **Admin:** Dashboard, Master Area, Master Category, Manage User, Semua laporan, Monitoring status, Review KPI dashboard

## Mobile UI Test (§11)

| Perangkat | Resolusi | Sidebar | Tombol kirim | Input foto | Dropdown | Tabel scroll | Gambar | Tracker | Status |
|---|---|---|---|---|---|---|---|---|---|
| Desktop | 1920 x 1080 | | | | | | | | [ ] |
| Laptop | 1366 x 768 | | | | | | | | [ ] |
| Tablet | 768 x 1024 | | | | | | | | [ ] |
| Mobile | 390 x 844 | | | | | | | | [ ] |

## Bug Tracker (§17.1)

Severity: Critical = keamanan/data loss/aplikasi tidak dapat digunakan; High = fitur utama gagal; Medium = masih usable tapi bermasalah; Low = cosmetic/UI.

| Bug ID | Modul | Problem | Severity | Status | Perbaikan | Regression test |
|---|---|---|---|---|---|---|
| BUG-001 | Login | Error message tidak tampil | Medium | Closed | `@error` + `invalid-feedback` di form login | AuthenticationTest |
| BUG-002 | Report | Foto >5 MB masih masuk | High | Closed | Rule `image\|max:5120` | ReportTest (photo larger than 5 MB) |
| BUG-003 | Role | Reporter dapat akses Area | Critical | Closed | Middleware `role:admin` pada route master data | MasterDataTest |
| BUG-004 | Mobile | Table overflow | Low | Closed | `table-responsive` + `text-nowrap` | Manual (Mobile UI Test) |
| BUG-005 | Prototype | Form laporan tampil di luar layout (`create.blade.php` terpotong) | High | Closed | Tag penutup + `@endsection` dilengkapi | ReportTest (create page) |

## Improvement Register (§17.2)

| IMP ID | Improvement | Priority | Phase | Status |
|---|---|---|---|---|
| IMP-001 | Filter laporan (status, area, urgency) | Medium | Tahap 3 | Done |
| IMP-002 | Search report ID | High | Tahap 3 | Done |
| IMP-003 | Mobile camera (`accept="image/*"` → pilihan kamera di smartphone) | High | Tahap 3 | Done |
| IMP-004 | SLA warning (KPI Overdue di dashboard) | Medium | Next | Sebagian (KPI dashboard) |
| IMP-005 | Email notification | Low | Next | Belum |

## Command Testing (§19)

```bash
php artisan test
php artisan test tests/Feature/ReportTest.php
php artisan test --filter=ReportTest
```

Automated test memakai database terpisah `cityfix_testing` (lihat `phpunit.xml`).
