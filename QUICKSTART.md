# 🚀 APERLEX - QUICK START GUIDE

**Status:** ✅ 100% COMPLETE & PRODUCTION READY

---

## ⚡ 3-Minute Quick Start

### Windows
```bash
cd server
setup.bat
npm run dev
```

### Mac/Linux  
```bash
cd server
chmod +x setup.sh && ./setup.sh
npm run dev
```

### Docker
```bash
cd server
docker-compose up -d
```

**That's it!** Server runs at `http://localhost:3000`

---

## 📖 Documentation

| Document | Purpose |
|----------|---------|
| [COMPLETION_SUMMARY.md](./COMPLETION_SUMMARY.md) | ✅ What was accomplished |
| [INSTALLATION.md](./INSTALLATION.md) | 📋 Detailed setup guide |
| [MIGRATION_STATUS.md](./MIGRATION_STATUS.md) | 📊 Project status |
| [server/README.md](./server/README.md) | 🔌 API reference |

---

## 🧪 Test APIs

### 1. Health Check
```bash
curl http://localhost:3000/health
```

### 2. Login (Demo Account)
```bash
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@aperlex.com",
    "password": "password123"
  }'
```

### 3. Get Profile (Replace TOKEN)
```bash
curl -X GET http://localhost:3000/api/users/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🛠️ Useful Commands

```bash
# Development
npm run dev              # Start with hot reload
npm run build            # Build for production
npm start                # Run production build

# Database
npm run prisma:migrate   # Run migrations
npm run prisma:seed      # Add demo data
npm run prisma:studio    # Open database GUI

# Quality
npm run lint             # Check code quality
npm test                 # Run tests

# Setup
npm run setup            # Fresh install
npm run db:setup         # Setup database
```

---

## 📁 Project Structure

```
server/
├── src/
│   ├── controllers/     ← Request handlers
│   ├── routes/          ← API routes
│   ├── services/        ← Business logic
│   ├── middleware/      ← Express middleware
│   ├── utils/           ← Helpers
│   ├── types/           ← TypeScript types
│   ├── app.ts           ← Express config
│   ├── socket.ts        ← WebSocket setup
│   └── index.ts         ← Entry point
├── prisma/
│   ├── schema.prisma    ← Database schema
│   └── seed.js          ← Demo data
├── dist/                ← Compiled code
└── node_modules/        ← Dependencies
```

---

## 🔌 API Endpoints (20+)

### Auth
- `POST /api/auth/register` - Create account
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout

### Users
- `GET /api/users/me` - Your profile
- `PUT /api/users/me` - Update profile
- `GET /api/users` - List users

### Projects
- `POST /api/projects` - Create project
- `GET /api/projects` - List projects
- `GET /api/projects/:id` - Project details
- `PUT /api/projects/:id` - Update project
- `DELETE /api/projects/:id` - Delete project
- `POST /api/projects/:id/members` - Add member
- `DELETE /api/projects/:id/members/:userId` - Remove member

### Tasks
- `POST /api/tasks` - Create task
- `GET /api/tasks` - List tasks
- `GET /api/tasks/:id` - Task details
- `PUT /api/tasks/:id` - Update task
- `DELETE /api/tasks/:id` - Delete task
- `POST /api/tasks/:id/comments` - Add comment
- `POST /api/tasks/:id/subtasks` - Add subtask

---

## 🔌 WebSocket Events (Real-time)

```javascript
// Connect
socket.connect()

// Direct Message
socket.emit('direct_message:send', {
  recipientId: 'user_id',
  conversationId: 'conv_id',
  content: 'Hello'
})

// Typing
socket.emit('typing:start', { recipientId, conversationId })
socket.emit('typing:stop', { recipientId, conversationId })

// Call
socket.emit('call:initiate', { participantId })
socket.emit('call:accept', { callId })
socket.emit('call:end', { callId })

// Status
socket.emit('status:online')
socket.emit('status:offline')
```

---

## 🗄️ Database Models

```
User
├── Projects (owner)
├── ProjectMembers
├── Tasks (assigned)
├── TaskComments
├── DirectMessages
├── ChatGroupMembers
├── ChatMessages
├── CallSessions
├── AiMessages
└── Notifications

Project
├── Members
├── Tasks
└── Owner (User)

Task
├── Subtasks
├── Comments
├── Attachments
└── Assignee (User)

ChatGroup
├── Members
└── Messages

DirectConversation
└── Messages
```

---

## 🔐 Environment Variables

```env
DATABASE_URL=mysql://user:password@localhost:3306/aperlex
NODE_ENV=development
PORT=3000
JWT_SECRET=change_me
SOCKET_IO_CORS_ORIGIN=http://localhost:5173
```

See `.env.example` for all options.

---

## 🐛 Troubleshooting

### Database Connection Failed
```bash
# Check MySQL is running
# Windows: net start MySQL80
# Mac: brew services start mysql-server
# Linux: sudo systemctl start mysql
```

### Port 3000 Already in Use
```bash
# Change PORT in .env
PORT=3001
```

### Dependencies Not Installing
```bash
npm install --legacy-peer-deps
```

### TypeScript Error
```bash
npm run prisma:generate
npm run build
```

See [INSTALLATION.md](./INSTALLATION.md) for more.

---

## 📊 What's New

✅ **From Laravel to Node.js**
- Modern TypeScript backend
- Type-safe development
- Hot reload development
- Better real-time support

✅ **Included**
- Complete REST API
- WebSocket support
- Database ORM
- Authentication system
- Error handling
- Logging system

✅ **Ready for Production**
- Docker configuration
- Setup scripts
- Database migrations
- Seed data
- Environment config

---

## 🎯 Next Steps

1. ✅ **Backend is ready** ← You are here
2. 🔄 **Update Vue.js frontend** ← Next
3. 🚀 **Deploy to production** ← Then

---

## 📞 Need Help?

1. Check troubleshooting above
2. Read [INSTALLATION.md](./INSTALLATION.md)
3. Review [server/README.md](./server/README.md)
4. Check console for errors

---

## 🎉 You're All Set!

Backend is **100% complete**.  
Start with `npm run dev` and begin frontend integration.

**Happy coding! 🚀**

---

**Quick Links:**
- [Full Setup Guide](./INSTALLATION.md)
- [Project Status](./MIGRATION_STATUS.md)  
- [API Reference](./server/README.md)
- [Completion Summary](./COMPLETION_SUMMARY.md)

Last Updated: April 25, 2024  
Version: 1.0.0
