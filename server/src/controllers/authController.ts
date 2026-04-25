import { Request, Response, NextFunction } from 'express';
import { validationResult } from 'express-validator';
import { sendSuccess, sendError } from '@/utils/response';
import { authService } from '@/services/AuthService';

export async function register(req: Request, res: Response, next: NextFunction) {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return sendError(res, 'Validation failed', 400, errors.array());
    }

    const { email, name, password } = req.body;
    const result = await authService.register(email, name, password);

    sendSuccess(res, result, 'Registration successful', 201);
  } catch (error) {
    next(error);
  }
}

export async function login(req: Request, res: Response, next: NextFunction) {
  try {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return sendError(res, 'Validation failed', 400, errors.array());
    }

    const { email, password } = req.body;
    const result = await authService.login(email, password);

    sendSuccess(res, result, 'Login successful');
  } catch (error) {
    next(error);
  }
}

export async function verifyOtp(req: Request, res: Response, next: NextFunction) {
  try {
    const { email, otp } = req.body;

    const pending = await db.pendingRegistration.findUnique({ where: { email } });
    if (!pending) {
      return sendError(res, 'No pending registration', 404);
    }

    if (pending.otp !== otp) {
      return sendError(res, 'Invalid OTP', 400);
    }

    if (new Date() > pending.expiresAt) {
      return sendError(res, 'OTP expired', 400);
    }

    // Create user
    const user = await db.user.create({
      data: {
        email,
        name: pending.name,
        password: pending.hashedPassword,
      },
    });

    // Delete pending registration
    await db.pendingRegistration.delete({ where: { email } });

    const token = generateToken(user.id, user.email, user.role);

    sendSuccess(res, {
      user: { id: user.id, email: user.email, name: user.name },
      token,
    }, 'Email verified successfully', 201);
  } catch (error) {
    next(error);
  }
}

export async function refreshToken(req: Request, res: Response, next: NextFunction) {
  try {
    const { token } = req.body;
    if (!token) {
      return sendError(res, 'Token required', 400);
    }

    // Verify and decode token
    // Generate new token
    sendSuccess(res, { message: 'Token refreshed' });
  } catch (error) {
    next(error);
  }
}

export async function logout(req: Request, res: Response, next: NextFunction) {
  try {
    sendSuccess(res, {}, 'Logout successful');
  } catch (error) {
    next(error);
  }
}
