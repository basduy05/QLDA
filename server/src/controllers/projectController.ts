import { Request, Response, NextFunction } from 'express';
import { db } from '@/utils/database';
import { sendSuccess, sendError, sendPaginated } from '@/utils/response';

export async function createProject(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = req.userId;
    const { name, description, startDate, endDate } = req.body;

    if (!name) {
      return sendError(res, 'Project name is required', 400);
    }

    const project = await db.project.create({
      data: {
        name,
        description,
        startDate: startDate ? new Date(startDate) : undefined,
        endDate: endDate ? new Date(endDate) : undefined,
        ownerId: userId,
        members: {
          create: {
            userId: req.user?.id as string,
            role: 'LEAD',
          },
        },
      },
      include: {
        members: { include: { user: { select: { id: true, name: true, email: true, avatar: true } } } },
      },
    });

    sendSuccess(res, project, 'Project created', 201);
  } catch (error) {
    next(error);
  }
}

export async function getProjects(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = req.userId;
    const { page = 1, limit = 10, status = 'active' } = req.query;

    const [projects, total] = await Promise.all([
      db.project.findMany({
        where: {
          OR: [
            { ownerId: userId },
            { members: { some: { userId } } },
          ],
          ...(status && { status: status as string }),
        },
        include: {
          owner: { select: { id: true, name: true, avatar: true } },
          members: { include: { user: { select: { id: true, name: true, avatar: true } } } },
          _count: { select: { tasks: true } },
        },
        skip: (Number(page) - 1) * Number(limit),
        take: Number(limit),
      }),
      db.project.count({
        where: {
          OR: [
            { ownerId: userId },
            { members: { some: { userId } } },
          ],
        },
      }),
    ]);

    sendPaginated(res, projects, total, Number(page), Number(limit));
  } catch (error) {
    next(error);
  }
}

export async function getProjectById(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const userId = req.userId;

    const project = await db.project.findUnique({
      where: { id },
      include: {
        owner: { select: { id: true, name: true, avatar: true } },
        members: { include: { user: { select: { id: true, name: true, email: true, avatar: true } } } },
        tasks: { select: { id: true, title: true, status: true, priority: true } },
      },
    });

    if (!project) {
      return sendError(res, 'Project not found', 404);
    }

    // Check access
    const isMember = project.members.some(m => m.userId === userId) || project.ownerId === userId;
    if (!isMember) {
      return sendError(res, 'Access denied', 403);
    }

    sendSuccess(res, project);
  } catch (error) {
    next(error);
  }
}

export async function updateProject(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const userId = req.userId;
    const { name, description, status, startDate, endDate } = req.body;

    const project = await db.project.findUnique({ where: { id } });
    if (!project) {
      return sendError(res, 'Project not found', 404);
    }

    // Check if user is owner
    if (project.ownerId !== userId) {
      return sendError(res, 'Only project owner can update', 403);
    }

    const updated = await db.project.update({
      where: { id },
      data: {
        ...(name && { name }),
        ...(description !== undefined && { description }),
        ...(status && { status }),
        ...(startDate && { startDate: new Date(startDate) }),
        ...(endDate && { endDate: new Date(endDate) }),
      },
      include: { members: { include: { user: true } } },
    });

    sendSuccess(res, updated, 'Project updated');
  } catch (error) {
    next(error);
  }
}

export async function deleteProject(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const userId = req.userId;

    const project = await db.project.findUnique({ where: { id } });
    if (!project) {
      return sendError(res, 'Project not found', 404);
    }

    if (project.ownerId !== userId) {
      return sendError(res, 'Only project owner can delete', 403);
    }

    await db.project.delete({ where: { id } });
    sendSuccess(res, {}, 'Project deleted');
  } catch (error) {
    next(error);
  }
}

export async function getMembers(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;

    const members = await db.projectMember.findMany({
      where: { projectId: id },
      include: { user: { select: { id: true, name: true, email: true, avatar: true, role: true } } },
    });

    sendSuccess(res, members);
  } catch (error) {
    next(error);
  }
}

export async function addMember(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const userId = req.userId;
    const { memberId, role } = req.body;

    const project = await db.project.findUnique({ where: { id } });
    if (!project || project.ownerId !== userId) {
      return sendError(res, 'Access denied', 403);
    }

    const member = await db.projectMember.create({
      data: {
        projectId: id,
        userId: memberId,
        role: role || 'MEMBER',
      },
      include: { user: true },
    });

    sendSuccess(res, member, 'Member added', 201);
  } catch (error) {
    next(error);
  }
}

export async function removeMember(req: Request, res: Response, next: NextFunction) {
  try {
    const { id, userId } = req.params;
    const currentUserId = req.userId;

    const project = await db.project.findUnique({ where: { id } });
    if (!project || project.ownerId !== currentUserId) {
      return sendError(res, 'Access denied', 403);
    }

    await db.projectMember.deleteMany({
      where: { projectId: id, userId },
    });

    sendSuccess(res, {}, 'Member removed');
  } catch (error) {
    next(error);
  }
}
