import { Request, Response, NextFunction } from 'express';
import { db } from '@/utils/database';
import { sendSuccess, sendError, sendPaginated } from '@/utils/response';

export async function createTask(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = req.userId;
    const { projectId, title, description, priority, dueDate, assignedTo } = req.body;

    if (!projectId || !title) {
      return sendError(res, 'Project ID and title required', 400);
    }

    // Verify user is member of project
    const membership = await db.projectMember.findFirst({
      where: { projectId, userId },
    });

    if (!membership) {
      return sendError(res, 'Not a member of this project', 403);
    }

    const task = await db.task.create({
      data: {
        projectId,
        title,
        description,
        priority,
        dueDate: dueDate ? new Date(dueDate) : undefined,
        assignedTo,
      },
    });

    sendSuccess(res, task, 'Task created', 201);
  } catch (error) {
    next(error);
  }
}

export async function getTasks(req: Request, res: Response, next: NextFunction) {
  try {
    const { page = 1, limit = 20, projectId, status, assignedTo } = req.query;

    const where: any = {};
    if (projectId) where.projectId = projectId as string;
    if (status) where.status = status as string;
    if (assignedTo) where.assignedTo = assignedTo as string;

    const [tasks, total] = await Promise.all([
      db.task.findMany({
        where,
        include: {
          project: { select: { id: true, name: true } },
          assignee: { select: { id: true, name: true, avatar: true } },
          subtasks: true,
          _count: { select: { comments: true } },
        },
        skip: (Number(page) - 1) * Number(limit),
        take: Number(limit),
      }),
      db.task.count({ where }),
    ]);

    sendPaginated(res, tasks, total, Number(page), Number(limit));
  } catch (error) {
    next(error);
  }
}

export async function getTaskById(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;

    const task = await db.task.findUnique({
      where: { id },
      include: {
        project: { select: { id: true, name: true } },
        assignee: { select: { id: true, name: true, avatar: true } },
        subtasks: true,
        comments: { include: { author: { select: { id: true, name: true, avatar: true } } } },
        attachments: true,
      },
    });

    if (!task) {
      return sendError(res, 'Task not found', 404);
    }

    sendSuccess(res, task);
  } catch (error) {
    next(error);
  }
}

export async function updateTask(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const { title, description, status, priority, dueDate, assignedTo } = req.body;

    const task = await db.task.update({
      where: { id },
      data: {
        ...(title && { title }),
        ...(description !== undefined && { description }),
        ...(status && { status }),
        ...(priority && { priority }),
        ...(dueDate && { dueDate: new Date(dueDate) }),
        ...(assignedTo !== undefined && { assignedTo }),
      },
    });

    sendSuccess(res, task, 'Task updated');
  } catch (error) {
    next(error);
  }
}

export async function deleteTask(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;

    await db.task.delete({ where: { id } });
    sendSuccess(res, {}, 'Task deleted');
  } catch (error) {
    next(error);
  }
}

export async function addSubtask(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const { title } = req.body;

    if (!title) {
      return sendError(res, 'Title required', 400);
    }

    const subtask = await db.subtask.create({
      data: {
        taskId: id,
        title,
      },
    });

    sendSuccess(res, subtask, 'Subtask created', 201);
  } catch (error) {
    next(error);
  }
}

export async function updateSubtask(req: Request, res: Response, next: NextFunction) {
  try {
    const { id, subtaskId } = req.params;
    const { title, isCompleted } = req.body;

    const subtask = await db.subtask.update({
      where: { id: subtaskId },
      data: {
        ...(title && { title }),
        ...(isCompleted !== undefined && { isCompleted }),
      },
    });

    sendSuccess(res, subtask, 'Subtask updated');
  } catch (error) {
    next(error);
  }
}

export async function deleteSubtask(req: Request, res: Response, next: NextFunction) {
  try {
    const { subtaskId } = req.params;

    await db.subtask.delete({ where: { id: subtaskId } });
    sendSuccess(res, {}, 'Subtask deleted');
  } catch (error) {
    next(error);
  }
}

export async function addComment(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const userId = req.userId;
    const { content } = req.body;

    if (!content) {
      return sendError(res, 'Comment content required', 400);
    }

    const comment = await db.taskComment.create({
      data: {
        taskId: id,
        authorId: userId,
        content,
      },
      include: { author: { select: { id: true, name: true, avatar: true } } },
    });

    sendSuccess(res, comment, 'Comment added', 201);
  } catch (error) {
    next(error);
  }
}

export async function getComments(req: Request, res: Response, next: NextFunction) {
  try {
    const { id } = req.params;
    const { page = 1, limit = 10 } = req.query;

    const [comments, total] = await Promise.all([
      db.taskComment.findMany({
        where: { taskId: id },
        include: { author: { select: { id: true, name: true, avatar: true } } },
        orderBy: { createdAt: 'desc' },
        skip: (Number(page) - 1) * Number(limit),
        take: Number(limit),
      }),
      db.taskComment.count({ where: { taskId: id } }),
    ]);

    sendPaginated(res, comments, total, Number(page), Number(limit));
  } catch (error) {
    next(error);
  }
}
