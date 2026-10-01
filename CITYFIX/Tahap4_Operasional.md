# CityFix - Panduan Operasional & Go-Live (Tahap 4)

Berkas pendukung di folder `CITYFIX/deploy/`:

| File | Isi |
|---|---|
| `.env.production.example` | Template `.env` production (APP_DEBUG=false, cookie aman, log harian) |
| `nginx-cityfix.conf` | Virtual host Nginx, document root `public/` |
| `deploy.sh` | Urutan deployment 12 langkah (§20) |
| `backup.sh` | Backup database + foto dengan retensi (§18) |

## Setup Production Awal

```bash
# 1. Database & user khusus (bukan root)
CREATE DATABASE cityfix_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cityfix_app'@'localhost' IDENTIFIED BY 'PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON cityfix_production.* TO 'cityfix_app'@'localhost';
FLUSH PRIVILEGES;

# 2. Source + dependency
cd /var/www && git clone <repository-cityfix> cityfix && cd cityfix
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Environment
cp CITYFIX/deploy/.env.production.example .env   # lalu isi nilai asli
php artisan key:generate                           # BACKUP APP_KEY di tempat aman

# 4. Database + master data (idempotent, tidak membuat akun dummy di production)
php artisan migrate --force
php artisan db:seed --force

# 5. Admin pertama (password kuat, wajib diganti saat login pertama)
php artisan cityfix:create-admin

# 6. Storage & permission
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 7. Optimize + health check
php artisan optimize
curl -I https://cityfix.example.sch.id/up
```

User pilot dibuat admin lewat menu **Master Data → User**. Setiap user baru (atau yang password-nya direset admin) wajib mengganti password saat login pertama.

## Smoke Test Setelah Deployment (§21)

| No | Test | Expected | Hasil |
|---|---|---|---|
| 1 | GET /up | HTTP 200 | [ ] |
| 2 | Login admin | Berhasil | [ ] |
| 3 | Dashboard | Tampil tanpa error | [ ] |
| 4 | Buat test report | Tersimpan | [ ] |
| 5 | Upload foto | Foto dapat dibuka | [ ] |
| 6 | Verifier assign | Berhasil | [ ] |
| 7 | Technician update | History berubah | [ ] |
| 8 | Completion photo | Tersimpan | [ ] |
| 9 | Reporter review | Hanya akses laporan yang berhak | [ ] |
| 10 | Logout | Session selesai | [ ] |

## Security Final Review (§22)

- [ ] APP_DEBUG=false
- [ ] `/.env`, `/storage/logs/laravel.log`, `/composer.json` tidak dapat diakses publik
- [ ] Document root mengarah ke `public/`
- [ ] HTTPS aktif + redirect HTTP → HTTPS, `SESSION_SECURE_COOKIE=true`
- [ ] Password database bukan root dan kuat
- [x] Role/middleware/policy aktif (MasterDataTest, ReportAuthorizationTest)
- [x] Reporter tidak bisa melihat laporan orang lain
- [x] Technician hanya task assigned
- [x] Upload hanya gambar, maksimal 5 MB
- [x] Completion photo wajib
- [ ] Tidak ada akun dummy/password default (DevelopmentUserSeeder hanya jalan di local/testing)
- [x] Master data hanya admin
- [ ] Database backup berjalan
- [ ] Log tidak publik

## Restore Test (§19)

```bash
mysql -u root -p -e "CREATE DATABASE cityfix_restore_test"
gunzip -c /var/backups/cityfix/cityfix_YYYYMMDD_HHMM.sql.gz | mysql -u root -p cityfix_restore_test
mysql -u root -p cityfix_restore_test -e "SELECT COUNT(*) FROM users; SELECT COUNT(*) FROM reports; SELECT COUNT(*) FROM report_histories;"
tar -tzf /var/backups/cityfix/cityfix_storage_YYYYMMDD_HHMM.tar.gz | head
```

Retensi yang disarankan: database harian 7 daily + 4 weekly + 6 monthly; foto sama atau lebih panjang; snapshot sebelum setiap release.

## SOP Operasional (§24)

**Reporter:** Login → Foto masalah → Pilih area → Kategori → Urgency → Isi deskripsi → Submit → Pantau progress (menu *Laporan Saya*) → Review hasil & foto bukti.

**Verifier (Sarpras):** Review laporan baru (filter status *Dilaporkan*) → Cek validitas/urgency → *Verifikasi & Assign* teknisi, atau ubah status ke *Ditolak* dengan catatan → Pantau SLA (dashboard *Overdue*, menu *Area Monitoring*) → Eskalasi jika overdue.

**Technician:** Buka *My Task* → Detail → Status *Dalam Penanganan* → Update catatan → *Menunggu Material* bila perlu → Selesaikan → Upload foto bukti → Status *Selesai*.

**Admin:** Kelola user & master data → Review dashboard → Audit history laporan → Cek data abnormal → Koordinasi backup/monitoring.

## Target SLA

| Urgensi | Target | Dihitung Overdue di dashboard |
|---|---|---|
| Darurat | < 2 jam | Ya |
| Tinggi | < 24 jam | Ya |
| Sedang | 2-3 hari (72 jam) | Ya |
| Rendah | Pemeliharaan rutin | Tidak |

## Go-Live Strategy (§25)

| Fase | Durasi | Scope |
|---|---|---|
| Pilot | 1-2 minggu | 1-2 area + user terbatas (Admin 1-2, Verifier 2-3, Technician 3-10, Reporter 10-30) |
| Controlled Rollout | 2-4 minggu | Beberapa gedung/asrama |
| Full Go-Live | Setelah KPI stabil | Seluruh area |

Area yang belum masuk scope pilot dapat dinonaktifkan di **Master Data → Area** agar tidak muncul di form laporan.

## KPI Pasca Go-Live (§26)

Tersedia di dashboard: Total Report, Dilaporkan, Dalam Penanganan, Selesai, Emergency Open, Waiting Material, Rata-rata Penyelesaian (reported_at → completed_at), Completion Rate (completed / non-rejected), Overdue. Belum tersedia: SLA Compliance, Repeat Issue.

## Monitoring Minggu Pertama (§27)

Review harian: aplikasi dapat diakses (`/up`)? error 500 di `storage/logs`? upload foto gagal? user salah role? report belum ter-assign (filter *Dilaporkan*)? report melewati SLA (KPI *Overdue*)? storage tumbuh wajar (`du -sh storage/app/public`)? backup berhasil? pengguna memahami status? bug baru dari mobile?

## Rollback Plan (§28)

1. `php artisan down`
2. Restore source code release sebelumnya (`git checkout <tag-sebelumnya>`)
3. Restore database hanya jika migration/data berubah dan rollback aman
4. Restore storage bila diperlukan
5. `php artisan optimize:clear`
6. `php artisan optimize`
7. Smoke test
8. `php artisan up`

Migration destruktif harus sangat hati-hati: rollback source code tidak otomatis mengembalikan data. Jangan pernah `migrate:fresh` di production.
