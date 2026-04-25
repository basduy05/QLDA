import { Router } from 'express';
import { authenticate } from '@/middleware/auth';
import {
  getProfile,
  updateProfile,
  getUsers,
  getUserById,
  updateUserRole,
} from '@/controllers/userController';

const router = Router();

router.get('/me', authenticate, getProfile);
router.put('/me', authenticate, updateProfile);

router.get('/', authenticate, getUsers);
router.get('/:id', authenticate, getUserById);

// Admin only
router.put('/:id/role', authenticate, updateUserRole);

export default router;
