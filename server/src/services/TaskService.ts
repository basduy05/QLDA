import { db } from '@/utils/database';
import { BaseService } from '@/services/BaseService';
import { NotFoundError, ValidationError, AuthorizationError } from '@/middleware/errorHandler';

export class TaskService extends BaseService {
  async createTask(projectId: string, userId: string, data: any) {
    try {
      const { title, description, priority, dueDate, assignedTo } = data;

      if (!title) {
        throw new ValidationError('Task title is required');
      }

      // Verify user is member of project
      const membership = await db.projectMember.findFirst({
        where: { projectId, userId },
      });

      if (!membership) {
        throw new AuthorizationError('Not a member of this project');
      }

      const task = await db.task.create({
        data: {
          projectId,
          title,
          description,
          priority: priority || 'MEDIUM',
          dueDate: dueDate ? new Date(dueDate) : undefined,
          assignedTo,
        },
        include: {
          assignee: { select: { id: true, name: true, avatar: true } },
        },
      });

      return task;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getTasksByProject(projectId: string, filters: any = {}) {
    try {
      const { status, priority, assignedTo, page = 1, limit = 20 } = filters;

      const where: any = { projectId };

      if (status) where.status = status;
      if (priority) where.priority = priority;
      if (assignedTo) where.assignedTo = assignedTo;

      const [tasks, total] = await Promise.all([
        db.task.findMany({
          where,
          include: {
            assignee: { select: { id: true, name: true, avatar: true } },
            subtasks: { select: { id: true, title: true, isCompleted: true } },
            _count: { select: { comments: true } },
          },
          skip: (page - 1) * limit,
          take: limit,
          orderBy: { createdAt: 'desc' },
        }),
        db.task.count({ where }),
      ]);

      return { tasks, total };
    } catch (error) {
      this.handleError(error);
    }
  }

  async getTaskById(taskId: string) {
    try {
      const task = await db.task.findUnique({
        where: { id: taskId },
        include: {
          project: { select: { id: true, name: true } },
          assignee: { select: { id: true, name: true, avatar: true } },
          subtasks: { orderBy: { createdAt: 'asc' } },
          comments: {
            include: { author: { select: { id: true, name: true, avatar: true } } },
            orderBy: { createdAt: 'desc' },
          },
          attachments: true,
        },
      });

      if (!task) {
        throw new NotFoundError('Task not found');
      }

      return task;
    } catch (error) {
      this.handleError(error);
    }
  }

  async updateTask(taskId: string, data: any) {
    try {
      const task = await db.task.update({
        where: { id: taskId },
        data: {
          ...(data.title && { title: data.title }),
          ...(data.description !== undefined && { description: data.description }),
          ...(data.status && { status: data.status }),
          ...(data.priority && { priority: data.priority }),
          ...(data.dueDate && { dueDate: new Date(data.dueDate) }),
          ...(data.assignedTo !== undefined && { assignedTo: data.assignedTo }),
        },
      });

      return task;
    } catch (error) {
      this.handleError(error);
    }
  }

  async deleteTask(taskId: string) {
    try {
      await db.task.delete({ where: { id: taskId } });
      return { message: 'Task deleted' };
    } catch (error) {
      this.handleError(error);
    }
  }

  async addSubtask(taskId: string, title: string) {
    try {
      if (!title) {
        throw new ValidationError('Subtask title required');
      }

      const subtask = await db.subtask.create({
        data: { taskId, title },
      });

      return subtask;
    } catch (error) {
      this.handleError(error);
    }
  }

  async updateSubtask(subtaskId: string, data: any) {
    try {
      const subtask = await db.subtask.update({
        where: { id: subtaskId },
        data: {
          ...(data.title && { title: data.title }),
          ...(data.isCompleted !== undefined && { isCompleted: data.isCompleted }),
        },
      });

      return subtask;
    } catch (error) {
      this.handleError(error);
    }
  }

  async deleteSubtask(subtaskId: string) {
    try {
      await db.subtask.delete({ where: { id: subtaskId } });
      return { message: 'Subtask deleted' };
    } catch (error) {
      this.handleError(error);
    }
  }

  async addComment(taskId: string, userId: string, content: string) {
    try {
      if (!content) {
        throw new ValidationError('Comment content required');
      }

      const comment = await db.taskComment.create({
        data: {
          taskId,
          authorId: userId,
          content,
        },
        include: { author: { select: { id: true, name: true, avatar: true } } },
      });

      return comment;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getTaskComments(taskId: string, page: number = 1, limit: number = 10) {
    try {
      const [comments, total] = await Promise.all([
        db.taskComment.findMany({
          where: { taskId },
          include: { author: { select: { id: true, name: true, avatar: true } } },
          orderBy: { createdAt: 'desc' },
          skip: (page - 1) * limit,
          take: limit,
        }),
        db.taskComment.count({ where: { taskId } }),
      ]);

      return { comments, total };
    } catch (error) {
      this.handleError(error);
    }
  }
}

export const taskService = new TaskService();
