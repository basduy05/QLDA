import { Router } from 'express';
import { authenticate } from '@/middleware/auth';
import {
  createProject,
  getProjects,
  getProjectById,
  updateProject,
  deleteProject,
  addMember,
  removeMember,
  getMembers,
} from '@/controllers/projectController';

const router = Router();

router.post('/', authenticate, createProject);
router.get('/', authenticate, getProjects);
router.get('/:id', authenticate, getProjectById);
router.put('/:id', authenticate, updateProject);
router.delete('/:id', authenticate, deleteProject);

// Members
router.get('/:id/members', authenticate, getMembers);
router.post('/:id/members', authenticate, addMember);
router.delete('/:id/members/:userId', authenticate, removeMember);

export default router;
