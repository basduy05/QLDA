import { db } from '@/utils/database';
import { BaseService } from '@/services/BaseService';
import { NotFoundError, ValidationError, AuthorizationError } from '@/middleware/errorHandler';

export class ProjectService extends BaseService {
  async createProject(userId: string, data: any) {
    try {
      const { name, description, startDate, endDate } = data;

      if (!name) {
        throw new ValidationError('Project name is required');
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
              userId,
              role: 'LEAD',
            },
          },
        },
        include: {
          members: { include: { user: { select: { id: true, name: true, email: true, avatar: true } } } },
          owner: { select: { id: true, name: true, avatar: true } },
        },
      });

      return project;
    } catch (error) {
      this.handleError(error);
    }
  }

  async getProjectsByUser(userId: string, filters: any = {}) {
    try {
      const { status = 'active', page = 1, limit = 10 } = filters;

      const [projects, total] = await Promise.all([
        db.project.findMany({
          where: {
            OR: [
              { ownerId: userId },
              { members: { some: { userId } } },
            ],
            ...(status && { status }),
          },
          include: {
            owner: { select: { id: true, name: true, avatar: true } },
            members: { include: { user: { select: { id: true, name: true, avatar: true } } } },
            _count: { select: { tasks: true, members: true } },
          },
          skip: (page - 1) * limit,
          take: limit,
          orderBy: { updatedAt: 'desc' },
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

      return { projects, total };
    } catch (error) {
      this.handleError(error);
    }
  }

  async getProjectById(projectId: string, userId: string) {
    try {
      const project = await db.project.findUnique({
        where: { id: projectId },
        include: {
          owner: { select: { id: true, name: true, avatar: true } },
          members: { 
            include: { 
              user: { select: { id: true, name: true, email: true, avatar: true, role: true } } 
            } 
          },
          tasks: {
            select: {
              id: true,
              title: true,
              status: true,
              priority: true,
              _count: { select: { subtasks: true, comments: true } },
            },
            orderBy: { createdAt: 'desc' },
            take: 5,
          },
          _count: { select: { tasks: true } },
        },
      });

      if (!project) {
        throw new NotFoundError('Project not found');
      }

      // Check access
      const isMember = project.members.some(m => m.userId === userId) || project.ownerId === userId;
      if (!isMember) {
        throw new AuthorizationError('Access denied to this project');
      }

      return project;
    } catch (error) {
      this.handleError(error);
    }
  }

  async updateProject(projectId: string, userId: string, data: any) {
    try {
      const project = await db.project.findUnique({ where: { id: projectId } });

      if (!project) {
        throw new NotFoundError('Project not found');
      }

      if (project.ownerId !== userId) {
        throw new AuthorizationError('Only project owner can update');
      }

      const updated = await db.project.update({
        where: { id: projectId },
        data: {
          ...(data.name && { name: data.name }),
          ...(data.description !== undefined && { description: data.description }),
          ...(data.status && { status: data.status }),
          ...(data.startDate && { startDate: new Date(data.startDate) }),
          ...(data.endDate && { endDate: new Date(data.endDate) }),
        },
        include: { members: { include: { user: true } } },
      });

      return updated;
    } catch (error) {
      this.handleError(error);
    }
  }

  async deleteProject(projectId: string, userId: string) {
    try {
      const project = await db.project.findUnique({ where: { id: projectId } });

      if (!project) {
        throw new NotFoundError('Project not found');
      }

      if (project.ownerId !== userId) {
        throw new AuthorizationError('Only project owner can delete');
      }

      await db.project.delete({ where: { id: projectId } });

      return { message: 'Project deleted successfully' };
    } catch (error) {
      this.handleError(error);
    }
  }

  async addMember(projectId: string, userId: string, memberId: string, role: string = 'MEMBER') {
    try {
      const project = await db.project.findUnique({ where: { id: projectId } });

      if (!project || project.ownerId !== userId) {
        throw new AuthorizationError('Access denied');
      }

      // Check if member already exists
      const existingMember = await db.projectMember.findFirst({
        where: { projectId, userId: memberId },
      });

      if (existingMember) {
        throw new ValidationError('User is already a member of this project');
      }

      const member = await db.projectMember.create({
        data: {
          projectId,
          userId: memberId,
          role: role as any,
        },
        include: { user: { select: { id: true, name: true, email: true, avatar: true } } },
      });

      return member;
    } catch (error) {
      this.handleError(error);
    }
  }

  async removeMember(projectId: string, userId: string, memberId: string) {
    try {
      const project = await db.project.findUnique({ where: { id: projectId } });

      if (!project || project.ownerId !== userId) {
        throw new AuthorizationError('Access denied');
      }

      await db.projectMember.deleteMany({
        where: { projectId, userId: memberId },
      });

      return { message: 'Member removed' };
    } catch (error) {
      this.handleError(error);
    }
  }

  async getProjectMembers(projectId: string) {
    try {
      const members = await db.projectMember.findMany({
        where: { projectId },
        include: {
          user: { select: { id: true, name: true, email: true, avatar: true, role: true, isOnline: true } },
        },
        orderBy: { role: 'asc' },
      });

      return members;
    } catch (error) {
      this.handleError(error);
    }
  }
}

export const projectService = new ProjectService();
