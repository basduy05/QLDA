"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { PropsWithChildren, useEffect, useMemo, useState } from "react";
import { authApi } from "@/lib/api";
import { clearSession, getToken } from "@/lib/storage";
import type { User } from "@/types";

const navItems = [
  { href: "/dashboard", label: "Dashboard" },
  { href: "/projects", label: "Projects" },
  { href: "/tasks", label: "Tasks" },
  { href: "/messenger", label: "Messenger" },
];

export default function ProtectedLayout({ children }: PropsWithChildren) {
  const router = useRouter();
  const pathname = usePathname();
  const [isReady, setIsReady] = useState(false);
  const [profile, setProfile] = useState<User | null>(null);

  useEffect(() => {
    const token = getToken();
    if (!token) {
      router.replace("/login");
      return;
    }

    authApi
      .getProfile()
      .then((res) => setProfile(res.data?.data ?? res.data?.user ?? null))
      .catch(() => {
        clearSession();
        router.replace("/login");
      })
      .finally(() => setIsReady(true));
  }, [router]);

  const initials = useMemo(() => {
    if (!profile?.name) return "AP";
    return profile.name
      .split(" ")
      .slice(0, 2)
      .map((s) => s[0]?.toUpperCase() ?? "")
      .join("");
  }, [profile]);

  const handleLogout = () => {
    clearSession();
    router.replace("/login");
  };

  if (!isReady) {
    return (
      <div className="grid min-h-screen place-items-center text-slate-500">
        Loading workspace...
      </div>
    );
  }

  return (
    <div className="app-shell">
      <aside className="app-sidebar p-4 lg:p-5">
        <div className="mb-6 flex items-center gap-3">
          <div className="grid h-10 w-10 place-items-center rounded-xl bg-cyan-600 font-display text-sm font-bold text-white">
            AP
          </div>
          <div>
            <p className="font-display text-base font-semibold text-slate-900">Aperlex Next</p>
            <p className="text-xs text-slate-500">Node API frontend</p>
          </div>
        </div>

        <nav className="space-y-1">
          {navItems.map((item) => {
            const isActive = pathname?.startsWith(item.href);
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`block rounded-xl px-3 py-2.5 text-sm font-medium transition ${
                  isActive
                    ? "bg-cyan-50 text-cyan-700"
                    : "text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                }`}
              >
                {item.label}
              </Link>
            );
          })}
        </nav>

        <div className="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-3">
          <div className="mb-2 flex items-center gap-2">
            <div className="grid h-8 w-8 place-items-center rounded-lg bg-slate-900 text-xs font-bold text-white">
              {initials}
            </div>
            <div className="min-w-0">
              <p className="truncate text-sm font-semibold text-slate-900">{profile?.name ?? "Member"}</p>
              <p className="truncate text-xs text-slate-500">{profile?.email ?? "No email"}</p>
            </div>
          </div>
          <button onClick={handleLogout} className="btn-secondary w-full text-xs">
            Log out
          </button>
        </div>
      </aside>

      <main className="app-main px-4 py-5 lg:px-8 lg:py-7">
        <div className="mx-auto w-full max-w-7xl">{children}</div>
      </main>
    </div>
  );
}
