<?php

namespace App\Http\Controllers;

use App\Models\AiMessage;
use App\Models\AppSetting;
use App\Models\Project;
use App\Models\Task;
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

    /**
     * AI Auto-generate Subtasks for a task.
     */
    public function generateSubtasks(Request $request, Task $task): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $project = $task->project;

        if (! $this->canAccessProject($project, $user)) {
            abort(403);
        }

        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return response()->json(['ok' => false, 'message' => __('AI assistant is currently unavailable.')], 422);
        }

        $existingSubtasks = $task->subtasks->pluck('title')->implode(', ');
        $locale = app()->getLocale();
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        $prompt = "You are a project management assistant. Reply in {$language}.\n\n" .
            "Task: {$task->title}\n" .
            "Description: " . ($task->description ?: 'None') . "\n" .
            "Priority: {$task->priority}\n" .
            "Project: {$project->name}\n" .
            ($existingSubtasks ? "Existing subtasks: {$existingSubtasks}\n" : '') .
            "\nSuggest 3-7 actionable subtasks to complete this task. " .
            "For each subtask, estimate effort points (0.2 = trivial, 1-3 = medium, 5+ = large).\n" .
            "Return ONLY a valid JSON array. No markdown, no explanation. Example:\n" .
            '[{"title":"Setup database schema","points":2.0},{"title":"Write unit tests","points":1.5}]';

        $reply = $this->callGemini($apiKey, $model, $prompt);

        if ($reply === null) {
            return response()->json(['ok' => false, 'message' => __('AI service is temporarily unavailable.')], 503);
        }

        // Parse JSON from reply (strip markdown fences if present)
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($reply));
        $subtasks = json_decode($cleaned, true);

        if (! is_array($subtasks)) {
            return response()->json(['ok' => false, 'message' => __('AI returned invalid format. Please try again.')], 503);
        }

        // Validate and sanitize
        $result = [];
        foreach ($subtasks as $item) {
            if (! is_array($item) || empty($item['title'])) {
                continue;
            }
            $points = isset($item['points']) ? max(0.1, min(10, (float) $item['points'])) : 0.2;
            $result[] = ['title' => Str::limit(strip_tags($item['title']), 255), 'points' => round($points, 1)];
        }

        return response()->json(['ok' => true, 'subtasks' => $result]);
    }

    /**
     * AI Smart Assignment - suggest the best assignee for a task.
     */
    public function suggestAssignee(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['nullable', 'string'],
        ]);

        $project = Project::with(['members', 'tasks.assignee'])->findOrFail($data['project_id']);

        if (! $this->canAccessProject($project, $user)) {
            abort(403);
        }

        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return response()->json(['ok' => false, 'message' => __('AI assistant is currently unavailable.')], 422);
        }

        // Build team workload context
        $members = $project->members->merge(collect([$project->owner]))->unique('id');
        $tasks = $project->tasks;

        $workload = $members->map(function ($member) use ($tasks) {
            $memberTasks = $tasks->where('assignee_id', $member->id);
            $open = $memberTasks->where('status', '!=', 'done');
            $overdue = $open->filter(fn($t) => $t->due_date && $t->due_date->isPast());

            return "- {$member->name} (ID:{$member->id}): {$open->count()} open tasks, {$overdue->count()} overdue, " .
                "high:{$open->where('priority','high')->count()}, " .
                "done:{$memberTasks->where('status','done')->count()}";
        })->implode("\n");

        $locale = app()->getLocale();
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        $prompt = "You are a project management assistant. Reply in {$language}.\n\n" .
            "Project: {$project->name}\n" .
            "New task: {$data['title']}\n" .
            "Description: " . ($data['description'] ?: 'None') . "\n" .
            "Priority: " . ($data['priority'] ?: 'medium') . "\n\n" .
            "Team workload:\n{$workload}\n\n" .
            "Suggest the best assignee for this task. Consider workload balance, overdue tasks, and priority.\n" .
            "Return ONLY valid JSON. No markdown. Example:\n" .
            '{"user_id":1,"user_name":"John","reason":"Lowest workload with 2 open tasks and no overdue items."}';

        $reply = $this->callGemini($apiKey, $model, $prompt);

        if ($reply === null) {
            return response()->json(['ok' => false, 'message' => __('AI service is temporarily unavailable.')], 503);
        }

        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($reply));
        $suggestion = json_decode($cleaned, true);

        if (! is_array($suggestion) || empty($suggestion['user_id'])) {
            return response()->json(['ok' => false, 'message' => __('AI returned invalid format. Please try again.')], 503);
        }

        // Verify the suggested user is actually a project member
        $suggestedMember = $members->firstWhere('id', $suggestion['user_id']);
        if (! $suggestedMember) {
            return response()->json(['ok' => false, 'message' => __('AI suggested an invalid team member.')], 422);
        }

        return response()->json([
            'ok' => true,
            'user_id' => $suggestedMember->id,
            'user_name' => $suggestedMember->name,
            'reason' => strip_tags($suggestion['reason'] ?? ''),
        ]);
    }

    /**
     * AI Risk Detection - analyze at-risk tasks across projects.
     */
    public function detectRisks(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return response()->json(['ok' => false, 'message' => __('AI assistant is currently unavailable.')], 422);
        }

        // Gather at-risk tasks
        $query = Task::with(['project', 'assignee'])
            ->where('status', '!=', 'done');

        if (! empty($data['project_id'])) {
            $project = Project::findOrFail($data['project_id']);
            if (! $this->canAccessProject($project, $user)) {
                abort(403);
            }
            $query->where('project_id', $data['project_id']);
        } elseif (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                    ->orWhereHas('project', function ($pq) use ($user) {
                        $pq->where('owner_id', $user->id)
                            ->orWhereHas('members', fn($mq) => $mq->where('users.id', $user->id));
                    });
            });
        }

        $tasks = $query->orderBy('due_date')->limit(50)->get();

        $overdue = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast());
        $dueSoon = $tasks->filter(fn($t) => $t->due_date && ! $t->due_date->isPast() && $t->due_date->diffInDays(now()) <= 3);
        $highPriority = $tasks->where('priority', 'high');
        $unassigned = $tasks->whereNull('assignee_id');

        $formatTask = fn($t) => "- [{$t->project->name}] {$t->title} (Priority: {$t->priority}, Due: " . ($t->due_date?->format('Y-m-d') ?? 'none') . ", Assignee: " . ($t->assignee?->name ?? 'none') . ")";

        $context = "=== OVERDUE ({$overdue->count()}) ===\n" . $overdue->take(15)->map($formatTask)->implode("\n") .
            "\n\n=== DUE WITHIN 3 DAYS ({$dueSoon->count()}) ===\n" . $dueSoon->take(15)->map($formatTask)->implode("\n") .
            "\n\n=== HIGH PRIORITY OPEN ({$highPriority->count()}) ===\n" . $highPriority->take(10)->map($formatTask)->implode("\n") .
            "\n\n=== UNASSIGNED ({$unassigned->count()}) ===\n" . $unassigned->take(10)->map($formatTask)->implode("\n");

        $locale = app()->getLocale();
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        $prompt = "You are a project risk analyst. Reply in {$language}.\n\n" .
            "Analyze the following task data and identify the top risks.\n" .
            "For each risk, suggest a concrete mitigation action.\n\n{$context}\n\n" .
            "Return ONLY valid JSON array. No markdown. Example:\n" .
            '[{"level":"high","title":"3 tasks overdue in Project X","detail":"Tasks A, B, C are past deadline","action":"Reassign or escalate immediately"}]';

        $reply = $this->callGemini($apiKey, $model, $prompt);

        if ($reply === null) {
            return response()->json(['ok' => false, 'message' => __('AI service is temporarily unavailable.')], 503);
        }

        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($reply));
        $risks = json_decode($cleaned, true);

        if (! is_array($risks)) {
            return response()->json(['ok' => false, 'message' => __('AI returned invalid format.')], 503);
        }

        // Sanitize
        $risks = collect($risks)->take(10)->map(fn($r) => [
            'level' => in_array($r['level'] ?? '', ['high', 'medium', 'low']) ? $r['level'] : 'medium',
            'title' => strip_tags(Str::limit($r['title'] ?? '', 200)),
            'detail' => strip_tags(Str::limit($r['detail'] ?? '', 500)),
            'action' => strip_tags(Str::limit($r['action'] ?? '', 500)),
        ])->values()->all();

        return response()->json([
            'ok' => true,
            'risks' => $risks,
            'summary' => [
                'overdue' => $overdue->count(),
                'due_soon' => $dueSoon->count(),
                'high_priority' => $highPriority->count(),
                'unassigned' => $unassigned->count(),
            ],
        ]);
    }

    /**
     * AI Project Retrospective - generate a comprehensive retrospective report.
     */
    public function retrospective(Request $request, Project $project): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $this->canAccessProject($project, $user)) {
            abort(403);
        }

        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return response()->json(['ok' => false, 'message' => __('AI assistant is currently unavailable.')], 422);
        }

        $tasks = $project->tasks()->with(['assignee', 'subtasks'])->get();
        $members = $project->members->merge(collect([$project->owner]))->unique('id');
        $memberStats = $project->getMemberStatistics();

        $total = $tasks->count();
        $done = $tasks->where('status', 'done')->count();
        $overdue = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'done')->count();
        $completedLate = $tasks->filter(fn($t) => $t->status === 'done' && $t->due_date && $t->updated_at->gt($t->due_date))->count();

        $memberSummary = collect($memberStats)->map(fn($s) =>
            "- {$s['user']->name}: {$s['total_tasks']} tasks, on-time:{$s['completed_on_time']}, late:{$s['completed_late']}, overdue:{$s['overdue']}, score:{$s['score']}, contribution:{$s['contribution_percentage']}%"
        )->implode("\n");

        $locale = app()->getLocale();
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        $prompt = "You are a project retrospective facilitator. Reply in {$language}.\n\n" .
            "Project: {$project->name}\n" .
            "Status: {$project->status}\n" .
            "Timeline: " . ($project->start_date?->format('Y-m-d') ?? '?') . " to " . ($project->end_date?->format('Y-m-d') ?? '?') . "\n" .
            "Description: " . ($project->description ?: 'None') . "\n\n" .
            "Task Stats: Total={$total}, Done={$done}, Overdue={$overdue}, Completed Late={$completedLate}\n" .
            "Members: {$members->count()}\n\n" .
            "Member Performance:\n{$memberSummary}\n\n" .
            "Generate a comprehensive project retrospective with these sections:\n" .
            "1. **Overall Assessment** - project health score (1-10) and summary\n" .
            "2. **What Went Well** - positive aspects\n" .
            "3. **What Needs Improvement** - issues and bottlenecks\n" .
            "4. **Team Performance Highlights** - notable contributions\n" .
            "5. **Action Items** - concrete recommendations for the future\n\n" .
            "Use markdown formatting. Be specific and data-driven.";

        $reply = $this->callGemini($apiKey, $model, $prompt, 0.5, 8192);

        if ($reply === null) {
            return response()->json(['ok' => false, 'message' => __('AI service is temporarily unavailable.')], 503);
        }

        return response()->json(['ok' => true, 'report' => $reply]);
    }

    /**
     * AI Smart Search - natural language to structured task search.
     */
    public function smartSearch(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $request->validate([
            'query' => ['required', 'string', 'max:500'],
        ]);

        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return response()->json(['ok' => false, 'message' => __('AI assistant is currently unavailable.')], 422);
        }

        // Get available projects for context
        $projects = $this->availableProjects($user);
        $projectList = $projects->map(fn($p) => "- {$p->name} (ID:{$p->id})")->implode("\n");

        $locale = app()->getLocale();
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';
        $today = now()->format('Y-m-d');

        $prompt = "You are a search query parser. The user typed a natural language query in {$language}.\n" .
            "Today's date: {$today}\n\n" .
            "Available projects:\n{$projectList}\n\n" .
            "User query: \"{$data['query']}\"\n\n" .
            "Parse this into structured search filters. Return ONLY valid JSON:\n" .
            '{"keywords":"search terms","status":"todo|in_progress|done|null","priority":"low|medium|high|null","assignee_name":"name or null","project_id":null,"overdue":false,"due_before":"Y-m-d or null","due_after":"Y-m-d or null"}' .
            "\n\nSet only relevant fields. Leave others as null/false.";

        $reply = $this->callGemini($apiKey, $model, $prompt, 0.1);

        if ($reply === null) {
            return response()->json(['ok' => false, 'message' => __('AI service is temporarily unavailable.')], 503);
        }

        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($reply));
        $filters = json_decode($cleaned, true);

        if (! is_array($filters)) {
            return response()->json(['ok' => false, 'message' => __('Could not parse search query.')], 422);
        }

        // Build query from parsed filters
        $query = Task::with(['project', 'assignee']);

        // Scope to user's accessible tasks
        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('assignee_id', $user->id)
                    ->orWhereHas('project', function ($pq) use ($user) {
                        $pq->where('owner_id', $user->id)
                            ->orWhereHas('members', fn($mq) => $mq->where('users.id', $user->id));
                    });
            });
        }

        if (! empty($filters['keywords'])) {
            $kw = '%' . $filters['keywords'] . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', $kw)->orWhere('description', 'like', $kw);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['project_id'])) {
            $query->where('project_id', (int) $filters['project_id']);
        }

        if (! empty($filters['assignee_name'])) {
            $query->whereHas('assignee', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['assignee_name'] . '%');
            });
        }

        if (! empty($filters['overdue'])) {
            $query->where('status', '!=', 'done')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now());
        }

        if (! empty($filters['due_before'])) {
            $query->where('due_date', '<=', $filters['due_before']);
        }

        if (! empty($filters['due_after'])) {
            $query->where('due_date', '>=', $filters['due_after']);
        }

        $tasks = $query->orderByDesc('updated_at')->limit(20)->get();

        $results = $tasks->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'status' => $t->status,
            'priority' => $t->priority,
            'due_date' => $t->due_date?->format('Y-m-d'),
            'assignee' => $t->assignee?->name,
            'project' => $t->project?->name,
            'url' => route('tasks.show', $t),
        ])->values()->all();

        return response()->json([
            'ok' => true,
            'filters' => $filters,
            'results' => $results,
            'count' => count($results),
        ]);
    }

    /**
     * Helper: call Gemini API.
     */
    private function callGemini(string $apiKey, string $model, string $prompt, float $temperature = 0.4, int $maxTokens = 4096): ?string
    {
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            urlencode($model),
            urlencode($apiKey)
        );

        try {
            $response = Http::withoutVerifying()->timeout(60)->acceptJson()->post($url, [
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => $maxTokens,
                ],
            ]);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->ok()) {
            return null;
        }

        $text = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        return trim($text) !== '' ? trim($text) : null;
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
