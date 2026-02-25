<?php

namespace App\Http\Controllers;

use App\Models\AiMessage;
use App\Models\AppSetting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiAssistantController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $hasApiKey = filled(AppSetting::getValue('ai.gemini_api_key'));

        // Delete messages older than 24 hours
        AiMessage::where('created_at', '<', now()->subHours(24))->delete();

        // Retrieve messages from the last 24 hours
        $messages = AiMessage::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'asc')
            ->get();

        return view('ai.chat', [
            'projects' => $this->availableProjects($user),
            'quickPrompts' => $this->quickPrompts(app()->getLocale()),
            'defaultModel' => AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash'),
            'hasApiKey' => $hasApiKey,
            'isAdmin' => $user->isAdmin(),
            'taskSuggestions' => $this->buildGeneralTaskSuggestions(),
            'messages' => $messages,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        if (empty($data['project_id'])) {
            return response()->json([
                'ok' => true,
                'suggestions' => $this->buildGeneralTaskSuggestions(),
                'context' => null,
            ]);
        }

        $project = Project::query()->with(['tasks.assignee'])->findOrFail((int) $data['project_id']);
        if (! $this->canAccessProject($project, $user)) {
            abort(403);
        }

        return response()->json([
            'ok' => true,
            'suggestions' => $this->buildProjectTaskSuggestions($project),
            'context' => [
                'project' => $project->name,
                'status' => $project->status,
            ],
        ]);
    }

    public function chat(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        $message = trim((string) $data['message']);
        if ($message === '') {
            return response()->json([
                'ok' => false,
                'message' => __('Message cannot be empty.'),
            ], 422);
        }

        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return response()->json([
                'ok' => false,
                'message' => __('AI assistant is currently unavailable.'),
            ], 422);
        }

        $projectContext = null;
        if (! empty($data['project_id'])) {
            $project = Project::query()->with(['tasks.assignee'])->findOrFail((int) $data['project_id']);
            if (! $this->canAccessProject($project, $user)) {
                abort(403);
            }

            $projectContext = $this->buildProjectContext($project);
        } else {
            $projectContext = $this->buildGeneralContext($user);
        }

        $prompt = $this->buildPrompt($user, $message, $projectContext, app()->getLocale());

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            urlencode($model),
            urlencode($apiKey)
        );

        try {
            $response = Http::withoutVerifying()->timeout(60)->acceptJson()->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 4096,
                ],
            ]);
        } catch (\Throwable) {
            return response()->json([
                'ok' => false,
                'message' => __('AI service is temporarily unavailable.'),
            ], 503);
        }

        if (! $response->ok()) {
            return response()->json([
                'ok' => false,
                'message' => __('AI request failed. Please try again later.'),
            ], 503);
        }

        $reply = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $reply = trim($reply);

        if ($reply === '') {
            return response()->json([
                'ok' => false,
                'message' => __('AI returned an empty response.'),
            ], 503);
        }

        // Store the message and response
        AiMessage::create([
            'user_id' => $user->id,
            'project_id' => $data['project_id'] ?? null,
            'user_message' => $message,
            'ai_response' => $reply,
        ]);

        return response()->json([
            'ok' => true,
            'reply' => $reply,
        ]);
    }

    private function availableProjects(User $user)
    {
        $query = Project::query()->orderBy('name');

        if (! $user->isAdmin()) {
            $query->where(function ($projectQuery) use ($user) {
                $projectQuery->where('owner_id', $user->id)
                    ->orWhereHas('members', function ($memberQuery) use ($user) {
                        $memberQuery->where('users.id', $user->id);
                    })
                    ->orWhereHas('tasks', function ($taskQuery) use ($user) {
                        $taskQuery->where('assignee_id', $user->id);
                    });
            });
        }

        return $query->get(['id', 'name', 'status']);
    }

    private function canAccessProject(Project $project, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ((int) $project->owner_id === (int) $user->id) {
            return true;
        }

        if ($project->members()->where('users.id', $user->id)->exists()) {
            return true;
        }

        return $project->tasks()->where('assignee_id', $user->id)->exists();
    }

    private function buildProjectContext(Project $project): string
    {
        $tasks = $project->tasks()->with(['assignee'])->get();
        $total = $tasks->count();
        $done = $tasks->where('status', 'done');
        $inProgress = $tasks->where('status', 'in_progress');
        $todo = $tasks->where('status', 'todo');
        $overdue = $tasks->filter(function ($task) {
            return $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
        });

        $formatTask = function ($t) {
            $due = $t->due_date ? $t->due_date->format('Y-m-d') : 'none';
            $assignee = $t->assignee ? $t->assignee->name : 'unassigned';
            return "- [{$t->id}] {$t->title} (Status: {$t->status}, Priority: {$t->priority}, Due: {$due}, Assignee: {$assignee})";
        };

        $doneSummary = $done->take(10)->map($formatTask)->implode("\n");
        $inProgressSummary = $inProgress->map($formatTask)->implode("\n");
        $todoSummary = $todo->take(20)->map($formatTask)->implode("\n");

        return "Project: {$project->name}\n" .
               "Status: {$project->status}\n" .
               "Overview: Total={$total}, Done={$done->count()}, In Progress={$inProgress->count()}, Todo={$todo->count()}, Overdue={$overdue->count()}\n\n" .
               "Tasks In Progress:\n{$inProgressSummary}\n\n" .
               "Tasks To Do (Top 20):\n{$todoSummary}\n\n" .
               "Recently Completed (Top 10):\n{$doneSummary}";
    }

    private function buildGeneralContext(User $user): string
    {
        // 1. Projects Access (Owned + Member)
        $projectsQuery = Project::query()->withCount(['tasks', 'members']);
        
        if (! $user->isAdmin()) {
            $projectsQuery->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('members', function ($mq) use ($user) {
                      $mq->where('users.id', $user->id);
                  });
            });
        }
        
        $projects = $projectsQuery->orderByDesc('created_at')->limit(30)->get();

        $projectSummary = $projects->map(function ($p) use ($user) {
            $role = ($p->owner_id == $user->id) ? 'Owner' : 'Member';
            return "- {$p->name} [ID:{$p->id}] (Status: {$p->status}, Role: {$role}): {$p->tasks_count} tasks.";
        })->implode("\n");

        // 2. User's Task Workload (Assigned to Me)
        $myTasks = \App\Models\Task::where('assignee_id', $user->id)
            ->with('project:id,name')
            ->orderByRaw("FIELD(status, 'in_progress', 'todo', 'done')")
            ->orderBy('due_date')
            ->limit(50)
            ->get();

        $groupedTasks = $myTasks->groupBy('status');

        $formatMyTask = fn($t) => "- [{$t->project->name}] {$t->title} (Priority: {$t->priority}, Due: " . ($t->due_date?->format('Y-m-d') ?? 'none') . ")";

        $inProgressTasks = $groupedTasks->get('in_progress', collect())->map($formatMyTask)->implode("\n");
        $todoTasks = $groupedTasks->get('todo', collect())->map($formatMyTask)->implode("\n");
        $doneTasks = $groupedTasks->get('done', collect())->map($formatMyTask)->implode("\n");

        return "User: {$user->name} (Global Role: {$user->role})\n\n" .
               "=== AVAILABLE PROJECTS (Top 30) ===\n{$projectSummary}\n\n" .
               "=== MY ASSIGNED TASKS ===\n" .
               "--- IN PROGRESS ---\n{$inProgressTasks}\n\n" .
               "--- TO DO ---\n{$todoTasks}\n\n" .
               "--- RECENTLY COMPLETED ---\n{$doneTasks}";
    }

    private function buildPrompt(User $user, string $userMessage, ?string $contextData, string $locale): string
    {
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';
        
        $system = "You are an AI project assistant. The user is named '{$user->name}' (ID: {$user->id}). You have access to their project data. Reply in {$language}. Keep answers practical and concise.";
        
        $contextSection = $contextData ? "\n\nAvailable Context:\n{$contextData}" : '';

        return "{$system}\nIf possible, offer:\n1) Quick summary\n2) Recommended next actions\n3) Risks/mitigations\nIf asked for unavailable data, clarify what is missing.{$contextSection}\n\nUser message:\n{$userMessage}";
    }

    private function buildGeneralTaskSuggestions(): array
    {
        return [
            __('Ưu tiên 3 việc quan trọng nhất hôm nay cho tôi.'),
            __('Kiểm tra rủi ro deadline trong tuần này.'),
            __('Gợi ý cách chia nhỏ một task lớn thành các bước thực thi.'),
            __('Viết mẫu nhắn tin cập nhật tiến độ cho nhóm.'),
        ];
    }

    private function buildProjectTaskSuggestions(Project $project): array
    {
        $tasks = $project->tasks;
        $overdueCount = $tasks->filter(function ($task) {
            return $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
        })->count();

        $highPriorityOpen = $tasks->where('priority', 'high')->where('status', '!=', 'done')->count();
        $unassigned = $tasks->whereNull('assignee_id')->where('status', '!=', 'done')->count();

        return [
            __('Tóm tắt nhanh tình trạng dự án này và đề xuất 3 hành động kế tiếp.'),
            __('Dựa trên deadline hiện tại, task nào cần ưu tiên trước trong 3 ngày tới?'),
            __('Gợi ý phương án phân công lại task để giảm tắc nghẽn tiến độ.'),
            __('Có :overdue task quá hạn, :high task ưu tiên cao chưa xong, :unassigned task chưa phân công. Hãy đề xuất kế hoạch xử lý.', [
                'overdue' => $overdueCount,
                'high' => $highPriorityOpen,
                'unassigned' => $unassigned,
            ]),
        ];
    }

    private function quickPrompts(string $locale): array
    {
        if ($locale === 'vi') {
            return [
                __('Tóm tắt tiến độ dự án'),
                __('Gợi ý ưu tiên công việc hôm nay'),
                __('Phát hiện rủi ro deadline'),
                __('Soạn tin nhắn cập nhật cho nhóm'),
            ];
        }

        return [
            'Summarize project progress',
            'Suggest today priorities',
            'Detect deadline risks',
            'Draft a team update message',
        ];
    }
}
