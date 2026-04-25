"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { getToken } from "@/lib/storage";

export default function HomePage() {
  const router = useRouter();

  useEffect(() => {
    if (getToken()) {
      router.replace("/dashboard");
      return;
    }
    router.replace("/login");
  }, [router]);

  return <div className="grid min-h-screen place-items-center text-slate-500">Redirecting...</div>;
}
