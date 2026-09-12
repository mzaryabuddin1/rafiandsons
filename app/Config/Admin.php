<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Admin extends BaseConfig
{
    /** URL segment for admin panel (no leading/trailing slash) */
    public string $path = 'axSdwfwgms';

    public function __construct()
    {
        parent::__construct();

        $fromEnv = env('admin.path');
        if ($fromEnv !== null && $fromEnv !== '') {
            $this->path = trim((string) $fromEnv, '/');
        }
    }
}
