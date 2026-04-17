# 🗂️ Data Persistence & Development Workflow

## ⚠️ IMPORTANT: How to Keep Your Trading Data Safe

Aplikasi ini menyimpan semua data trading Anda di database MySQL yang **persistent** (tersimpan di folder host `./db/data`).

### ✅ Data AKAN tetap ada jika:
```bash
docker-compose down
docker-compose up -d
```

### ❌ Data AKAN HILANG jika:
```bash
# JANGAN PERNAH GUNAKAN INI:
docker-compose down -v              # ← Hapus semua volumes (HILANG DATA!)
docker-compose down --volumes       # ← Sama dengan -v
```

---

## 📝 Workflow Development yang Aman

### First Setup (saat pertama kali)
Jalankan migration & seed **sekali saja** untuk setup database:
```bash
docker-compose up -d
docker exec newproject_php php artisan migrate:fresh --seed
```

Ini akan:
- Create semua tabel database
- Seed dummy data untuk testing (trades, daily limits, roles, users)
- Siap untuk Anda input data trading

### Development Selanjutnya ⭐ (JANGAN PAKAI migrate:fresh!)

**Jika Anda sudah input trading data sendiri, JANGAN PAKAI `migrate:fresh`!**

Gunakan ini jika ada migration baru:
```bash
docker exec newproject_php php artisan migrate
```

Atau jika hanya perlu clear cache:
```bash
docker exec newproject_php php artisan cache:clear
```

### Kontrol + C / Restart Container (AMAN ✓)
Data Anda tetap aman:
```bash
# Stop container
docker-compose down

# Start kembali - data masih ada!
docker-compose up -d
```

---

## 🔄 Jika Data Tidak Sengaja Hilang

### Option 1: Backup/Restore
Jika Anda pernah backup user data sebelum migrate:
```bash
# Lihat backup yang tersedia
ls -la backups/user-backups/

# Restore dari backup
bash scripts/restore-users.sh backups/user-backups/users_backup_20260417_202304.sql
```

### Option 2: Buat User Baru
Jika tidak ada backup, Anda bisa buat user baru:
1. Buka http://localhost (atau http://yourhost)
2. Click "Sign up"
3. Buat akun baru
4. Mulai input trading data lagi

### Option 3: Backup Sebelum Migrate
Jika Anda akan run `migrate:fresh --seed`, **backup user data dulu**:
```bash
bash scripts/backup-users.sh
# Sekarang aman untuk jalankan:
docker exec newproject_php php artisan migrate:fresh --seed
# Kemudian restore users:
bash scripts/restore-users.sh backups/user-backups/users_backup_XXXXXX.sql
```

---

## 📋 Checklist untuk Hindari Data Loss

- [ ] **JANGAN gunakan `docker-compose down -v`** (ini hapus semua data!)
- [ ] Gunakan `docker-compose down` saja (tanpa `-v`)
- [ ] **JANGAN pakai `migrate:fresh`** kecuali setup pertama kali
- [ ] Jika harus migrate, jalankan `backup-users.sh` dulu
- [ ] Restart container boleh, migration `migrate:fresh` jangan

---

## 🎯 Ringkas:

| Action | Data Safe? | Notes |
|--------|-----------|-------|
| `docker-compose down` | ✅ YES | Data tetap di host folder `./db/data` |
| `docker-compose up -d` | ✅ YES | Reload data dari host folder |
| `docker-compose down -v` | ❌ NO | Hapus semua volumes - DATA HILANG! |
| `migrate:fresh --seed` | ❌ NO | Drop semua tabel - user data hilang! Hanya gunakan first setup. |
| `migrate` (tanpa fresh) | ✅ YES | Aman, hanya jalankan migration baru |
| Edit & input trading | ✅ YES | Data langsung disimpan ke DB |

---

## 🛡️ Best Practices

1. **Jangan pakai `migrate:fresh` di development** setelah setup awal
2. **Sebelum jalankan seeded migration**, backup users dengan script:
   ```bash
   bash scripts/backup-users.sh
   ```
3. **Cek backup folder** regularly:
   ```bash
   ls -la backups/user-backups/
   ```
4. **Gunakan `docker-compose down` saja** saat stop (tanpa `-v`)

---

**Enjoy trading! 📊 Data Anda aman di aplikasi ini.**
