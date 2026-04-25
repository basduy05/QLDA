import 'dotenv/config';
import app from '@/app';
import { initializeSocketIO } from '@/socket';
import { createServer } from 'http';

const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || 'localhost';

const httpServer = createServer(app);

// Initialize Socket.io for real-time features
initializeSocketIO(httpServer);

httpServer.listen(PORT, () => {
  console.log(`🚀 Server running at http://${HOST}:${PORT}`);
  console.log(`📡 WebSocket ready for real-time features`);
  console.log(`🌍 Environment: ${process.env.NODE_ENV || 'development'}`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
  console.log('SIGTERM received, closing server...');
  httpServer.close(() => {
    console.log('Server closed');
    process.exit(0);
  });
});
