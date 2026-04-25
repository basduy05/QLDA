"use client";

import { FormEvent, useEffect, useMemo, useState } from "react";
import { io } from "socket.io-client";
import ProtectedLayout from "@/components/ProtectedLayout";
import PageHeader from "@/components/PageHeader";
import { messageApi } from "@/lib/api";
import { getToken } from "@/lib/storage";

type Conversation = { id: string; title?: string; name?: string };
type Message = { id: string; message?: string; content?: string; sender?: { name?: string } };

const socketUrl = process.env.NEXT_PUBLIC_SOCKET_URL || "http://localhost:3000";

export default function MessengerPage() {
  const [conversations, setConversations] = useState<Conversation[]>([]);
  const [activeConversationId, setActiveConversationId] = useState("");
  const [messages, setMessages] = useState<Message[]>([]);
  const [draft, setDraft] = useState("");

  useEffect(() => {
    messageApi
      .conversations()
      .then((res) => {
        const list = res.data?.data ?? res.data ?? [];
        setConversations(list);
        if (list[0]?.id) setActiveConversationId(list[0].id);
      })
      .catch(() => setConversations([]));
  }, []);

  useEffect(() => {
    if (!activeConversationId) return;
    messageApi
      .conversationMessages(activeConversationId)
      .then((res) => setMessages(res.data?.data ?? res.data ?? []))
      .catch(() => setMessages([]));
  }, [activeConversationId]);

  useEffect(() => {
    const token = getToken();
    if (!token) return;

    const socket = io(socketUrl, {
      transports: ["websocket"],
      auth: { token },
    });

    socket.on("direct_message:received", () => {
      if (!activeConversationId) return;
      messageApi
        .conversationMessages(activeConversationId)
        .then((res) => setMessages(res.data?.data ?? res.data ?? []))
        .catch(() => undefined);
    });

    return () => {
      socket.disconnect();
    };
  }, [activeConversationId]);

  const activeTitle = useMemo(() => {
    const current = conversations.find((c) => c.id === activeConversationId);
    return current?.title || current?.name || "Conversation";
  }, [conversations, activeConversationId]);

  const handleSend = async (e: FormEvent) => {
    e.preventDefault();
    if (!draft.trim() || !activeConversationId) return;

    await messageApi.sendDirect(activeConversationId, draft.trim());
    setDraft("");
    const res = await messageApi.conversationMessages(activeConversationId);
    setMessages(res.data?.data ?? res.data ?? []);
  };

  return (
    <ProtectedLayout>
      <PageHeader title="Messenger" subtitle="Realtime-ready conversation view in Next.js" />

      <section className="card overflow-hidden">
        <div className="grid min-h-[70vh] gap-0 lg:grid-cols-[300px_1fr]">
          <aside className="border-b border-slate-200 bg-slate-50/60 p-3 lg:border-b-0 lg:border-r">
            <p className="mb-2 px-2 text-xs uppercase tracking-wider text-slate-500">Conversations</p>
            <div className="space-y-1">
              {conversations.map((c) => {
                const active = c.id === activeConversationId;
                return (
                  <button
                    key={c.id}
                    type="button"
                    onClick={() => setActiveConversationId(c.id)}
                    className={`w-full rounded-xl px-3 py-2 text-left text-sm transition ${
                      active
                        ? "bg-cyan-100 text-cyan-800"
                        : "bg-white text-slate-700 hover:bg-slate-100"
                    }`}
                  >
                    {c.title || c.name || `Conversation ${c.id.slice(0, 6)}`}
                  </button>
                );
              })}
              {!conversations.length ? <p className="px-2 py-6 text-sm text-slate-500">No conversations.</p> : null}
            </div>
          </aside>

          <div className="flex min-h-[500px] flex-col">
            <div className="border-b border-slate-200 px-4 py-3">
              <h2 className="font-display text-lg font-semibold text-slate-900">{activeTitle}</h2>
            </div>

            <div className="flex-1 space-y-2 overflow-y-auto bg-white px-4 py-4">
              {messages.map((m) => (
                <div key={m.id} className="max-w-[85%] rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2">
                  <p className="mb-0.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                    {m.sender?.name || "Member"}
                  </p>
                  <p className="text-sm text-slate-800">{m.message || m.content || ""}</p>
                </div>
              ))}
              {!messages.length ? <p className="text-sm text-slate-500">No messages yet.</p> : null}
            </div>

            <form onSubmit={handleSend} className="border-t border-slate-200 bg-slate-50/60 p-3">
              <div className="flex gap-2">
                <input
                  value={draft}
                  onChange={(e) => setDraft(e.target.value)}
                  className="field"
                  placeholder="Type a message..."
                />
                <button type="submit" className="btn-primary">Send</button>
              </div>
            </form>
          </div>
        </div>
      </section>
    </ProtectedLayout>
  );
}
