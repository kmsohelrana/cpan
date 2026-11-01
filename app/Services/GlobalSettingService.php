<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Cache;

class GlobalSettingService
{
    protected string $cacheKey = 'global_settings';

    public function all()
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return GlobalSetting::pluck('value', 'key');
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()->get($key, $default);
    }

    public function set(array|string $keys, mixed $value = null): mixed
    {
        if (is_array($keys)) {
            foreach ($keys as $key => $val) {
                $this->set($key, $val);
            }
            return true;
        }

        $setting = GlobalSetting::firstOrNew(['key' => $keys]);
        $setting->value = $value;
        $setting->save();

        $this->flushCache();

        return $value;
    }

    public function has(string $key): bool
    {
        return $this->all()->has($key);
    }

    public function remove(string $key): int
    {
        $deleted = GlobalSetting::where('key', $key)->delete();
        $this->flushCache();

        return $deleted;
    }

    public function flushCache(): bool
    {
        return Cache::forget($this->cacheKey);
    }
}
