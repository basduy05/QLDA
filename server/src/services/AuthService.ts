import { db } from '@/utils/database';
import { BaseService } from '@/services/BaseService';
import { generateToken, hashPassword, comparePassword } from '@/utils/auth';
import { NotFoundError, ValidationError, AuthenticationError } from '@/middleware/errorHandler';

export class AuthService extends BaseService {
  async register(email: string, name: string, password: string) {
    try {
      // Check if user exists
      const existingUser = await db.user.findUnique({ where: { email } });
      if (existingUser) {
        throw new ValidationError('Email already registered');
      }

      // Hash password
      const hashedPassword = await hashPassword(password);

      // Create user
      const user = await db.user.create({
        data: {
          email,
          name,
          password: hashedPassword,
        },
      });

      // Generate token
      const token = generateToken(user.id, user.email, user.role);

      return {
        user: {
          id: user.id,
          email: user.email,
          name: user.name,
          role: user.role,
        },
        token,
      };
    } catch (error) {
      this.handleError(error);
    }
  }

  async login(email: string, password: string) {
    try {
      const user = await db.user.findUnique({ where: { email } });
      if (!user) {
        throw new AuthenticationError('Invalid credentials');
      }

      const isPasswordValid = await comparePassword(password, user.password);
      if (!isPasswordValid) {
        throw new AuthenticationError('Invalid credentials');
      }

      const token = generateToken(user.id, user.email, user.role);

      // Update last seen
      await db.user.update({
        where: { id: user.id },
        data: { lastSeenAt: new Date(), isOnline: true },
      });

      return {
        user: {
          id: user.id,
          email: user.email,
          name: user.name,
          role: user.role,
          avatar: user.avatar,
          isOnline: true,
        },
        token,
      };
    } catch (error) {
      this.handleError(error);
    }
  }

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
}

export const authService = new AuthService();
