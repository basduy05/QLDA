<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->ensureAccess($task);

        $request->validate([
            "files" => ["required", "array"],
            "files.*" => ["file", "max:10240"], // 10MB max per file
        ]);

        if ($request->hasFile("files")) {
            foreach ($request->file("files") as $file) {
                 $path = $file->store("task-attachments", "public");
                 $mime = $file->getMimeType();
                 $type = "other";

                 if (str_starts_with($mime, "image/")) {
                     $type = "image";
                 } elseif ($mime === "application/pdf") {
                     $type = "pdf";
                 }

                $task->attachments()->create([
                    "uploaded_by" => Auth::id(),
                    "file_path" => $path,
                    "file_name" => $file->getClientOriginalName(),
                    "file_type" => $type,
                    "file_size" => $file->getSize(),
                ]);
            }
        }

        return back()->with("status", "Files uploaded successfully.");
    }

    public function destroy(TaskAttachment $attachment)
    {
        $user = Auth::user();
        if ($attachment->uploaded_by !== $user->id) {
             $this->ensureAccess($attachment->task);
        }

        if (Storage::disk("public")->exists($attachment->file_path)) {
            Storage::disk("public")->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with("status", "Attachment deleted.");
    }

    private function ensureAccess(Task $task): void
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return;
        }

        if ($task->project->owner_id === $user->id) {
            return;
        }

        if ($task->project->members()->where("users.id", $user->id)->exists()) {
            return;
        }

        abort(403);
    }
}
