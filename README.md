# TransactionAPP

> Aplikasi manajemen transaksi keuangan berbasis web.

## 🚀 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8 |
| Frontend | Bootstrap 5 |
| Database | MySQL |
| Arsitektur | MVC + Layered |

## 📁 Struktur Proyek

```
TransactionAPP/
├── public/          # Document root
├── src/             # Controllers, Services, Models, Middleware, Core
├── views/           # UI templates (layouts, auth, users, dashboard)
├── migrations/      # SQL migration files
├── seeders/         # Database seeder files
└── config/          # Konfigurasi aplikasi
```

## 🔐 Fase 1 — Auth System

| Fitur | Status |
|-------|--------|
| Login / Logout | ✅ Done |
| Session Management | ✅ Done |
| RBAC (Role & Permission) | ✅ Done |
| CRUD User (via Sidebar + Bootstrap Table) | ✅ Done |

> ❌ Tidak ada halaman register publik. User dibuat oleh admin dari dalam aplikasi.

## 🚀 Quick Start

1. **Buat database** `transactionapp` di MySQL
2. **Konfigurasi koneksi** di `src/config/database.php`
3. **Jalankan migration & seeder:**
   ```bash
   php public/migrate.php
   ```
4. **Buka browser:** `http://localhost/TransactionAPP`
5. **Login sebagai admin:** `admin@example.com` / `admin123`

## 📅 Changelog

| Tanggal | Fase | Deskripsi |
|---------|------|-----------|
| 2026-06-25 | Fase 1 | Implementasi Auth System + CRUD User (complete) |

---

> 📋 Detail rencana pengembangan: [issue.md](issue.md)
