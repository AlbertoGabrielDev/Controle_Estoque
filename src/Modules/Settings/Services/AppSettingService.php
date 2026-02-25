<?php

namespace Modules\Settings\Services;

use Modules\Settings\Models\AppSetting;
use Illuminate\Support\Facades\Schema;

class AppSettingService
{
    public const KEY_SALES_REQUIRE_CLIENT = 'sales.require_client';
    public const ANON_CLIENT_PREFIX = 'ANON-';

    public function getBool(string $key, bool $default = false): bool
    {
        if (!Schema::hasTable('app_settings')) {
            return $default;
        }

        $value = AppSetting::query()->where('key', $key)->value('value');
        if ($value === null) {
            return $default;
        }

        $normalized = strtolower(trim((string) $value));
        if ($normalized === '') {
            return $default;
        }

        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }

    public function setBool(string $key, bool $value): void
    {
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        AppSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value ? '1' : '0']
        );
    }
}
