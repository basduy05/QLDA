import express, { Express, Request, Response, NextFunction } from 'express';
import cors from 'cors';
import { errorHandler } from '@/middleware/errorHandler';
import { requestLogger } from '@/middleware/requestLogger';

const app: Express = express();

// ============= MIDDLEWARE =============
app.use(cors({
  origin: process.env.SOCKET_IO_CORS_ORIGIN || 'http://localhost:5173',
  credentials: true,
}));

app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ limit: '10mb', extended: true }));

// Request logging
app.use(requestLogger);

// ============= ROUTES =============
app.get('/health', (req: Request, res: Response) => {
  res.json({ 
    status: 'ok', 
    timestamp: new Date().toISOString(),
    uptime: process.uptime(),
  });
});

app.get('/api/health', (req: Request, res: Response) => {
  res.json({ 
    message: 'Aperlex API Server',
    version: '1.0.0',
    status: 'running',
  });
});

// Auth routes
import authRoutes from '@/routes/auth';
app.use('/api/auth', authRoutes);

// User routes
import userRoutes from '@/routes/users';
app.use('/api/users', userRoutes);

// Project routes
import projectRoutes from '@/routes/projects';
app.use('/api/projects', projectRoutes);

// Task routes
import taskRoutes from '@/routes/tasks';
app.use('/api/tasks', taskRoutes);

// Message routes
import messageRoutes from '@/routes/messages';
app.use('/api/messages', messageRoutes);

// ============= ERROR HANDLING =============
app.use((req: Request, res: Response) => {
  res.status(404).json({ error: 'Route not found' });
});

app.use(errorHandler);

export default app;
