import { db } from '@/utils/database';
import { BaseService } from '@/services/BaseService';
import { NotFoundError } from '@/middleware/errorHandler';

export class UserService extends BaseService {
  async getUserById(userId: string) {
    try {
      const user = await db.user.findUnique({
        where: { id: userId },
        select: {
          id: true,
          email: true,
          name: true,
          avatar: true,
          role: true,
          isOnline: true,
          lastSeenAt: true,
          createdAt: true,
        },
      });

      if (!user) {
        throw new NotFoundError('User not found');
      }

      return user;
    } catch (error) {
      this.handleError(error);
    }
  }

  async updateUser(userId: string, data: any) {
    try {
      const user = await db.user.update({
        where: { id: userId },
        data: {
          ...(data.name && { name: data.name }),
          ...(data.avatar !== undefined && { avatar: data.avatar }),
        },
        select: {
          id: true,
          email: true,
          name: true,
          avatar: true,
          role: true,
        },
      });

      return user;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getAllUsers(filters: any = {}) {
    try {
      const { search, page = 1, limit = 10 } = filters;

      const where = search
        ? {
            OR: [
              { name: { contains: search } },
              { email: { contains: search } },
            ],
          }
        : {};

      const [users, total] = await Promise.all([
        db.user.findMany({
          where,
          select: {
            id: true,
            email: true,
            name: true,
            avatar: true,
            role: true,
            isOnline: true,
            lastSeenAt: true,
          },
          skip: (page - 1) * limit,
          take: limit,
          orderBy: { createdAt: 'desc' },
        }),
        db.user.count({ where }),
      ]);

      return { users, total };
    } catch (error) {
      this.handleError(error);
    }
  }

  async updateUserRole(userId: string, newRole: string) {
    try {
      const user = await db.user.update({
        where: { id: userId },
        data: { role: newRole as any },
        select: {
          id: true,
          email: true,
          name: true,
          role: true,
        },
      });

      return user;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getUsersForProject(projectId: string) {
    try {
      const members = await db.projectMember.findMany({
        where: { projectId },
        include: {
          user: {
            select: {
              id: true,
              name: true,
              email: true,
              avatar: true,
              isOnline: true,
            },
          },
        },
      });

      return members;
    } catch (error) {
      this.handleError(error);
    }
  }
}

export const userService = new UserService();
