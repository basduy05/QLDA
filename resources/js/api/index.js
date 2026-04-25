import apiClient from './client';

export const authAPI = {
    // Register new user
    register: (data) => apiClient.post('/auth/register', data),

    // Login user
    login: (email, password) => apiClient.post('/auth/login', { email, password }),

    // Verify OTP
    verifyOtp: (email, code) => apiClient.post('/auth/verify-otp', { email, code }),

    // Refresh token
    refreshToken: (refreshToken) => apiClient.post('/auth/refresh-token', { refresh_token: refreshToken }),

    // Logout
    logout: () => apiClient.post('/auth/logout'),

    // Resend OTP
    resendOtp: (email) => apiClient.post('/auth/resend-otp', { email }),

    // Check if email exists
    checkEmail: (email) => apiClient.get(`/auth/check-email?email=${email}`),
};

export const userAPI = {
    // Get current user profile
    getProfile: () => apiClient.get('/users/profile'),

    // Update user profile
    updateProfile: (data) => apiClient.put('/users/profile', data),

    // Get all users
    getAllUsers: () => apiClient.get('/users'),

    // Get user by ID
    getUser: (id) => apiClient.get(`/users/${id}`),

    // Update user role
    updateRole: (userId, role) => apiClient.put(`/users/${userId}/role`, { role }),

    // Get users for project
    getUsersForProject: (projectId) => apiClient.get(`/users/project/${projectId}`),

    // Upload avatar
    uploadAvatar: (file) => {
        const formData = new FormData();
        formData.append('avatar', file);
        return apiClient.post('/users/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    },

    // Delete account
    deleteAccount: () => apiClient.delete('/users/account'),
};

export const projectAPI = {
    // Create project
    create: (data) => apiClient.post('/projects', data),

    // Get all projects
    getAll: () => apiClient.get('/projects'),

    // Get project by ID
    getById: (id) => apiClient.get(`/projects/${id}`),

    // Update project
    update: (id, data) => apiClient.put(`/projects/${id}`, data),

    // Delete project
    delete: (id) => apiClient.delete(`/projects/${id}`),

    // Get project members
    getMembers: (id) => apiClient.get(`/projects/${id}/members`),

    // Add member to project
    addMember: (projectId, userId, role = 'member') => 
        apiClient.post(`/projects/${projectId}/members`, { user_id: userId, role }),

    // Remove member from project
    removeMember: (projectId, userId) => 
        apiClient.delete(`/projects/${projectId}/members/${userId}`),

    // Update member role
    updateMemberRole: (projectId, userId, role) => 
        apiClient.put(`/projects/${projectId}/members/${userId}`, { role }),

    // Export project
    exportProject: (id, format = 'xlsx') => 
        apiClient.get(`/projects/${id}/export?format=${format}`, { responseType: 'blob' }),
};

export const taskAPI = {
    // Create task
    create: (projectId, data) => apiClient.post(`/projects/${projectId}/tasks`, data),

    // Get tasks by project
    getByProject: (projectId) => apiClient.get(`/projects/${projectId}/tasks`),

    // Get task by ID
    getById: (id) => apiClient.get(`/tasks/${id}`),

    // Update task
    update: (id, data) => apiClient.put(`/tasks/${id}`, data),

    // Delete task
    delete: (id) => apiClient.delete(`/tasks/${id}`),

    // Change task status
    changeStatus: (id, status) => apiClient.patch(`/tasks/${id}/status`, { status }),

    // Assign task to user
    assign: (id, userId) => apiClient.patch(`/tasks/${id}/assign`, { user_id: userId }),

    // Add subtask
    addSubtask: (taskId, data) => apiClient.post(`/tasks/${taskId}/subtasks`, data),

    // Update subtask
    updateSubtask: (taskId, subtaskId, data) => 
        apiClient.put(`/tasks/${taskId}/subtasks/${subtaskId}`, data),

    // Delete subtask
    deleteSubtask: (taskId, subtaskId) => 
        apiClient.delete(`/tasks/${taskId}/subtasks/${subtaskId}`),

    // Add comment
    addComment: (taskId, comment) => apiClient.post(`/tasks/${taskId}/comments`, { comment }),

    // Get comments
    getComments: (taskId) => apiClient.get(`/tasks/${taskId}/comments`),

    // Delete comment
    deleteComment: (taskId, commentId) => 
        apiClient.delete(`/tasks/${taskId}/comments/${commentId}`),

    // Upload attachment
    uploadAttachment: (taskId, file) => {
        const formData = new FormData();
        formData.append('file', file);
        return apiClient.post(`/tasks/${taskId}/attachments`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    },

    // Delete attachment
    deleteAttachment: (taskId, attachmentId) => 
        apiClient.delete(`/tasks/${taskId}/attachments/${attachmentId}`),

    // Export tasks
    exportTasks: (projectId, format = 'xlsx') => 
        apiClient.get(`/projects/${projectId}/tasks/export?format=${format}`, { responseType: 'blob' }),
};

export const messageAPI = {
    // Send direct message
    sendDirectMessage: (conversationId, message) => 
        apiClient.post(`/conversations/${conversationId}/messages`, { message }),

    // Get conversation messages
    getConversationMessages: (conversationId) => 
        apiClient.get(`/conversations/${conversationId}/messages`),

    // Get all conversations
    getConversations: () => apiClient.get('/conversations'),

    // Create conversation
    createConversation: (userId) => apiClient.post('/conversations', { user_id: userId }),

    // Mark message as read
    markAsRead: (conversationId, messageId) => 
        apiClient.patch(`/conversations/${conversationId}/messages/${messageId}/read`),

    // Delete message
    deleteMessage: (conversationId, messageId) => 
        apiClient.delete(`/conversations/${conversationId}/messages/${messageId}`),

    // Send group message
    sendGroupMessage: (groupId, message) => 
        apiClient.post(`/chat-groups/${groupId}/messages`, { message }),

    // Get group messages
    getGroupMessages: (groupId) => apiClient.get(`/chat-groups/${groupId}/messages`),

    // Create chat group
    createChatGroup: (data) => apiClient.post('/chat-groups', data),

    // Get chat groups
    getChatGroups: () => apiClient.get('/chat-groups'),

    // Add member to group
    addGroupMember: (groupId, userId) => 
        apiClient.post(`/chat-groups/${groupId}/members`, { user_id: userId }),

    // Remove member from group
    removeGroupMember: (groupId, userId) => 
        apiClient.delete(`/chat-groups/${groupId}/members/${userId}`),
};

export const notificationAPI = {
    // Get notifications
    getNotifications: () => apiClient.get('/notifications'),

    // Get unread notifications count
    getUnreadCount: () => apiClient.get('/notifications/unread/count'),

    // Mark as read
    markAsRead: (notificationId) => 
        apiClient.patch(`/notifications/${notificationId}/read`),

    // Mark all as read
    markAllAsRead: () => apiClient.patch('/notifications/read-all'),

    // Delete notification
    delete: (notificationId) => apiClient.delete(`/notifications/${notificationId}`),

    // Delete all notifications
    deleteAll: () => apiClient.delete('/notifications/delete-all'),
};

export const reportAPI = {
    // Get project reports
    getProjectReport: (projectId, startDate, endDate) => 
        apiClient.get(`/projects/${projectId}/reports`, { 
            params: { start_date: startDate, end_date: endDate } 
        }),

    // Get task reports
    getTaskReport: (projectId, startDate, endDate) => 
        apiClient.get(`/projects/${projectId}/task-reports`, { 
            params: { start_date: startDate, end_date: endDate } 
        }),

    // Get user activity report
    getUserActivityReport: (userId, startDate, endDate) => 
        apiClient.get(`/users/${userId}/activity-report`, { 
            params: { start_date: startDate, end_date: endDate } 
        }),

    // Export report
    exportReport: (type, projectId, startDate, endDate, format = 'xlsx') => 
        apiClient.get(`/reports/export`, { 
            params: { type, project_id: projectId, start_date: startDate, end_date: endDate, format },
            responseType: 'blob'
        }),
};

export const aiAPI = {
    // Get AI messages
    getMessages: () => apiClient.get('/ai/messages'),

    // Send message to AI
    sendMessage: (message) => apiClient.post('/ai/messages', { message }),

    // Get AI insights
    getInsights: (projectId) => apiClient.get(`/ai/insights/${projectId}`),

    // Generate report
    generateReport: (projectId, type = 'weekly') => 
        apiClient.post(`/ai/report`, { project_id: projectId, type }),
};

export default {
    authAPI,
    userAPI,
    projectAPI,
    taskAPI,
    messageAPI,
    notificationAPI,
    reportAPI,
    aiAPI,
};
