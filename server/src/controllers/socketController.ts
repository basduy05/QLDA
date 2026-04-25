import { Socket, Server } from 'socket.io';
import { db } from '@/utils/database';

export async function handleDirectMessage(
  data: any,
  socket: Socket,
  io: Server,
  callback?: Function
) {
  try {
    const userId = socket.data.userId;
    const { recipientId, content, conversationId } = data;

    // Find or create conversation
    let conversation = await db.directConversation.findUnique({
      where: { id: conversationId },
    });

    if (!conversation) {
      conversation = await db.directConversation.create({
        data: {
          participants: JSON.stringify([userId, recipientId]),
        },
      });
    }

    // Create message
    const message = await db.directMessage.create({
      data: {
        conversationId: conversation.id,
        senderId: userId,
        recipientId,
        content,
      },
      include: {
        sender: { select: { id: true, name: true, avatar: true } },
      },
    });

    // Emit to recipient
    io.to(`user:${recipientId}`).emit('direct_message:received', {
      ...message,
      timestamp: new Date().toISOString(),
    });

    if (callback) {
      callback({ success: true, message });
    }
  } catch (error) {
    console.error('Error handling direct message:', error);
    if (callback) {
      callback({ success: false, error: 'Failed to send message' });
    }
  }
}

export async function handleTyping(
  data: any,
  socket: Socket,
  io: Server,
  isTyping: boolean = true
) {
  try {
    const userId = socket.data.userId;
    const { recipientId, conversationId } = data;

    io.to(`user:${recipientId}`).emit('typing:status', {
      userId,
      conversationId,
      isTyping,
    });
  } catch (error) {
    console.error('Error handling typing:', error);
  }
}

export async function handleCallEvent(
  data: any,
  socket: Socket,
  io: Server,
  eventType: 'initiate' | 'accept' | 'decline' | 'end'
) {
  try {
    const userId = socket.data.userId;
    const { participantId, callId } = data;

    if (eventType === 'initiate') {
      const callSession = await db.callSession.create({
        data: {
          initiatorId: userId,
          participantId,
        },
      });

      io.to(`user:${participantId}`).emit('call:incoming', {
        callId: callSession.id,
        from: userId,
        timestamp: new Date().toISOString(),
      });
    } else if (eventType === 'accept') {
      await db.callSession.update({
        where: { id: callId },
        data: {
          status: 'ACTIVE',
          startedAt: new Date(),
        },
      });

      io.to(`user:${participantId}`).emit('call:accepted', { callId });
      socket.emit('call:accepted', { callId });
    } else if (eventType === 'decline') {
      await db.callSession.update({
        where: { id: callId },
        data: { status: 'DECLINED' },
      });

      io.to(`user:${participantId}`).emit('call:declined', { callId });
    } else if (eventType === 'end') {
      await db.callSession.update({
        where: { id: callId },
        data: {
          status: 'ENDED',
          endedAt: new Date(),
        },
      });

      io.to(`user:${participantId}`).emit('call:ended', { callId });
    }
  } catch (error) {
    console.error('Error handling call event:', error);
  }
}

export async function handleOnlineStatus(
  userId: string,
  isOnline: boolean,
  io: Server
) {
  try {
    await db.user.update({
      where: { id: userId },
      data: {
        isOnline,
        lastSeenAt: new Date(),
      },
    });

    // Broadcast to all connected users
    io.emit('user:status_changed', {
      userId,
      isOnline,
      lastSeenAt: new Date().toISOString(),
    });
  } catch (error) {
    console.error('Error handling online status:', error);
  }
}
