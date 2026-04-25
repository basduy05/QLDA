import { Router, Request, Response, NextFunction } from 'express';
import { body, validationResult } from 'express-validator';
import { authenticate } from '@/middleware/auth';
import { 
  register, 
  login, 
  logout, 
  refreshToken,
  verifyOtp,
} from '@/controllers/authController';

const router = Router();

// Validation middleware
const validateEmail = body('email').isEmail().normalizeEmail();
const validatePassword = body('password').isLength({ min: 6 });

// Error handling wrapper
const asyncHandler = (fn: Function) => (req: Request, res: Response, next: NextFunction) => {
  Promise.resolve(fn(req, res, next)).catch(next);
};

router.post(
  '/register',
  validateEmail,
  validatePassword,
  body('name').notEmpty(),
  asyncHandler(register)
);

router.post(
  '/login',
  validateEmail,
  validatePassword,
  asyncHandler(login)
);

router.post('/verify-otp', body('email').isEmail(), asyncHandler(verifyOtp));

router.post('/refresh-token', asyncHandler(refreshToken));

router.post('/logout', authenticate, asyncHandler(logout));

export default router;
