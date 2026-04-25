# Aperlex Server - Node.js Backend

Modern Project Management System backend built with Express.js, TypeScript, and Prisma.

## 🚀 Quick Start

### Prerequisites
- Node.js 18+
- MySQL 8.0+
- npm or yarn

### Installation

```bash
cd server
npm install
cp .env.example .env
```

### Setup Database

Edit `.env` with your database credentials:

```env
DATABASE_URL=mysql://user:password@localhost:3306/aperlex
```

Generate Prisma client:

```bash
npm run prisma:generate
npm run prisma:migrate
```

### Run Development Server

```bash
npm run dev
```

Server will run on `http://localhost:3000`

### Production Build

```bash
npm run build
npm start
```

## 📁 Project Structure

```
server/
├── src/
│   ├── controllers/       # Request handlers
│   ├── routes/            # API routes
│   ├── middleware/        # Express middleware
│   ├── services/          # Business logic (coming soon)
│   ├── utils/             # Helper functions
│   ├── types/             # TypeScript types
│   ├── app.ts             # Express config
│   ├── socket.ts          # WebSocket config
│   └── index.ts           # Entry point
├── prisma/
│   └── schema.prisma      # Database schema
├── package.json
├── tsconfig.json
└── .env.example
```

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/verify-otp` - Verify email OTP
- `POST /api/auth/logout` - Logout (requires auth)

### Users
- `GET /api/users/me` - Get profile
- `PUT /api/users/me` - Update profile
- `GET /api/users` - List all users
- `GET /api/users/:id` - Get user by ID
- `PUT /api/users/:id/role` - Update user role (admin only)

### Projects
- `POST /api/projects` - Create project
- `GET /api/projects` - List projects
- `GET /api/projects/:id` - Get project details
- `PUT /api/projects/:id` - Update project
- `DELETE /api/projects/:id` - Delete project
- `GET /api/projects/:id/members` - Get project members
- `POST /api/projects/:id/members` - Add member
- `DELETE /api/projects/:id/members/:userId` - Remove member

### Tasks
- `POST /api/tasks` - Create task
- `GET /api/tasks` - List tasks
- `GET /api/tasks/:id` - Get task details
- `PUT /api/tasks/:id` - Update task
- `DELETE /api/tasks/:id` - Delete task
- `POST /api/tasks/:id/subtasks` - Add subtask
- `PUT /api/tasks/:id/subtasks/:subtaskId` - Update subtask
- `DELETE /api/tasks/:id/subtasks/:subtaskId` - Delete subtask
- `GET /api/tasks/:id/comments` - Get comments
- `POST /api/tasks/:id/comments` - Add comment

## 🔌 WebSocket Events (Real-time)

### Direct Messaging
```javascript
// Send direct message
socket.emit('direct_message:send', {
  recipientId: 'user_id',
  conversationId: 'conv_id',
  content: 'Hello'
})

// Typing indicators
socket.emit('typing:start', { recipientId, conversationId })
socket.emit('typing:stop', { recipientId, conversationId })
```

### Video Calls
```javascript
socket.emit('call:initiate', { participantId })
socket.emit('call:accept', { callId })
socket.emit('call:decline', { callId })
socket.emit('call:end', { callId })
```

### Chat Groups
```javascript
socket.emit('group_message:send', { groupId, content })
socket.emit('group:join', groupId)
socket.emit('group:leave', groupId)
```

### Status
```javascript
socket.emit('status:online')
socket.emit('status:offline')
```

## 🗄️ Database Schema

The application uses Prisma ORM with the following main entities:

- **User** - User accounts with roles (ADMIN, USER)
- **Project** - Project container with owner and members
- **Task** - Individual tasks with status and priority
- **Subtask** - Task breakdowns
- **TaskComment** - Comments on tasks
- **DirectConversation** & **DirectMessage** - 1:1 messaging
- **ChatGroup** & **ChatMessage** - Group messaging
- **CallSession** - Video call tracking
- **AiMessage** - AI assistant conversation history
- **Notification** - System notifications

## 🔐 Authentication

Uses JWT tokens:

```bash
Authorization: Bearer <token>
```

Token expires in 7 days (configurable via `JWT_EXPIRE` env var).

## 📝 Environment Variables

```env
# Database
DATABASE_URL=mysql://user:password@localhost:3306/aperlex

# Server
NODE_ENV=development
PORT=3000
HOST=localhost

# JWT
JWT_SECRET=your_secret_key
JWT_EXPIRE=7d

# AWS S3
AWS_ACCESS_KEY_ID=key
AWS_SECRET_ACCESS_KEY=secret
AWS_REGION=us-east-1
AWS_S3_BUCKET=bucket-name

# AI
GEMINI_API_KEY=api_key

# Email
BREVO_API_KEY=api_key
BREVO_SENDER_EMAIL=email@domain.com
BREVO_SENDER_NAME=App Name

# WebSocket
SOCKET_IO_CORS_ORIGIN=http://localhost:5173
```

## 🧪 Testing

```bash
npm test
```

## 🔗 Database Migrations

```bash
# Create new migration
npm run prisma:migrate

# Open Prisma Studio
npm run prisma:studio

# Reset database (dev only)
npx prisma migrate reset
```

## 📚 Next Steps

### Phase 2: Additional Features
- [ ] File upload to S3
- [ ] Email notification service (Brevo)
- [ ] AI assistant integration (Gemini)
- [ ] Advanced search and filtering
- [ ] Export functionality (Excel, PDF)
- [ ] Audit logs
- [ ] Rate limiting

### Phase 3: Deployment
- [ ] Docker configuration
- [ ] CI/CD pipeline
- [ ] Database backups
- [ ] Performance monitoring
- [ ] Error tracking (Sentry)

## 🤝 Contributing

1. Create a feature branch
2. Make changes
3. Submit a pull request

## 📄 License

MIT

## 👨‍💻 Support

For issues or questions, please create an issue in the repository.
