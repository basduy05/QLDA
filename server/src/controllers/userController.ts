import { Request, Response, NextFunction } from 'express';
import { db } from '@/utils/database';
import { sendSuccess, sendError, sendPaginated } from '@/utils/response';

export async function getProfile(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = req.userId;

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
      return sendError(res, 'User not found', 404);
    }

    sendSuccess(res, user);
  } catch (error) {
    next(error);
  }
}

export async function updateProfile(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = req.userId;
    const { name, avatar } = req.body;

    const user = await db.user.update({
      where: { id: userId },
      data: {
        ...(name && { name }),
        ...(avatar && { avatar }),
      },
      select: {
        id: true,
        email: true,
        name: true,
        avatar: true,
        role: true,
      },
    });

    sendSuccess(res, user, 'Profile updated');
  } catch (error) {
    next(error);
  }
}

export async function getUsers(req: Request, res: Response, next: NextFunction) {
  try {
    const { page = 1, limit = 10, search } = req.query;

    const where = search ? {
      OR: [
        { name: { contains: search as string } },
        { email: { contains: search as string } },
      ],
    } : {};

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
        skip: (Number(page) - 1) * Number(limit),
        take: Number(limit),
      }),
      db.user.count({ where }),
    ]);

    sendPaginated(res, users, total, Number(page), Number(limit));
  } catch (error) {
    next(error);
  }
}

export async function getUserById(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;

    const user = await db.user.findUnique({
      where: { id },
      select: {
        id: true,
        email: true,
        name: true,
        avatar: true,
        role: true,
        isOnline: true,
        lastSeenAt: true,
      },
    });

    if (!user) {
      return sendError(res, 'User not found', 404);
    }

    sendSuccess(res, user);
  } catch (error) {
    next(error);
  }
}

export async function updateUserRole(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const { role } = req.body;

    // Check if requester is admin
    const requester = await db.user.findUnique({ where: { id: req.userId } });
    if (requester?.role !== 'ADMIN') {
      return sendError(res, 'Only admins can update user roles', 403);
    }

    const user = await db.user.update({
      where: { id },
      data: { role },
      select: {
        id: true,
        email: true,
        name: true,
        role: true,
      },
    });

    sendSuccess(res, user, 'User role updated');
  } catch (error) {
    next(error);
  }
}
