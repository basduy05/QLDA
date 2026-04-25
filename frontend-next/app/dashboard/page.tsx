"use client";

import { useEffect, useMemo, useState } from "react";
import ProtectedLayout from "@/components/ProtectedLayout";
import PageHeader from "@/components/PageHeader";
import { projectApi } from "@/lib/api";
import type { Project } from "@/types";

export default function DashboardPage() {
  const [projects, setProjects] = useState<Project[]>([]);

  useEffect(() => {
    projectApi
      .list()
      .then((res) => setProjects(res.data?.data ?? res.data ?? []))
      .catch(() => setProjects([]));
  }, []);

  const stats = useMemo(() => {
    const total = projects.length;
    const active = projects.filter((p) => p.status === "active").length;
    const planning = projects.filter((p) => p.status === "planning").length;
    const completed = projects.filter((p) => p.status === "completed").length;
    return { total, active, planning, completed };
  }, [projects]);

  return (
    <ProtectedLayout>
      <PageHeader
        title="Dashboard"
        subtitle="Overview from Next.js frontend connected to Node API"
        actions={<a href="/projects" className="btn-primary">Open Projects</a>}
      />

      <section className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <Kpi title="Total Projects" value={stats.total} tone="cyan" />
        <Kpi title="Active" value={stats.active} tone="emerald" />
        <Kpi title="Planning" value={stats.planning} tone="amber" />
        <Kpi title="Completed" value={stats.completed} tone="slate" />
      </section>

      <section className="card mt-5 overflow-hidden">
        <div className="border-b border-slate-200 px-5 py-3.5">
          <h2 className="font-display text-lg font-semibold text-slate-900">Recent Projects</h2>
        </div>
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
              <tr>
                <th className="px-5 py-3 text-left">Name</th>
                <th className="px-5 py-3 text-left">Status</th>
                <th className="px-5 py-3 text-left">Owner</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 bg-white">
              {projects.slice(0, 8).map((project) => (
                <tr key={project.id} className="hover:bg-cyan-50/50">
                  <td className="px-5 py-3.5 font-medium text-slate-800">{project.name}</td>
                  <td className="px-5 py-3.5">
                    <span className="badge border-slate-200 bg-slate-100 text-slate-700">{project.status ?? "unknown"}</span>
                  </td>
                  <td className="px-5 py-3.5 text-slate-600">{project.owner?.name ?? "-"}</td>
                </tr>
              ))}
              {!projects.length ? (
                <tr>
                  <td colSpan={3} className="px-5 py-12 text-center text-slate-500">
                    No project data received yet.
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

function Kpi({ title, value, tone }: { title: string; value: number; tone: "cyan" | "emerald" | "amber" | "slate" }) {
  const toneClass = {
    cyan: "from-cyan-500 to-cyan-600",
    emerald: "from-emerald-500 to-emerald-600",
    amber: "from-amber-500 to-amber-600",
    slate: "from-slate-600 to-slate-700",
  }[tone];

  return (
    <article className="card p-4">
      <p className="text-xs uppercase tracking-wider text-slate-500">{title}</p>
      <div className="mt-2 flex items-end justify-between">
        <p className="font-display text-3xl font-semibold text-slate-900">{value}</p>
        <span className={`h-2.5 w-20 rounded-full bg-gradient-to-r ${toneClass}`} />
      </div>
    </article>
  );
}
