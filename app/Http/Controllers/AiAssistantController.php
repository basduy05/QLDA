<?php

namespace App\Http\Controllers;

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

        return view('ai.chat', [
            'projects' => $this->availableProjects($user),
            'quickPrompts' => $this->quickPrompts(app()->getLocale()),
            'defaultModel' => AppSetting::getValue('ai.gemini_model', 'gemini-3.0-flash'),
            'hasApiKey' => $hasApiKey,
            'isAdmin' => $user->isAdmin(),
            'taskSuggestions' => $this->buildGeneralTaskSuggestions(),
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
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-3.0-flash');

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
        }

        $prompt = $this->buildPrompt($message, $projectContext, app()->getLocale());

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            urlencode($model),
            urlencode($apiKey)
        );

        try {
            $response = Http::timeout(25)->acceptJson()->post($url, [
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
                    'maxOutputTokens' => 700,
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
        $tasks = $project->tasks;
        $total = $tasks->count();
        $done = $tasks->where('status', 'done')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $todo = $tasks->where('status', 'todo')->count();
        $overdue = $tasks->filter(function ($task) {
            return $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
        })->count();

        $taskLines = $tasks
            ->sortBy(function ($task) {
                return $task->due_date?->timestamp ?? PHP_INT_MAX;
            })
            ->take(15)
            ->map(function ($task) {
                $due = $task->due_date?->format('Y-m-d') ?? 'none';
                $assignee = $task->assignee?->name ?? 'unassigned';
                return sprintf(
                    '- %s | status=%s | priority=%s | due=%s | assignee=%s',
                    Str::limit((string) $task->title, 70),
                    $task->status,
                    $task->priority,
                    $due,
                    $assignee
                );
            })
            ->implode("\n");

        return "Project: {$project->name}\nStatus: {$project->status}\nTask summary: total={$total}, done={$done}, in_progress={$inProgress}, todo={$todo}, overdue={$overdue}\nKey tasks:\n{$taskLines}";
    }

    private function buildPrompt(string $userMessage, ?string $projectContext, string $locale): string
    {
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        $context = $projectContext ? "\n\nProject context:\n{$projectContext}" : '';

        return "You are an assistant for a project management platform. Reply in {$language}. Keep answers practical and concise. If possible, return:\n1) Quick summary\n2) Recommended next actions (3-5 bullets)\n3) Risks and mitigations\n4) Suggested message template for team communication when relevant.\nIf asked for unavailable data, clearly say so and suggest next steps.{$context}\n\nUser message:\n{$userMessage}";
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
