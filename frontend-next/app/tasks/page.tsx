"use client";

import { useEffect, useMemo, useState } from "react";
import ProtectedLayout from "@/components/ProtectedLayout";
import PageHeader from "@/components/PageHeader";
import { projectApi, taskApi } from "@/lib/api";
import type { Project, Task } from "@/types";

export default function TasksPage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [selectedProjectId, setSelectedProjectId] = useState("");
  const [tasks, setTasks] = useState<Task[]>([]);

  useEffect(() => {
    projectApi
      .list()
      .then((res) => {
        const list = res.data?.data ?? res.data ?? [];
        setProjects(list);
        if (list[0]?.id) setSelectedProjectId(list[0].id);
      })
      .catch(() => setProjects([]));
  }, []);

  useEffect(() => {
    if (!selectedProjectId) return;
    taskApi
      .listByProject(selectedProjectId)
      .then((res) => setTasks(res.data?.data ?? res.data ?? []))
      .catch(() => setTasks([]));
  }, [selectedProjectId]);

  const statusMap = useMemo(
    () => ({
      todo: "badge-todo",
      in_progress: "badge-progress",
      done: "badge-done",
    }),
    []
  );

  return (
    <ProtectedLayout>
      <PageHeader
        title="Tasks"
        subtitle="Project-scoped task list from Node API"
        actions={<button className="btn-primary">New Task</button>}
      />

      <section className="card overflow-hidden">
        <div className="flex flex-wrap items-center gap-3 border-b border-slate-200 bg-slate-50/70 p-4">
          <select
            className="field w-full max-w-xs"
            value={selectedProjectId}
            onChange={(e) => setSelectedProjectId(e.target.value)}
          >
            {projects.map((project) => (
              <option key={project.id} value={project.id}>
                {project.name}
              </option>
            ))}
          </select>
          <span className="text-sm text-slate-500">{tasks.length} tasks</span>
        </div>

        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
              <tr>
                <th className="px-5 py-3 text-left">Task</th>
                <th className="px-5 py-3 text-left">Status</th>
                <th className="px-5 py-3 text-left">Priority</th>
                <th className="px-5 py-3 text-left">Assignee</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 bg-white">
              {tasks.map((task) => (
                <tr key={task.id} className="hover:bg-cyan-50/50">
                  <td className="px-5 py-3.5">
                    <p className="font-semibold text-slate-900">{task.title}</p>
                    <p className="text-xs text-slate-500">{task.description || "No description"}</p>
                  </td>
                  <td className="px-5 py-3.5">
                    <span className={statusMap[task.status] || "badge-todo"}>{task.status}</span>
                  </td>
                  <td className="px-5 py-3.5 text-slate-700">{task.priority ?? "medium"}</td>
                  <td className="px-5 py-3.5 text-slate-700">{task.assignee?.name ?? "Unassigned"}</td>
                </tr>
              ))}
              {!tasks.length ? (
                <tr>
                  <td colSpan={4} className="px-5 py-14 text-center text-slate-500">
                    No task data for this project.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </section>
    </ProtectedLayout>
  );
}
