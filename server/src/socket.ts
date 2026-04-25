import { Server as HTTPServer } from 'http';
import { Server, Socket } from 'socket.io';
import jwt from 'jsonwebtoken';
import { handleDirectMessage, handleTyping, handleCallEvent, handleOnlineStatus } from '@/controllers/socketController';

let io: Server;

export function initializeSocketIO(httpServer: HTTPServer): Server {
  io = new Server(httpServer, {
    cors: {
      origin: process.env.SOCKET_IO_CORS_ORIGIN || 'http://localhost:5173',
      methods: ['GET', 'POST'],
      credentials: true,
    },
    transports: ['websocket', 'polling'],
  });

  // ============= AUTHENTICATION MIDDLEWARE =============
  io.use((socket, next) => {
    try {
      const token = socket.handshake.auth.token;
      if (!token) {
        return next(new Error('Authentication failed'));
      }

      const decoded = jwt.verify(token, process.env.JWT_SECRET || 'secret');
      socket.data.userId = (decoded as any).id;
      socket.data.user = decoded;
      next();
    } catch (error) {
      next(new Error('Authentication failed'));
    }
  });

  // ============= CONNECTION & EVENTS =============
  io.on('connection', (socket: Socket) => {
    const userId = socket.data.userId;
    console.log(`✅ User ${userId} connected via Socket.io`);

    // Join user to their own room for targeted messages
    socket.join(`user:${userId}`);

    // -------- DIRECT MESSAGING ---------
    socket.on('direct_message:send', (data, callback) => {
      handleDirectMessage(data, socket, io, callback);
    });

    socket.on('typing:start', (data) => {
      handleTyping(data, socket, io);
    });

    socket.on('typing:stop', (data) => {
      handleTyping(data, socket, io, false);
    });

    // -------- VIDEO CALLS ---------
    socket.on('call:initiate', (data) => {
      handleCallEvent(data, socket, io, 'initiate');
    });

    socket.on('call:accept', (data) => {
      handleCallEvent(data, socket, io, 'accept');
    });

    socket.on('call:decline', (data) => {
      handleCallEvent(data, socket, io, 'decline');
    });

    socket.on('call:end', (data) => {
      handleCallEvent(data, socket, io, 'end');
    });

    // -------- ONLINE STATUS ---------
    socket.on('status:online', () => {
      handleOnlineStatus(userId, true, io);
    });

    socket.on('status:offline', () => {
      handleOnlineStatus(userId, false, io);
    });

    // -------- CHAT GROUP EVENTS ---------
    socket.on('group_message:send', (data) => {
      io.to(`group:${data.groupId}`).emit('group_message:received', {
        ...data,
        timestamp: new Date().toISOString(),
      });
    });

    socket.on('group:join', (groupId) => {
      socket.join(`group:${groupId}`);
      io.to(`group:${groupId}`).emit('user:joined_group', {
        groupId,
        userId,
        timestamp: new Date().toISOString(),
      });
    });

    socket.on('group:leave', (groupId) => {
      socket.leave(`group:${groupId}`);
      io.to(`group:${groupId}`).emit('user:left_group', {
        groupId,
        userId,
        timestamp: new Date().toISOString(),
      });
    });

    // -------- NOTIFICATIONS ---------
    socket.on('notification:subscribe', () => {
      socket.join(`notifications:${userId}`);
    });

    // -------- DISCONNECT ---------
    socket.on('disconnect', () => {
      console.log(`❌ User ${userId} disconnected`);
      handleOnlineStatus(userId, false, io);
    });

    socket.on('error', (error) => {
      console.error(`Socket error for user ${userId}:`, error);
    });
  });

  return io;
}

export function getIO(): Server {
  if (!io) {
    throw new Error('Socket.io not initialized');
  }
  return io;
}
