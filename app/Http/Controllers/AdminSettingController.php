<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Project;
use App\Models\User;
use App\Mail\OtpCodeMail;
use App\Mail\WeeklyAiReportMail;
use App\Mail\WelcomeUserMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminSettingController extends Controller
{
    public function editAi()
    {
        $apiKey = AppSetting::getValue('ai.gemini_api_key');

        return view('admin.settings.ai', [
            'geminiModel' => AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash'),
            'hasApiKey' => filled($apiKey),
            'apiKeyMask' => $this->maskApiKey($apiKey),
            'projectMembersOnly' => (bool) AppSetting::getValue('messenger.project_members_only', false),
            // Email settings
            'mailProvider' => AppSetting::getValue('mail.provider', 'brevo'),
            'brevoApiKey' => AppSetting::getValue('mail.brevo_api_key'),
            'hasBrevoKey' => filled(AppSetting::getValue('mail.brevo_api_key')),
            'brevoKeyMask' => $this->maskApiKey(AppSetting::getValue('mail.brevo_api_key')),
            'mailFromAddress' => AppSetting::getValue('mail.from_address', config('mail.from.address')),
            'smtpHost' => AppSetting::getValue('mail.smtp_host', ''),
            'smtpPort' => AppSetting::getValue('mail.smtp_port', '587'),
            'smtpUsername' => AppSetting::getValue('mail.smtp_username', ''),
            'smtpPassword' => AppSetting::getValue('mail.smtp_password'),
            'hasSmtpPassword' => filled(AppSetting::getValue('mail.smtp_password')),
            'smtpPasswordMask' => $this->maskApiKey(AppSetting::getValue('mail.smtp_password')),
        ]);
    }

    public function updateAi(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'gemini_api_key' => ['nullable', 'string', 'max:255'],
            'gemini_model' => ['required', 'string', 'max:120'],
            'clear_api_key' => ['nullable', 'boolean'],
        ]);

        if (($data['clear_api_key'] ?? false) === true) {
            AppSetting::putEncrypted('ai.gemini_api_key', null);
        } elseif (filled($data['gemini_api_key'] ?? null)) {
            AppSetting::putEncrypted('ai.gemini_api_key', trim((string) $data['gemini_api_key']));
        }

        AppSetting::put('ai.gemini_model', trim((string) $data['gemini_model']));

        return back()->with('status', __('AI settings saved successfully.'));
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mail_provider' => ['required', 'in:brevo,smtp'],
            'brevo_api_key' => ['nullable', 'string', 'max:255'],
            'clear_brevo_key' => ['nullable', 'boolean'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'clear_smtp_password' => ['nullable', 'boolean'],
        ]);

        AppSetting::put('mail.provider', $data['mail_provider']);
        AppSetting::put('mail.from_address', $data['mail_from_address']);

        // Brevo API key
        if (($data['clear_brevo_key'] ?? false) === true) {
            AppSetting::putEncrypted('mail.brevo_api_key', null);
        } elseif (filled($data['brevo_api_key'] ?? null)) {
            AppSetting::putEncrypted('mail.brevo_api_key', trim((string) $data['brevo_api_key']));
        }

        // SMTP settings
        AppSetting::put('mail.smtp_host', trim((string) ($data['smtp_host'] ?? '')));
        AppSetting::put('mail.smtp_port', (string) ($data['smtp_port'] ?? '587'));
        AppSetting::put('mail.smtp_username', trim((string) ($data['smtp_username'] ?? '')));

        if (($data['clear_smtp_password'] ?? false) === true) {
            AppSetting::putEncrypted('mail.smtp_password', null);
        } elseif (filled($data['smtp_password'] ?? null)) {
            AppSetting::putEncrypted('mail.smtp_password', trim((string) $data['smtp_password']));
        }

        return back()->with('status', __('Email settings saved successfully.'));
    }

    public function sendTestEmail(): RedirectResponse
    {
        $user = Auth::user();

        try {
            Mail::to($user->email)->send(new OtpCodeMail('123456', __('test email')));
        } catch (\Throwable $e) {
            return back()->withErrors(['email_test' => __('Failed to send test email: ') . $e->getMessage()]);
        }

        return back()->with('status', __('Test email sent to :email', ['email' => $user->email]));
    }

    public function sendWelcomeToAll(): RedirectResponse
    {
        // Mark unverified users as verified
        User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);

        $users = User::all();
        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    (new WelcomeUserMail($user))->locale($user->locale ?? 'vi')
                );
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('Welcome email failed (batch)', [
                    'user_id' => $user->id,
                    'message' => $e->getMessage(),
                ]);
            }

            usleep(500_000);
        }

        if ($failed > 0) {
            return back()->with('status', __('Welcome emails sent: :sent, failed: :failed', ['sent' => $sent, 'failed' => $failed]));
        }

        return back()->with('status', __('Welcome emails sent to all :count users.', ['count' => $sent]));
    }

    private function maskApiKey(?string $key): ?string
    {
        if (! filled($key)) {
            return null;
        }

        $len = strlen($key);
        if ($len <= 8) {
            return str_repeat('*', $len);
        }

        return substr($key, 0, 4).'••••••'.substr($key, -4);
    }

    public function editMessenger()
    {
        return view('admin.settings.messenger', [
            'projectMembersOnly' => (bool) AppSetting::getValue('messenger.project_members_only', false),
        ]);
    }

    public function sendWeeklyReport(): RedirectResponse
    {
        $apiKey = AppSetting::getValue('ai.gemini_api_key');
        $model = AppSetting::getValue('ai.gemini_model', 'gemini-2.5-flash');

        if (! filled($apiKey)) {
            return back()->with('status', __('AI API key is not configured.'));
        }

        $projects = Project::with(['tasks.assignee', 'tasks.subtasks', 'members', 'owner'])->get();
        $locale = app()->getLocale();
        $language = $locale === 'vi' ? 'Vietnamese' : 'English';

        // Build context for all projects
        $projectSummaries = $projects->map(function ($project) {
            $tasks = $project->tasks;
            $total = $tasks->count();
            $done = $tasks->where('status', 'done')->count();
            $inProgress = $tasks->where('status', 'in_progress')->count();
            $overdue = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'done')->count();
            $highOpen = $tasks->where('priority', 'high')->where('status', '!=', 'done')->count();

            return "- {$project->name} (Status: {$project->status}): Total={$total}, Done={$done}, In Progress={$inProgress}, Overdue={$overdue}, High Priority Open={$highOpen}, Members={$project->members->count()}";
        })->implode("\n");

        $prompt = "You are a project manager generating a weekly report. Reply in {$language}.\n\n" .
            "Date: " . now()->format('Y-m-d') . "\n\n" .
            "Project summaries:\n{$projectSummaries}\n\n" .
            "Generate a weekly project report with:\n" .
            "1. **Executive Summary** - overall status across all projects\n" .
            "2. **Key Highlights** - achievements this week\n" .
            "3. **Risk Areas** - projects that need attention\n" .
            "4. **Recommendations** - top 3-5 action items for next week\n\n" .
            "Be concise. Use bullet points. Use markdown formatting with headers. Use emojis for visual emphasis.";

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            urlencode($model),
            urlencode($apiKey)
        );

        try {
            $response = Http::withoutVerifying()->timeout(60)->acceptJson()->post($url, [
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.5, 'maxOutputTokens' => 8192],
            ]);

            $reply = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));
        } catch (\Throwable $e) {
            Log::warning('Weekly AI report generation failed', ['error' => $e->getMessage()]);
            return back()->with('status', __('AI report generation failed.'));
        }

        if (empty($reply)) {
            return back()->with('status', __('AI returned an empty report.'));
        }

        // Convert markdown to HTML (simple conversion)
        $reportHtml = $this->markdownToHtml($reply);

        // Send to all admin users and project leads
        $recipients = User::where('role', 'admin')->get();
        $sent = 0;

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->send(new WeeklyAiReportMail($reportHtml, $user->name));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Weekly report email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
            usleep(500_000);
        }

        return back()->with('status', __('Weekly AI report sent to :count admins.', ['count' => $sent]));
    }

    private function markdownToHtml(string $md): string
    {
        // Simple markdown → HTML conversion for emails
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

    public function updateMessenger(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_members_only' => ['boolean'],
        ]);

        AppSetting::put('messenger.project_members_only', (bool) ($validated['project_members_only'] ?? false));

        return back()->with('status', __('Messenger settings saved successfully.'));
    }
}
