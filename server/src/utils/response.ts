import { Response } from 'express';

export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
}

export function sendSuccess<T>(
  res: Response,
  data: T,
  message: string = 'Success',
  status: number = 200
): Response {
  return res.status(status).json({
    success: true,
    data,
    message,
  });
}

export function sendError(
  res: Response,
  error: string,
  status: number = 400,
  data?: any
): Response {
  return res.status(status).json({
    success: false,
    error,
    data,
  });
}

export function sendPaginated<T>(
  res: Response,
  data: T[],
  total: number,
  page: number,
  limit: number,
  status: number = 200
): Response {
  const totalPages = Math.ceil(total / limit);
  return res.status(status).json({
    success: true,
    data,
    pagination: {
      total,
      page,
      limit,
      totalPages,
      hasNext: page < totalPages,
      hasPrev: page > 1,
    },
  });
}
