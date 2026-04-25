# GitHub Actions Secrets Setup

Để GitHub Actions tự động deploy, bạn cần setup các secrets sau:

## 📋 GitHub Secrets Configuration

### 1. SSH Key Pair (Tạo trên server)

**Trên server của bạn:**
```bash
# Generate SSH key
ssh-keygen -t rsa -b 4096 -f ~/.ssh/github_deploy

# Copy public key
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys

# Copy private key
cat ~/.ssh/github_deploy
```

### 2. Add Secrets to GitHub

**Trên GitHub (Settings → Secrets and variables → Actions):**

| Tên | Giá Trị | Ví Dụ |
|-----|--------|-------|
| `SERVER_HOST` | IP/Domain của server | `192.168.1.100` hoặc `api.domain.com` |
| `SERVER_USER` | SSH username | `ubuntu` hoặc `ec2-user` |
| `SERVER_PORT` | SSH port | `22` |
| `SERVER_SSH_KEY` | Private SSH key | Output của `cat ~/.ssh/github_deploy` |
| `NOTIFICATION_EMAIL` | Email để nhận thông báo | `your-email@gmail.com` |
| `MAIL_SERVER` | SMTP server | `smtp.gmail.com` |
| `MAIL_PORT` | SMTP port | `587` |
| `MAIL_USERNAME` | Email username | `your-email@gmail.com` |
| `MAIL_PASSWORD` | Email password | Mật khẩu hoặc app password |

---

## 🔧 Setup Steps

### 1. Generate SSH Key Pair

**Trên server:**
```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/github_deploy -N ""

# Thêm vào authorized_keys
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys

# Lấy private key
cat ~/.ssh/github_deploy
```

**Copy toàn bộ nội dung của private key**

### 2. Add Secrets to GitHub

1. Vào GitHub repo → **Settings**
2. Chọn **Secrets and variables** → **Actions**
3. Nhấn **New repository secret**
4. Thêm từng secret theo bảng dưới:

```
SERVER_HOST: 192.168.1.100
SERVER_USER: ubuntu
SERVER_PORT: 22
SERVER_SSH_KEY: (paste private key content here)
NOTIFICATION_EMAIL: your-email@gmail.com
MAIL_SERVER: smtp.gmail.com
MAIL_PORT: 587
MAIL_USERNAME: your-email@gmail.com
MAIL_PASSWORD: your-app-password
```

---

## 🚀 Test Deployment Pipeline

```bash
# 1. Make a commit
git add .
git commit -m "Test deployment"

# 2. Push to main
git push origin main

# 3. Check GitHub Actions
# Vào GitHub repo → Actions → Deploy workflow
```

---

## 📊 Workflow Triggers

Deployment tự động chạy khi:
- ✅ Push to `main` branch
- ✅ Push to `production` branch  
- ✅ Manual trigger (Workflow dispatch)

---

## 📝 Disable/Enable Deployment

### Disable:
```bash
# Đổi tên workflow
mv .github/workflows/deploy.yml .github/workflows/deploy.yml.disabled
git push
```

### Enable:
```bash
# Đổi tên lại
mv .github/workflows/deploy.yml.disabled .github/workflows/deploy.yml
git push
```

---

## 📧 Email Notifications

### Gmail Setup:
1. Bật 2-step verification
2. Tạo App Password
3. Dùng App Password trong `MAIL_PASSWORD`

### Outlook/Office365:
```
MAIL_SERVER: smtp-mail.outlook.com
MAIL_PORT: 587
MAIL_USERNAME: your-email@outlook.com
MAIL_PASSWORD: your-password
```

---

## 🔍 View Deployment Logs

```bash
# GitHub Actions logs
# Vào: GitHub repo → Actions → Deploy → Click run

# Server logs
ssh user@server
pm2 logs aperlex-backend
```

---

## ⚠️ Troubleshooting

### SSH Connection Failed
```bash
# Check SSH key
ssh -i ~/.ssh/github_deploy user@server

# Fix permissions
chmod 600 ~/.ssh/github_deploy
chmod 700 ~/.ssh
```

### Deployment Fails
```bash
# Check server logs
ssh user@server
pm2 status
pm2 logs aperlex-backend
```

### Email Not Sending
```bash
# Test credentials
telnet smtp.gmail.com 587

# Check App Password
# (for Gmail: account.google.com → App passwords)
```

---

## ✅ Health Check

Sau mỗi deployment, workflow tự động kiểm tra:

```bash
curl http://localhost:3000/api/health
```

Nếu response là **200 OK**, deployment thành công! ✅

---

## 🎯 Summary

```
GitHub Push
    ↓
Actions Workflow Start
    ↓
Install Dependencies
    ↓
Run Linter
    ↓
Build Backend
    ↓
Build Frontend
    ↓
SSH to Server
    ↓
Pull Latest Code
    ↓
Run deploy.sh
    ↓
Health Check
    ↓
Send Notification Email
```

---

**Ready? Let's deploy! 🚀**
