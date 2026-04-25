# Quick Deployment Setup Guide

## 📋 Checklist Trước Deploy

- [ ] Có server machine chạy Linux/Windows với Node.js + MySQL
- [ ] GitHub account + đã push code
- [ ] Domain hoặc IP của server
- [ ] SSH access hoặc RDP access

---

## 🚀 Quick Setup (5 minutes)

### Trên máy tính của bạn (LOCAL):

```bash
# 1. Login vào server via SSH
ssh user@your-server-ip

# Hoặc RDP nếu Windows Server
```

### Trên SERVER:

```bash
# 2. Clone code
cd /home/user
git clone https://github.com/your-username/QLDUAN.git
cd QLDUAN

# 3. Setup environment
cp SERVER_ENV_TEMPLATE.env server/.env

# Edit .env - thay đổi DATABASE_URL, JWT_SECRET, etc.
nano server/.env

# 4. Install PM2 globally
npm install -g pm2

# 5. Run deployment script
chmod +x deploy.sh
./deploy.sh

# OR for Windows
# deploy.bat
```

---

## ✅ Verify Deployment

```bash
# Check if running
pm2 status

# View logs
pm2 logs aperlex-backend

# Test API
curl http://localhost:3000/api/health

# View specific logs
pm2 logs aperlex-backend --lines 100
```

---

## 🔗 Access Application

- **Frontend**: `http://your-server-ip:5173` (dev) 
- **API**: `http://your-server-ip:3000/api`
- **WebSocket**: `ws://your-server-ip:3000`

---

## 📝 Common Issues

### Issue: Port 3000 already in use
```bash
# Find process
lsof -i :3000

# Kill it
kill -9 <PID>
```

### Issue: Database connection error
```bash
# Check MySQL is running
sudo systemctl status mysql

# Test connection
mysql -u root -p
```

### Issue: npm install fails
```bash
# Clear cache
npm cache clean --force

# Try again
npm install
```

---

## 🔄 Auto-restart on server reboot

```bash
pm2 startup
pm2 save
```

---

## 📊 Monitor Performance

```bash
# Real-time monitor
pm2 monit

# Memory usage
pm2 status

# Kill and restart
pm2 restart aperlex-backend
```

---

## 🚀 Update code (Pull latest)

```bash
git pull origin main
./deploy.sh
```

---

## 🔐 Setup SSL (Optional but Recommended)

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d your-domain.com

# Auto-renew
sudo certbot renew --dry-run
```

---

## 📞 Need Help?

Check logs:
```bash
pm2 logs
tail -f /var/log/syslog
```

---

**Deploy successfully? Congratulations! 🎉**
