<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Store extends BaseConfig
{
    /** Show vendor links, tags, and /vendor/* portal on the storefront */
    public bool $showVendor = false;

    public function __construct()
    {
        parent::__construct();

        $fromEnv = env('store.showVendor');
        if ($fromEnv !== null && $fromEnv !== '') {
            $this->showVendor = filter_var($fromEnv, FILTER_VALIDATE_BOOLEAN);
        }
    }
}
