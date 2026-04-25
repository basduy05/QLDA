# 🎯 Complete Integration Guide

## Overview

This guide shows how to integrate all components:
- ✅ Frontend API integration  
- ✅ Testing framework
- ✅ AWS S3 file uploads
- ✅ Brevo email service
- ✅ Gemini AI features
- ✅ SSL/HTTPS setup

---

## 📋 Checklist

### Phase 1: Frontend Integration ✅

```bash
# 1. Install dependencies
cd QLDUAN
npm install

# 2. Update .env.frontend
cp .env.frontend .env.local

# Edit .env.local:
VITE_API_URL=http://localhost:3000/api
VITE_SOCKET_URL=http://localhost:3000

# 3. Start frontend
npm run dev
```

### Phase 2: Backend Setup ✅

```bash
# 1. Install dependencies
cd server
npm install

# 2. Install new packages
npm install aws-sdk @getbrevo/brevo @google/generative-ai socket.io-client

# 3. Configure environment
cp .env.example .env

# Edit .env with:
DATABASE_URL=mysql://user:password@localhost:3306/db
JWT_SECRET=your_secret_key
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_REGION=us-east-1
AWS_S3_BUCKET=your_bucket
BREVO_API_KEY=your_brevo_key
BREVO_SENDER_EMAIL=your_email@domain.com
GEMINI_API_KEY=your_gemini_key

# 4. Setup database
npm run prisma:migrate:deploy
npm run prisma:seed

# 5. Start backend
npm run dev
```

### Phase 3: API Testing ✅

```bash
# 1. Using test file
node server/tests/api.test.js

# 2. Using Postman
# - Import: server/tests/APERLEX_API.postman_collection.json
# - Set base_url: http://localhost:3000/api
# - Get token from login
# - Run tests

# 3. Using curl
curl -X POST http://localhost:3000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "TestPassword123!",
    "password_confirmation": "TestPassword123!"
  }'
```

### Phase 4: Service Integration ✅

#### AWS S3

```bash
# 1. Create AWS S3 bucket
# 2. Create IAM user with S3 access
# 3. Add to .env:
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_REGION=us-east-1
AWS_S3_BUCKET=your_bucket

# 4. Use in code:
import S3Service from '@/services/S3Service';
const result = await S3Service.uploadFile(file, 'uploads');
```

#### Brevo Email

```bash
# 1. Create Brevo account at brevo.com
# 2. Verify sender email
# 3. Get API key
# 4. Add to .env:
BREVO_API_KEY=your_key
BREVO_SENDER_EMAIL=noreply@yourdomain.com
BREVO_SENDER_NAME=APERLEX

# 5. Use in code:
import EmailService from '@/services/EmailService';
await EmailService.sendOtpEmail('user@example.com', '123456');
```

#### Gemini AI

```bash
# 1. Go to google.ai/
# 2. Get API key
# 3. Add to .env:
GEMINI_API_KEY=your_key

# 4. Use in code:
import GeminiAiService from '@/services/GeminiAiService';
const response = await GeminiAiService.generateResponse('Hello');
```

#### SSL/HTTPS

```bash
# 1. Get domain
# 2. Install Certbot:
sudo apt install certbot python3-certbot-nginx

# 3. Get certificate:
sudo certbot --nginx -d yourdomain.com

# 4. Auto-renew:
sudo certbot renew --dry-run
```

---

## 🚀 Complete Setup Script

**setup-all.sh (Linux/Mac):**

```bash
#!/bin/bash

echo "🚀 APERLEX Complete Setup"

# Frontend
echo "📦 Setting up frontend..."
npm install

# Backend
echo "📦 Setting up backend..."
cd server
npm install
npm install aws-sdk @getbrevo/brevo @google/generative-ai socket.io-client

# Database
echo "🗄️ Setting up database..."
npm run prisma:migrate:deploy
npm run prisma:seed

# Back to root
cd ..

echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Configure .env files:"
echo "   - server/.env"
echo "   - .env.frontend"
echo ""
echo "2. Start services:"
echo "   - Terminal 1: npm run dev (frontend)"
echo "   - Terminal 2: cd server && npm run dev (backend)"
echo ""
echo "3. Test:"
echo "   - Frontend: http://localhost:5173"
echo "   - API: http://localhost:3000/api"
echo "   - WebSocket: ws://localhost:3000"
```

---

## 📦 Environment Variables Template

**.env (Backend):**

```env
# Core
NODE_ENV=production
PORT=3000
DEBUG=aperlex:*

# Database
DATABASE_URL=mysql://root:password@localhost:3306/qhorizonpm_db

# Auth
JWT_SECRET=your_super_secret_key_change_this
JWT_EXPIRY=24h
REFRESH_TOKEN_EXPIRY=7d

# CORS
FRONTEND_URL=http://localhost:5173
CORS_ORIGIN=http://localhost:5173,https://yourdomain.com

# AWS S3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_REGION=us-east-1
AWS_S3_BUCKET=aperlex-files-prod
AWS_S3_URL=https://aperlex-files-prod.s3.amazonaws.com

# Brevo Email
BREVO_API_KEY=your_brevo_api_key
BREVO_SENDER_EMAIL=noreply@yourdomain.com
BREVO_SENDER_NAME=APERLEX

# Gemini AI
GEMINI_API_KEY=your_gemini_api_key

# Redis (Optional)
REDIS_HOST=localhost
REDIS_PORT=6379

# App
APP_NAME=APERLEX
APP_URL=https://yourdomain.com
LOG_LEVEL=info
```

**.env.frontend:**

```env
VITE_API_URL=http://localhost:3000/api
VITE_SOCKET_URL=http://localhost:3000
VITE_APP_NAME=APERLEX
VITE_APP_DEBUG=true
```

---

## 🧪 Testing Flow

### 1. Unit Tests

```bash
cd server
npm run test
```

### 2. API Integration Tests

```bash
# Manual testing
node tests/api.test.js

# Or with Postman
# Import collection and run tests
```

### 3. Frontend Tests

```bash
# Component tests
npm run test:unit

# E2E tests
npm run test:e2e
```

---

## 🔍 Verification Checklist

- [ ] Frontend loads at http://localhost:5173
- [ ] Backend API responds at http://localhost:3000/api
- [ ] WebSocket connects at ws://localhost:3000
- [ ] Database migrations completed
- [ ] AWS S3 file upload working
- [ ] Brevo email sending working
- [ ] Gemini AI responding
- [ ] All API endpoints tested
- [ ] Error handling working
- [ ] SSL/HTTPS configured

---

## 📊 Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                         Frontend (Vue.js)                   │
│              http://localhost:5173                          │
└────────────────┬────────────────────────────────────────────┘
                 │
                 │ HTTP/WebSocket
                 ▼
┌─────────────────────────────────────────────────────────────┐
│                    Backend (Express.js)                     │
│              http://localhost:3000                          │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   Auth API   │  │ Project API  │  │  Message API │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  Task API    │  │ Notification │  │   Report API │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└────────┬──────────────┬──────────────┬──────────┬──────────┘
         │              │              │          │
         ▼              ▼              ▼          ▼
    ┌────────┐   ┌───────────┐  ┌──────────┐  ┌───────────┐
    │ MySQL  │   │ AWS S3    │  │  Brevo   │  │  Gemini   │
    │Database│   │ (Files)   │  │ (Email)  │  │   (AI)    │
    └────────┘   └───────────┘  └──────────┘  └───────────┘
```

---

## 🔗 API Routes Summary

```
Authentication
├── POST   /auth/register              - Register new user
├── POST   /auth/login                 - Login user
├── POST   /auth/verify-otp            - Verify OTP
├── POST   /auth/logout                - Logout user
└── POST   /auth/refresh-token         - Refresh JWT token

Users
├── GET    /users/profile              - Get user profile
├── PUT    /users/profile              - Update profile
├── GET    /users                      - Get all users
└── PUT    /users/:id/role             - Update user role

Projects
├── POST   /projects                   - Create project
├── GET    /projects                   - Get user projects
├── GET    /projects/:id               - Get project by ID
├── PUT    /projects/:id               - Update project
├── DELETE /projects/:id               - Delete project
├── GET    /projects/:id/members       - Get project members
└── POST   /projects/:id/members       - Add member

Tasks
├── POST   /projects/:id/tasks         - Create task
├── GET    /projects/:id/tasks         - Get tasks
├── PUT    /tasks/:id                  - Update task
├── DELETE /tasks/:id                  - Delete task
├── POST   /tasks/:id/subtasks         - Add subtask
└── POST   /tasks/:id/comments         - Add comment

Messages
├── POST   /conversations              - Create conversation
├── GET    /conversations              - Get conversations
├── POST   /conversations/:id/messages - Send message
└── GET    /conversations/:id/messages - Get messages

AI
├── POST   /ai/messages                - Send AI message
├── GET    /ai/insights/:id            - Get project insights
└── POST   /ai/report                  - Generate report

Notifications
├── GET    /notifications              - Get notifications
├── GET    /notifications/unread/count - Unread count
├── PATCH  /notifications/:id/read     - Mark as read
└── DELETE /notifications/:id          - Delete notification
```

---

## 📚 Documentation Files

- `DEPLOYMENT_GUIDE.md` - 3 deployment options
- `QUICK_DEPLOY.md` - Quick 5-minute setup
- `SSL_HTTPS_SETUP.md` - SSL certificate setup
- `AWS_S3_SETUP.md` - S3 file upload integration
- `BREVO_EMAIL_SETUP.md` - Email service setup
- `GEMINI_AI_SETUP.md` - AI features setup
- `DEPLOYMENT_CHECKLIST.md` - Pre-deployment checklist

---

## 🆘 Common Issues & Solutions

### Issue: API not responding
```bash
# Check backend is running
curl http://localhost:3000/api/health

# Check logs
pm2 logs aperlex-backend

# Restart backend
pm2 restart aperlex-backend
```

### Issue: Database connection error
```bash
# Check MySQL running
sudo systemctl status mysql

# Check credentials in .env
mysql -u root -p -h localhost -D qhorizonpm_db
```

### Issue: WebSocket not connecting
```bash
# Check Socket.io in backend
# Verify CORS_ORIGIN in .env
# Check firewall allows port 3000
```

### Issue: S3 upload failing
```bash
# Verify AWS credentials
# Check S3 bucket exists
# Verify IAM user has S3 permissions
```

### Issue: Email not sending
```bash
# Verify BREVO_API_KEY
# Check sender email is verified in Brevo
# Review Brevo bounce list
```

---

## 🎓 Next Steps

1. **Complete Frontend Update**
   - Update all Vue components to use new APIs
   - Test each feature thoroughly
   - Add error boundaries

2. **User Testing**
   - Invite test users
   - Collect feedback
   - Fix issues

3. **Performance Optimization**
   - Add caching
   - Optimize queries
   - Monitor performance

4. **Production Deployment**
   - Setup SSL/HTTPS
   - Configure CDN
   - Setup monitoring
   - Plan backup strategy

5. **Advanced Features**
   - Excel export
   - Team collaboration
   - Advanced analytics
   - Mobile app

---

**Integration Complete! 🎉**

For questions, check the specific setup guides or documentation files.
