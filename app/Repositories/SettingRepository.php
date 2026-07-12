<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingRepository implements SettingRepositoryInterface
{
    private const CACHE_KEY = 'settings.all';
    private const CACHE_TTL = 3600;

    /**
     * Create or update setting(s)
     *
     * @param array $data
     */
    public function storeOrUpdate(array $data)
    {
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->getAllSetting();
        return $settings[$key] ?? $default;
    }
    /**
     * Get setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAllSetting()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $settings = Setting::all();
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = $setting->value;
            }
            return $result;
        });
    }
}
