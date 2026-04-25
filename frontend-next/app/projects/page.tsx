"use client";

import { useEffect, useMemo, useState } from "react";
import ProtectedLayout from "@/components/ProtectedLayout";
import PageHeader from "@/components/PageHeader";
import { projectApi } from "@/lib/api";
import type { Project } from "@/types";

export default function ProjectsPage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [query, setQuery] = useState("");

  useEffect(() => {
    projectApi
      .list()
      .then((res) => setProjects(res.data?.data ?? res.data ?? []))
      .catch(() => setProjects([]));
  }, []);

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return projects;
    return projects.filter((p) => [p.name, p.description, p.owner?.name].join(" ").toLowerCase().includes(q));
  }, [projects, query]);

  return (
    <ProtectedLayout>
      <PageHeader
        title="Projects"
        subtitle="Unified and optimized list view in Next.js"
        actions={<button className="btn-primary" type="button">New Project</button>}
      />

      <div className="card overflow-hidden">
        <div className="border-b border-slate-200 bg-slate-50/70 p-4">
          <input
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            className="field max-w-sm"
            placeholder="Search projects..."
          />
        </div>

        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
              <tr>
                <th className="px-5 py-3 text-left">Project</th>
                <th className="px-5 py-3 text-left">Owner</th>
                <th className="px-5 py-3 text-left">Status</th>
                <th className="px-5 py-3 text-left">Tasks</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 bg-white">
              {filtered.map((project) => (
                <tr key={project.id} className="hover:bg-cyan-50/50">
                  <td className="px-5 py-3.5">
                    <p className="font-semibold text-slate-900">{project.name}</p>
                    <p className="text-xs text-slate-500">{project.description || "No description"}</p>
                  </td>
                  <td className="px-5 py-3.5 text-slate-700">{project.owner?.name ?? "-"}</td>
                  <td className="px-5 py-3.5">
                    <span className="badge border-slate-200 bg-slate-100 text-slate-700">{project.status ?? "unknown"}</span>
                  </td>
                  <td className="px-5 py-3.5 text-slate-700">{project.tasks_count ?? 0}</td>
                </tr>
              ))}

              {!filtered.length ? (
                <tr>
                  <td colSpan={4} className="px-5 py-14 text-center text-slate-500">
                    No projects matched your search.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      </div>
    </ProtectedLayout>
  );
}
