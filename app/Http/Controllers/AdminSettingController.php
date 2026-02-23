<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function editAi()
    {
        $apiKey = AppSetting::getValue('ai.gemini_api_key');

        return view('admin.settings.ai', [
            'geminiModel' => AppSetting::getValue('ai.gemini_model', 'gemini-3.0-flash'),
            'hasApiKey' => filled($apiKey),
            'apiKeyMask' => $this->maskApiKey($apiKey),
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
}
