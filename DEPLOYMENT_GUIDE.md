# 📦 Hướng Dẫn Deploy

## 📊 So Sánh 3 Giải Pháp Deployment

| Tiêu chí | **Self-Hosted (Máy của bạn)** | **Railway/Render** | **Vercel Frontend + Backend khác** |
|---------|-----|-----|-----|
| **Chi phí** | Miễn phí (máy có sẵn) | $5-20/tháng | Miễn phí frontend + backend khác |
| **Database** | MySQL hiện tại | Hỗ trợ (Postgres/MySQL) | Cần database riêng |
| **Độ khó** | Trung bình | Dễ | Trung bình |
| **Hiệu suất** | Phụ thuộc máy | Tốt | Tốt |
| **Kiểm soát** | Toàn bộ | Hạn chế | Hạn chế |
| **Setup time** | 30 phút | 15 phút | 20 phút |

---

## 🏠 **GIẢI PHÁP 1: Self-Hosted (Khuyên Nghị)**

### Ưu điểm:
- ✅ Miễn phí hoàn toàn (máy bạn có sẵn)
- ✅ Toàn quyền kiểm soát
- ✅ DB MySQL hiện tại không cần thay đổi
- ✅ Real-time Socket.io hoạt động tốt
- ✅ Không giới hạn resource

### Cách setup trên server machine:

#### **Bước 1: Clone code**
```bash
# Trên server machine của bạn
cd /home/user
git clone https://github.com/your-username/QLDUAN.git
cd QLDUAN
```

#### **Bước 2: Setup backend (Node.js)**
```bash
cd server

# Copy .env
cp .env.example .env

# Edit .env - cấu hình database
# DATABASE_URL=mysql://user:password@localhost:3306/qhorizonpm_db
# JWT_SECRET=your_secret_key

npm install
npm run prisma:migrate:deploy
npm run build
npm run start   # Chạy production
```

#### **Bước 3: Setup PM2 (Keep server running)**
```bash
npm install -g pm2

# Chạy backend với PM2
cd server
pm2 start "npm run start" --name "aperlex-backend"

# Auto restart khi server reboot
pm2 startup
pm2 save
```

#### **Bước 4: Setup frontend (Vue.js)**
```bash
# Quay lại root
cd ..

# Build frontend
npm install
npm run build

# Serve với Nginx hoặc Apache
# Hoặc chạy dev: npm run dev
```

#### **Bước 5: Setup Nginx (Reverse Proxy)**

```nginx
# /etc/nginx/sites-available/aperlex

upstream backend {
    server localhost:3000;
}

server {
    listen 80;
    server_name your-domain.com;

    # Frontend
    location / {
        root /home/user/QLDUAN;
        try_files $uri $uri/ /index.html;
    }

    # Backend API
    location /api/ {
        proxy_pass http://backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # WebSocket
    location /socket.io/ {
        proxy_pass http://backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
    }
}
```

#### **Bước 6: SSL with Let's Encrypt**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

---

## 🚀 **GIẢI PHÁP 2: Railway / Render (Dễ nhất)**

### Railway (Khuyên nghị cho backend)

#### Bước 1: Push code to GitHub
```bash
git add .
git commit -m "Ready for deployment"
git push origin main
```

#### Bước 2: Connect Railway
1. Vào https://railway.app
2. Đăng nhập bằng GitHub
3. New Project → GitHub Repo
4. Chọn `QLDUAN` repo
5. Railway tự detect Node.js project

#### Bước 3: Cấu hình Environment Variables
```
DATABASE_URL=mysql://user:password@host:port/db
JWT_SECRET=your_secret
NODE_ENV=production
PORT=3000
```

#### Bước 4: Deploy
```bash
# Railway CLI
npm install -g @railway/cli
railway link
railway up
```

**Chi phí:** ~$5-10/tháng

---

## ⚡ **GIẢI PHÁP 3: Vercel (Frontend) + Railway (Backend)**

### Phù hợp nếu bạn muốn:
- Frontend trên Vercel (siêu nhanh, CDN global)
- Backend trên Railway (database, WebSocket)

#### Frontend on Vercel:
```bash
# Bước 1: Deploy frontend
npm run build
vercel --prod

# .env.production:
VITE_API_URL=https://your-api.railway.app
```

#### Backend on Railway:
```bash
# Deployment như Giải Pháp 2
```

**Chi phí:** Vercel miễn phí + Railway $5-10/tháng

---

## 📋 So Sánh Nhanh

### Self-Hosted ✅ (KHUYÊN NGHỊ)
```
Setup:    30 phút (1 lần)
Bảo trì: 10 phút/tháng
Chi phí:  $0
Hiệu năng: ⭐⭐⭐⭐⭐
```

### Railway
```
Setup:    10 phút (cực dễ)
Bảo trì: Tự động
Chi phí:  $5-10/tháng
Hiệu năng: ⭐⭐⭐⭐
```

### Vercel + Railway
```
Setup:    15 phút
Bảo trì: Tự động
Chi phí:  $5-10/tháng
Hiệu năng: ⭐⭐⭐⭐⭐
```

---

## 🎯 Khuyến Nghị Của Tôi

### **Nên chọn: SELF-HOSTED** ✅

**Lý do:**
1. Bạn có server sẵn → Tiết kiệm tiền
2. MySQL hiện tại → Không cần migrate
3. Toàn quyền kiểm soát → Dễ debug
4. Real-time Socket.io → Hoạt động tốt nhất
5. Không giới hạn requests/bandwidth

**Setup script for self-hosted:**

```bash
#!/bin/bash
# deploy.sh - Run this on your server

cd /home/user/QLDUAN

# Stop old process
pm2 stop aperlex-backend

# Pull latest code
git pull origin main

# Install dependencies
cd server
npm install
npm run prisma:migrate:deploy

# Build
npm run build

# Start with PM2
pm2 start "npm run start" --name "aperlex-backend"

# Frontend
cd ..
npm install
npm run build

echo "✅ Deployment complete!"
```

---

## 📱 Testing Sau Deploy

```bash
# Test API
curl http://localhost:3000/api/health

# Test WebSocket
wscat -c ws://localhost:3000/socket.io/?EIO=4&transport=websocket

# View logs
pm2 logs aperlex-backend

# Monitor
pm2 monit
```

---

## 🔗 Useful Links

- [Railway Docs](https://docs.railway.app)
- [Render Docs](https://render.com/docs)
- [Vercel Docs](https://vercel.com/docs)
- [Nginx Config](https://nginx.org/en/docs/)
- [PM2 Guide](https://pm2.keymetrics.io/docs/usage/quick-start/)
- [Let's Encrypt](https://letsencrypt.org/)

---

## ❓ Có vấn đề gì?

1. **Database connection error?** → Check DATABASE_URL in .env
2. **WebSocket not working?** → Make sure reverse proxy allows upgrades
3. **Port 3000 already in use?** → `sudo lsof -i :3000` then kill process
4. **PM2 not starting?** → Check logs: `pm2 logs`
5. **SSL certificate issue?** → Use certbot or Railway's SSL

---

**Ready to deploy? Let's go! 🚀**
