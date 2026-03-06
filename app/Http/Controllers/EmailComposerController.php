<?php

namespace App\Http\Controllers;

use App\Mail\CustomComposedMail;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailComposerController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('admin.email-composer', [
            'users' => $users,
            'templates' => $this->getTemplates(),
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:50000'],
        ]);

        $sender = Auth::user();
        $body = $this->markdownToHtml($data['body']);
        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($data['recipients'] as $email) {
            try {
                Mail::to(trim($email))->send(
                    new CustomComposedMail($data['subject'], $body, $sender->name)
                );
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = $email . ': ' . $e->getMessage();
                Log::warning('Custom email failed', [
                    'to' => $email,
                    'error' => $e->getMessage(),
                ]);
            }

            if (count($data['recipients']) > 1) {
                usleep(500_000);
            }
        }

        if ($failed > 0) {
            return back()
                ->with('status', __('Sent :sent, failed :failed emails.', ['sent' => $sent, 'failed' => $failed]))
                ->withErrors(['send' => implode("\n", $errors)]);
        }

        return back()->with('status', __('Email sent successfully to :count recipients.', ['count' => $sent]));
    }

    public function aiSuggest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'context' => ['nullable', 'string', 'max:5000'],
        ]);

        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return response()->json(['error' => __('AI API key is not configured.')], 422);
        }

        $locale = app()->getLocale();
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        $systemPrompt = "You are an email writing assistant for a project management app called Aperlex. " .
            "Write professional, clear, and concise emails. Reply in {$language}.\n" .
            "Return ONLY the email body text (no subject, no greeting prefix like 'Subject:').\n" .
            "Use markdown formatting (bold, lists, etc.) for better readability.\n" .
            "Keep a professional but friendly tone.";

        $userPrompt = $data['prompt'];
        if (filled($data['context'] ?? null)) {
            $userPrompt .= "\n\nAdditional context:\n" . $data['context'];
        }

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            urlencode($model),
            urlencode($apiKey)
        );

        try {
            $response = Http::withoutVerifying()->timeout(30)->acceptJson()->post($url, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\n" . $userPrompt]]],
                ],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 4096],
            ]);

            $reply = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

            if (empty($reply)) {
                return response()->json(['error' => __('AI returned an empty response.')], 422);
            }

            return response()->json(['suggestion' => $reply]);
        } catch (\Throwable $e) {
            return response()->json(['error' => __('AI request failed: ') . $e->getMessage()], 500);
        }
    }

    public function getTemplate(Request $request): JsonResponse
    {
        $key = $request->input('key');
        $templates = $this->getTemplates();

        $template = collect($templates)->firstWhere('key', $key);

        if (! $template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        return response()->json($template);
    }

    private function getTemplates(): array
    {
        return [
            [
                'key' => 'project_kickoff',
                'name' => __('Project Kickoff'),
                'icon' => '🚀',
                'subject' => __('Project Kickoff: [Project Name]'),
                'body' => __("Hi team,\n\nI'm excited to announce the kickoff of **[Project Name]**!\n\n**Project Goals:**\n- [Goal 1]\n- [Goal 2]\n- [Goal 3]\n\n**Timeline:** [Start Date] → [End Date]\n\n**Key milestones:**\n1. [Milestone 1] — [Date]\n2. [Milestone 2] — [Date]\n3. [Milestone 3] — [Date]\n\nPlease review the project details on the dashboard and reach out if you have any questions.\n\nLet's make this a success!"),
            ],
            [
                'key' => 'deadline_reminder',
                'name' => __('Deadline Reminder'),
                'icon' => '⏰',
                'subject' => __('Reminder: Deadline Approaching — [Task/Project]'),
                'body' => __("Hi,\n\nThis is a friendly reminder that the deadline for **[Task/Project Name]** is approaching.\n\n**Due date:** [Date]\n**Current status:** [Status]\n\n**Action needed:**\n- [Action item 1]\n- [Action item 2]\n\nPlease make sure all deliverables are completed on time. If you're facing any blockers, let me know so we can address them together.\n\nThank you for your hard work!"),
            ],
            [
                'key' => 'meeting_invitation',
                'name' => __('Meeting Invitation'),
                'icon' => '📅',
                'subject' => __('Meeting Invitation: [Topic]'),
                'body' => __("Hi,\n\nYou're invited to a meeting:\n\n**Topic:** [Meeting Topic]\n**Date:** [Date]\n**Time:** [Time]\n**Duration:** [Duration]\n**Location/Link:** [Location or Meeting Link]\n\n**Agenda:**\n1. [Agenda item 1]\n2. [Agenda item 2]\n3. [Agenda item 3]\n\nPlease confirm your attendance. If you can't make it, let me know and I'll share the meeting notes afterwards.\n\nSee you there!"),
            ],
            [
                'key' => 'progress_update',
                'name' => __('Progress Update'),
                'icon' => '📊',
                'subject' => __('Progress Update: [Project Name] — Week [X]'),
                'body' => __("Hi team,\n\nHere's this week's progress update for **[Project Name]**:\n\n**✅ Completed:**\n- [Task 1]\n- [Task 2]\n\n**🔄 In Progress:**\n- [Task 3] — [% complete]\n- [Task 4] — [% complete]\n\n**⚠️ Blockers:**\n- [Blocker description]\n\n**📅 Next week's priorities:**\n- [Priority 1]\n- [Priority 2]\n\nOverall project health: **[On Track / At Risk / Behind]**\n\nLet me know if you have questions or concerns."),
            ],
            [
                'key' => 'welcome_member',
                'name' => __('Welcome New Member'),
                'icon' => '👋',
                'subject' => __('Welcome to the Team!'),
                'body' => __("Hi [Name],\n\nWelcome to the team! We're thrilled to have you on board.\n\n**Getting started:**\n1. Log in to Aperlex at [URL]\n2. Check your assigned projects on the Dashboard\n3. Review any outstanding tasks\n4. Join the team chat to say hello!\n\n**Your team:**\n- [Team member 1] — [Role]\n- [Team member 2] — [Role]\n\nFeel free to reach out if you have any questions. We're here to help!\n\nWelcome aboard! 🎉"),
            ],
            [
                'key' => 'task_delegation',
                'name' => __('Task Delegation'),
                'icon' => '📋',
                'subject' => __('New Task Assigned: [Task Name]'),
                'body' => __("Hi [Name],\n\nI've assigned you a new task:\n\n**Task:** [Task Name]\n**Project:** [Project Name]\n**Priority:** [High/Medium/Low]\n**Due date:** [Date]\n\n**Description:**\n[Detailed task description]\n\n**Deliverables:**\n- [Deliverable 1]\n- [Deliverable 2]\n\n**Resources:**\n- [Link/Document 1]\n- [Link/Document 2]\n\nPlease let me know if you have any questions or need clarification on any aspect of this task."),
            ],
            [
                'key' => 'thank_you',
                'name' => __('Thank You / Recognition'),
                'icon' => '🏆',
                'subject' => __('Great Work on [Project/Task]!'),
                'body' => __("Hi [Name/Team],\n\nI wanted to take a moment to recognize your outstanding work on **[Project/Task Name]**.\n\n**What you achieved:**\n- [Achievement 1]\n- [Achievement 2]\n- [Achievement 3]\n\nYour dedication and effort have made a real difference. The quality of your work is truly impressive, and it hasn't gone unnoticed.\n\nKeep up the excellent work! 🌟\n\nThank you!"),
            ],
            [
                'key' => 'issue_report',
                'name' => __('Issue Report'),
                'icon' => '🐛',
                'subject' => __('Issue Report: [Brief Description]'),
                'body' => __("Hi,\n\nI'd like to report the following issue:\n\n**Issue:** [Brief title]\n**Severity:** [Critical/High/Medium/Low]\n**Affected area:** [Module/Feature]\n\n**Description:**\n[Detailed description of the issue]\n\n**Steps to reproduce:**\n1. [Step 1]\n2. [Step 2]\n3. [Step 3]\n\n**Expected behavior:** [What should happen]\n**Actual behavior:** [What actually happens]\n\nPlease investigate and prioritize accordingly. Let me know if you need more information."),
            ],
        ];
    }

    private function markdownToHtml(string $md): string
    {
        $html = e($md);
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3 style="color:#1e293b;font-size:16px;margin:16px 0 8px;">$1</h3>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2 style="color:#1e293b;font-size:18px;margin:20px 0 10px;">$1</h2>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1 style="color:#1e293b;font-size:20px;margin:24px 0 12px;">$1</h1>', $html);
        $html = preg_replace('/^- (.+)$/m', '<li style="margin:4px 0;">$1</li>', $html);
        $html = preg_replace('/(<li.*<\/li>\n?)+/', '<ul style="padding-left:20px;margin:8px 0;">$0</ul>', $html);
        $html = nl2br($html);

        return $html;
    }
}
