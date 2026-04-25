import { apiClient } from "./api-client";

export const authApi = {
  login: (email: string, password: string) => apiClient.post("/auth/login", { email, password }),
  register: (payload: Record<string, unknown>) => apiClient.post("/auth/register", payload),
  getProfile: () => apiClient.get("/users/profile"),
};

export const projectApi = {
  list: () => apiClient.get("/projects"),
  detail: (id: string) => apiClient.get(`/projects/${id}`),
  create: (payload: Record<string, unknown>) => apiClient.post("/projects", payload),
};

export const taskApi = {
  listByProject: (projectId: string) => apiClient.get(`/projects/${projectId}/tasks`),
  detail: (id: string) => apiClient.get(`/tasks/${id}`),
};

export const userApi = {
  list: () => apiClient.get("/users"),
};

export const messageApi = {
  conversations: () => apiClient.get("/conversations"),
  conversationMessages: (conversationId: string) => apiClient.get(`/conversations/${conversationId}/messages`),
  sendDirect: (conversationId: string, message: string) =>
    apiClient.post(`/conversations/${conversationId}/messages`, { message }),
  groups: () => apiClient.get("/chat-groups"),
  groupMessages: (groupId: string) => apiClient.get(`/chat-groups/${groupId}/messages`),
  sendGroup: (groupId: string, message: string) => apiClient.post(`/chat-groups/${groupId}/messages`, { message }),
};
