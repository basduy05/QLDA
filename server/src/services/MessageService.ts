import { db } from '@/utils/database';
import { BaseService } from '@/services/BaseService';

export class MessageService extends BaseService {
  async createDirectMessage(conversationId: string, senderId: string, recipientId: string, content: string) {
    try {
      const message = await db.directMessage.create({
        data: {
          conversationId,
          senderId,
          recipientId,
          content,
        },
        include: {
          sender: { select: { id: true, name: true, avatar: true } },
        },
      });

      return message;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getConversationMessages(conversationId: string, limit: number = 50, offset: number = 0) {
    try {
      const messages = await db.directMessage.findMany({
        where: { conversationId },
        include: {
          sender: { select: { id: true, name: true, avatar: true } },
        },
        orderBy: { createdAt: 'desc' },
        skip: offset,
        take: limit,
      });

      return messages.reverse();
    } catch (error) {
      this.handleError(error);
    }
  }

  async markMessageAsRead(messageId: string) {
    try {
      const message = await db.directMessage.update({
        where: { id: messageId },
        data: { isRead: true, readAt: new Date() },
      });

      return message;
    } catch (error) {
      this.handleError(error);
    }
  }

  async createGroupMessage(groupId: string, senderId: string, content: string) {
    try {
      const message = await db.chatMessage.create({
        data: {
          groupId,
          senderId,
          content,
        },
        include: {
          sender: { select: { id: true, name: true, avatar: true } },
        },
      });

      return message;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getGroupMessages(groupId: string, limit: number = 50, offset: number = 0) {
    try {
      const messages = await db.chatMessage.findMany({
        where: { groupId },
        include: {
          sender: { select: { id: true, name: true, avatar: true } },
        },
        orderBy: { createdAt: 'desc' },
        skip: offset,
        take: limit,
      });

      return messages.reverse();
    } catch (error) {
      this.handleError(error);
    }
  }
}

export const messageService = new MessageService();
