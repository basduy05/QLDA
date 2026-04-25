export type User = {
  id: string;
  name: string;
  email: string;
  role?: string;
};

export type Project = {
  id: string;
  name: string;
  description?: string;
  status?: string;
  tasks_count?: number;
  owner?: User;
};

export type Task = {
  id: string;
  title: string;
  description?: string;
  status: "todo" | "in_progress" | "done";
  priority?: "low" | "medium" | "high";
  due_date?: string;
  project?: Project;
  assignee?: User;
};

export type Conversation = {
  id: string;
  user?: User;
  last_message?: string;
};

export type ApiListResponse<T> = {
  data: T[];
};
