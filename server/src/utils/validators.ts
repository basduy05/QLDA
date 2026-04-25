export const validateEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
};

export const validatePassword = (password: string): string | null => {
  if (password.length < 6) {
    return 'Password must be at least 6 characters';
  }
  if (!/[A-Z]/.test(password)) {
    return 'Password must contain at least one uppercase letter';
  }
  if (!/[0-9]/.test(password)) {
    return 'Password must contain at least one number';
  }
  return null;
};

export const validateProjectName = (name: string): string | null => {
  if (!name || name.trim().length === 0) {
    return 'Project name is required';
  }
  if (name.length > 255) {
    return 'Project name must not exceed 255 characters';
  }
  return null;
};

export const validateTaskTitle = (title: string): string | null => {
  if (!title || title.trim().length === 0) {
    return 'Task title is required';
  }
  if (title.length > 500) {
    return 'Task title must not exceed 500 characters';
  }
  return null;
};

export const validatePriority = (priority: string): boolean => {
  return ['LOW', 'MEDIUM', 'HIGH', 'URGENT'].includes(priority);
};

export const validateStatus = (status: string): boolean => {
  return ['TODO', 'IN_PROGRESS', 'REVIEW', 'DONE', 'BLOCKED'].includes(status);
};

export const validateProjectRole = (role: string): boolean => {
  return ['LEAD', 'DEPUTY', 'MEMBER'].includes(role);
};
