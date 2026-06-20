<?php

namespace App\Repositories\Interfaces;

interface SettingRepositoryInterface
{
    /**
     * Create or update setting(s)
     *
     * @param array $data
     * @return void
     */
    public function storeOrUpdate(array $data);

    /**
     * Get setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, $default = null);

    /**
     * Get setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAllSetting();
}
