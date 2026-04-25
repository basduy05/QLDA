import { Router } from 'express';
import { authenticate } from '@/middleware/auth';
import {
  createTask,
  getTasks,
  getTaskById,
  updateTask,
  deleteTask,
  addSubtask,
  updateSubtask,
  deleteSubtask,
  addComment,
  getComments,
} from '@/controllers/taskController';

const router = Router();

router.post('/', authenticate, createTask);
router.get('/', authenticate, getTasks);
router.get('/:id', authenticate, getTaskById);
router.put('/:id', authenticate, updateTask);
router.delete('/:id', authenticate, deleteTask);

// Subtasks
router.post('/:id/subtasks', authenticate, addSubtask);
router.put('/:id/subtasks/:subtaskId', authenticate, updateSubtask);
router.delete('/:id/subtasks/:subtaskId', authenticate, deleteSubtask);

// Comments
router.get('/:id/comments', authenticate, getComments);
router.post('/:id/comments', authenticate, addComment);

export default router;
