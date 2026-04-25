# 🚀 Aperlex Complete Installation Guide

Hướng dẫn chi tiết để chuyển đổi từ Laravel sang Node.js/Express.

## 📋 Table of Contents
1. [Prerequisites](#prerequisites)
2. [Quick Start](#quick-start)
3. [Detailed Setup](#detailed-setup)
4. [Testing](#testing)
5. [Docker Setup](#docker-setup)
6. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required Software
- **Node.js 18+** - [Download](https://nodejs.org/)
- **npm 9+** - Comes with Node.js
- **MySQL 8.0+** - [Download](https://www.mysql.com/downloads/)
- **Git** - [Download](https://git-scm.com/)

### Optional
- **Docker & Docker Compose** - For containerized setup
- **Postman** - For API testing
- **VS Code** - Recommended editor

---

## Quick Start

### 1️⃣ Windows Setup

```bash
# Navigate to server directory
cd server

# Run setup script
setup.bat

# Edit .env with your database
notepad .env

# Setup database
npm run db:setup

# Start development server
npm run dev
```

### 2️⃣ Mac/Linux Setup

```bash
# Navigate to server directory
cd server

# Run setup script
chmod +x setup.sh
./setup.sh

# Edit .env with your database
nano .env  # or vim .env

# Setup database
npm run db:setup

# Start development server
npm run dev
```

---

## Detailed Setup

### Step 1: Install Dependencies

```bash
cd server
npm install
```

**Expected Output:**
```
added 245 packages, and audited 246 packages in 2m
```

### Step 2: Configure Environment

Create `.env` file:

```bash
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
# Database
DATABASE_URL=mysql://root:password@localhost:3306/aperlex

# Server Config
NODE_ENV=development
PORT=3000
HOST=localhost

# JWT
JWT_SECRET=your_super_secret_key_change_in_production
JWT_EXPIRE=7d

# AWS S3 (optional)
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_REGION=us-east-1
AWS_S3_BUCKET=aperlex-files

# AI (optional)
GEMINI_API_KEY=your_key

# Email (optional)
BREVO_API_KEY=your_key
BREVO_SENDER_EMAIL=noreply@aperlex.com

# WebSocket
SOCKET_IO_CORS_ORIGIN=http://localhost:5173
```

### Step 3: Setup Database

```bash
# Generate Prisma Client
npm run prisma:generate

# Run migrations
npm run prisma:migrate

# Seed demo data (optional)
npm run prisma:seed
```

**What this does:**
- Creates MySQL database schema
- Creates tables (users, projects, tasks, etc.)
- Inserts demo data for testing

### Step 4: Build & Start

**Development Mode (with hot reload):**
```bash
npm run dev
```

**Production Mode:**
```bash
npm run build
npm start
```

**Expected Output:**
```
🚀 Server running at http://localhost:3000
📡 WebSocket ready for real-time features
🌍 Environment: development
```

---

## Testing

### 1️⃣ Test Server Health

```bash
curl http://localhost:3000/health
```

Response:
```json
{
  "status": "ok",
  "timestamp": "2024-01-15T10:30:00Z",
  "uptime": 30.5
}
```

### 2️⃣ Test API - Register User

```bash
curl -X POST http://localhost:3000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "name": "Test User",
    "password": "Password123"
  }'
```

### 3️⃣ Test API - Login

```bash
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@aperlex.com",
    "password": "password123"
  }'
```

### 4️⃣ Use Token for Protected Routes

```bash
curl -X GET http://localhost:3000/api/users/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 5️⃣ Demo Credentials

After seed:
- **Email:** `admin@aperlex.com`
- **Password:** `password123`
- **Role:** `ADMIN`

---

## Docker Setup

### Using Docker Compose

```bash
# Start all services (MySQL, Redis, Node.js)
docker-compose up -d

# View logs
docker-compose logs -f server

# Stop services
docker-compose down

# Reset everything
docker-compose down -v
```

### Manual Docker Build

```bash
# Build image
docker build -t aperlex-server .

# Run container
docker run -p 3000:3000 \
  -e DATABASE_URL=mysql://user:pass@localhost:3306/aperlex \
  -e JWT_SECRET=secret \
  aperlex-server
```

---

## Project Structure

```
server/
├── src/
│   ├── controllers/        # Request handlers
│   ├── routes/             # API routes
│   ├── services/           # Business logic
│   ├── middleware/         # Express middleware
│   ├── utils/              # Helper functions
│   ├── types/              # TypeScript types
│   ├── app.ts              # Express config
│   ├── socket.ts           # WebSocket setup
│   └── index.ts            # Entry point
│
├── prisma/
│   ├── schema.prisma       # Database schema
│   └── seed.js             # Demo data
│
├── dist/                   # Compiled JavaScript (generated)
├── node_modules/           # Dependencies
│
├── package.json            # Dependencies config
├── tsconfig.json           # TypeScript config
├── Dockerfile              # Docker config
├── docker-compose.yml      # Docker Compose config
├── .env.example            # Environment template
├── .eslintrc.json          # Linting config
│
├── setup.sh                # Linux/Mac setup script
├── setup.bat               # Windows setup script
├── README.md               # Project README
└── INSTALLATION.md         # This file
```

---

## Common Commands

| Command | Description |
|---------|-------------|
| `npm run dev` | Start development server |
| `npm run build` | Build for production |
| `npm start` | Run production server |
| `npm run lint` | Check code quality |
| `npm run prisma:studio` | Open database GUI |
| `npm run prisma:migrate` | Create/run migrations |
| `npm run prisma:seed` | Populate with demo data |
| `npm test` | Run tests |

---

## Troubleshooting

### ❌ Database Connection Error

**Error:** `Can't reach database server at localhost:3306`

**Solution:**
```bash
# Check MySQL is running
# Windows
net start MySQL80

# Mac
brew services start mysql-server

# Linux
sudo systemctl start mysql
```

### ❌ Port Already in Use

**Error:** `Error: listen EADDRINUSE: address already in use :::3000`

**Solution:**
```bash
# Find process using port 3000
# Windows
netstat -ano | findstr :3000

# Mac/Linux
lsof -i :3000

# Kill process
# Windows
taskkill /PID <PID> /F

# Mac/Linux
kill -9 <PID>

# Or change PORT in .env
PORT=3001
```

### ❌ Dependencies Installation Failed

**Error:** `npm ERR! code ERESOLVE`

**Solution:**
```bash
# Clear cache and retry
npm cache clean --force
npm install

# Or use legacy peer deps
npm install --legacy-peer-deps
```

### ❌ TypeScript Compilation Error

**Error:** `error TS2307: Cannot find module '@/utils/database'`

**Solution:**
```bash
# Regenerate Prisma client
npm run prisma:generate

# Clear and rebuild
rm -rf dist
npm run build
```

### ❌ WebSocket Connection Failed

**Error:** `WebSocket connection to ws://localhost:3000 failed`

**Check:**
1. Frontend CORS origin matches `SOCKET_IO_CORS_ORIGIN` in .env
2. Server is running
3. Frontend is using correct port

**Solution:**
```env
# Update .env
SOCKET_IO_CORS_ORIGIN=http://localhost:5173  # Your frontend URL
```

---

## Performance Tips

### ✅ Development
- Use `npm run dev` for hot reload
- Install **Thunder Client** or **REST Client** VS Code extension
- Keep browser DevTools open to see real-time logs

### ✅ Production
- Set `NODE_ENV=production`
- Use `npm run build && npm start`
- Enable caching in database queries
- Use environment variables for secrets

---

## Next Steps

1. ✅ **Update Frontend** - Configure Vue.js to use new APIs
2. ✅ **Test All Features** - Verify authentication, CRUD, real-time
3. ✅ **Add Services** - S3 uploads, Email, AI integration
4. ✅ **Deploy** - Docker, Vercel, AWS, or your server

---

## Support

### Documentation
- [API Documentation](./API.md)
- [Database Schema](./SCHEMA.md)
- [WebSocket Events](./WEBSOCKET.md)

### Resources
- [Express.js Docs](https://expressjs.com/)
- [Prisma Docs](https://www.prisma.io/docs/)
- [Socket.io Docs](https://socket.io/docs/)
- [TypeScript Docs](https://www.typescriptlang.org/docs/)

### Getting Help
- Check troubleshooting section above
- Create GitHub issue with error details
- Contact development team

---

## Checklist

- [ ] Node.js 18+ installed
- [ ] MySQL 8.0+ running locally
- [ ] `.env` file created and configured
- [ ] `npm install` completed
- [ ] `npm run db:setup` completed
- [ ] `npm run dev` starts successfully
- [ ] `curl http://localhost:3000/health` returns OK
- [ ] Can register and login in Postman
- [ ] Frontend connects to new API

---

**🎉 You're ready to go! Happy coding!**

Last Updated: April 25, 2024
Version: 1.0.0
