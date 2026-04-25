import { db } from '@/utils/database';
import { BaseService } from '@/services/BaseService';
import { NotificationType } from '@prisma/client';

export class NotificationService extends BaseService {
  async createNotification(
    userId: string,
    type: NotificationType,
    title: string,
    message: string,
    data?: any
  ) {
    try {
      const notification = await db.notification.create({
        data: {
          userId,
          type,
          title,
          message,
          data: data ? JSON.stringify(data) : undefined,
        },
      });

      return notification;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getUserNotifications(userId: string, limit: number = 20, offset: number = 0) {
    try {
      const [notifications, total] = await Promise.all([
        db.notification.findMany({
          where: { userId },
          orderBy: { createdAt: 'desc' },
          skip: offset,
          take: limit,
        }),
        db.notification.count({ where: { userId } }),
      ]);

      return { notifications, total };
    } catch (error) {
      this.handleError(error);
    }
  }

  async getUnreadNotifications(userId: string) {
    try {
      const notifications = await db.notification.findMany({
        where: { userId, isRead: false },
        orderBy: { createdAt: 'desc' },
      });

      return notifications;
    } catch (error) {
      this.handleError(error);
    }
  }

  async markAsRead(notificationId: string) {
    try {
      const notification = await db.notification.update({
        where: { id: notificationId },
        data: { isRead: true, readAt: new Date() },
      });

      return notification;
    } catch (error) {
      this.handleError(error);
    }
  }

  async markAllAsRead(userId: string) {
    try {
      await db.notification.updateMany({
        where: { userId, isRead: false },
        data: { isRead: true, readAt: new Date() },
      });

      return { message: 'All notifications marked as read' };
    } catch (error) {
      this.handleError(error);
    }
  }

  async deleteNotification(notificationId: string) {
    try {
      await db.notification.delete({ where: { id: notificationId } });
      return { message: 'Notification deleted' };
    } catch (error) {
      this.handleError(error);
    }
  }
}

export const notificationService = new NotificationService();
