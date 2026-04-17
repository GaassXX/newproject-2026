# 🚀 Trading Management System - Quick Start

Professional Trading Journal & Risk Management Platform.

## 📖 Documentation

| Document | Purpose |
|----------|---------|
| **[DATA_PERSISTENCE.md](DATA_PERSISTENCE.md)** | ⭐ **READ THIS FIRST** - How to keep your trading data safe |
| [BLADE_VIEWS_SETUP.md](BLADE_VIEWS_SETUP.md) | UI/View components & layout structure |
| [BLADE_VIEWS_DOCUMENTATION.md](BLADE_VIEWS_DOCUMENTATION.md) | Feature documentation |

---

## ⚡ Quick Start

### Prerequisites
- Docker & Docker Compose
- Linux/Mac (or WSL on Windows)

### Setup (First Time Only)

```bash
# 1. Start containers
docker-compose up -d

# 2. Run migrations & seed (ONLY FIRST TIME!)
docker exec newproject_php php artisan migrate:fresh --seed

# 3. Open browser
# Visit: http://localhost
```

### Default Login Credentials
- **Email:** admin@admin.com
- **Password:** password

### Default Users
- Admin: admin@admin.com (super-admin role)
- Trader: user@admin.com (trader role)

---

## 🛑 IMPORTANT: Data Persistence

**Your trading data is safe!** Database is persistent via host bind-mount.

```bash
# Safe - data preserved ✅
docker-compose down
docker-compose up -d

# DANGER - will delete all data ❌
docker-compose down -v       # Don't use this!
```

**⚠️ For more details on keeping data safe, read [DATA_PERSISTENCE.md](DATA_PERSISTENCE.md)**

---

## 📱 Features

### Dashboard
- Daily loss tracking
- Trading summary & profit/loss recap
- Signals status (MT5 integration)

### Trading Log
- Record all trades (forex, crypto, stocks)
- Entry/exit prices, stop loss, take profit
- Calculate PNL automatically
- Filter by instrument & status
- Real-time loss limit checking

### Daily Loss Limits
- Set max loss per day (USD/IDR)
- Real-time conversion & display
- Auto-lock trading when reached
- Historical tracking

### Admin Panel
- User management & role assignment
- Role-based permissions (super-admin, admin, trader, viewer)
- Signal tracking (MT5 EA integration)
- Logs & activity tracking

### Trading Signals (MT5 Integration)
- REST API for automated TP/SL execution
- Example MQL5 EA included (docs/mt5_ea_http_signal.mq5)
- Webhook callbacks for execution confirmation
- Token-based authentication

---

## 🐳 Common Commands

```bash
# Start containers
docker-compose up -d

# Stop containers (data safe)
docker-compose down

# View logs
docker-compose logs -f php

# Enter PHP container
docker exec -it newproject_php bash

# Run Laravel artisan
docker exec newproject_php php artisan <command>

# Reset database (WARNING: deletes user data!)
docker exec newproject_php php artisan migrate:fresh --seed
```

---

## 🔐 Development Workflow

### Daily Development (SAFE)
```bash
# If you only modified code files - just restart containers
docker-compose restart

# If there's a NEW migration file to run
docker exec newproject_php php artisan migrate
```

### CAREFUL - Only if Needed
```bash
# Backup user data FIRST
bash scripts/backup-users.sh

# Then reset database
docker exec newproject_php php artisan migrate:fresh --seed

# Restore users if needed
bash scripts/restore-users.sh backups/user-backups/users_backup_XXXXXX.sql
```

---

## 📊 Database Schema

Key tables:
- `users` - User accounts & roles
- `trades` - Trading records
- `daily_limits` - Daily loss limits per user
- `signals` - MT5 signals & execution tracking
- `roles` / `permissions` - Role-based access control

---

## 🚀 Next Steps

1. **Read [DATA_PERSISTENCE.md](DATA_PERSISTENCE.md)** to understand data safety
2. **Login** with admin@admin.com / password
3. **Set daily loss limit** in Daily Limits section
4. **Create first trade** in Trades section
5. **Check dashboard** for summary

---

## 🐛 Troubleshooting

**Containers won't start:**
```bash
docker-compose restart
docker-compose logs
```

**Database connection error:**
```bash
# Check if DB is healthy
docker-compose ps

# Rebuild containers
docker-compose down
docker-compose up -d
docker exec newproject_php php artisan migrate:fresh --seed
```

**Port already in use (80 or 443):**
Edit `docker-compose.yml` and change port mappings.

---

## 📝 Notes

- All data persisted to `./db/data/` on host
- Never use `docker-compose down -v` in production!
- Always backup before running `migrate:fresh`
- Use role system for multi-user access control

---

**Built with ❤️ for traders who want discipline in their trading journal.**
