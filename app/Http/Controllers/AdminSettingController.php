<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpCodeMail;

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

    public function updateMessenger(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_members_only' => ['boolean'],
        ]);

        AppSetting::put('messenger.project_members_only', (bool) ($validated['project_members_only'] ?? false));

        return back()->with('status', __('Messenger settings saved successfully.'));
    }
}
