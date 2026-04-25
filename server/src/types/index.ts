// Types for the application
export interface JWTPayload {
  id: string;
  email: string;
  role: string;
  iat?: number;
  exp?: number;
}

export interface ApiErrorResponse {
  success: false;
  error: {
    code: string;
    message: string;
    status: number;
  };
}

export interface ApiSuccessResponse<T = any> {
  success: true;
  data?: T;
  message?: string;
}

export interface PaginationMeta {
  total: number;
  page: number;
  limit: number;
  totalPages: number;
  hasNext: boolean;
  hasPrev: boolean;
}

export interface PaginatedResponse<T = any> {
  success: true;
  data: T[];
  pagination: PaginationMeta;
}

// User related
export interface UserDTO {
  id: string;
  email: string;
  name: string;
  avatar?: string;
  role: 'ADMIN' | 'USER';
  isOnline: boolean;
  lastSeenAt?: Date;
  createdAt: Date;
}

// Project related
export type ProjectRole = 'LEAD' | 'DEPUTY' | 'MEMBER';

export interface ProjectDTO {
  id: string;
  name: string;
  description?: string;
  status: string;
  startDate?: Date;
  endDate?: Date;
  ownerId: string;
  createdAt: Date;
  updatedAt: Date;
}

// Task related
export type TaskStatus = 'TODO' | 'IN_PROGRESS' | 'REVIEW' | 'DONE' | 'BLOCKED';
export type TaskPriority = 'LOW' | 'MEDIUM' | 'HIGH' | 'URGENT';

export interface TaskDTO {
  id: string;
  title: string;
  description?: string;
  status: TaskStatus;
  priority: TaskPriority;
  dueDate?: Date;
  projectId: string;
  assignedTo?: string;
  createdAt: Date;
  updatedAt: Date;
}
