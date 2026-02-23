<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('ai.chat', [
            'projects' => $this->availableProjects($user),
            'defaultModel' => AppSetting::getValue('ai.gemini_model', 'gemini-3.0-flash'),
            'hasApiKey' => filled(AppSetting::getValue('ai.gemini_api_key')),
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
                'message' => __('AI is not configured yet. Please contact admin.'),
            ], 422);
        }

        $projectContext = null;
        if (! empty($data['project_id'])) {
            $project = Project::query()->with('tasks')->findOrFail((int) $data['project_id']);
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

        return "Project: {$project->name}\nStatus: {$project->status}\nTask summary: total={$total}, done={$done}, in_progress={$inProgress}, todo={$todo}";
    }

    private function buildPrompt(string $userMessage, ?string $projectContext, string $locale): string
    {
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        $context = $projectContext ? "\n\nProject context:\n{$projectContext}" : '';

        return "You are an assistant for a project management platform. Reply in {$language}. Keep answers concise, actionable, and safe. If asked for unavailable data, clearly say so and suggest next steps.{$context}\n\nUser message:\n{$userMessage}";
    }
}
