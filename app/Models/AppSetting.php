<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::query()->where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $value = $setting->value;
        if ($value === null) {
            return $default;
        }

        if (! $setting->is_encrypted) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function put(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'is_encrypted' => false,
            ]
        );
    }

    public static function putEncrypted(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value !== null && $value !== '' ? Crypt::encryptString($value) : null,
                'is_encrypted' => true,
            ]
        );
    }
}
