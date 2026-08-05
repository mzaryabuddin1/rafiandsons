<?php

namespace App\Controllers\Admin;

use App\Models\SettingModel;

class SettingsController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('settings/index', [
            'pageTitle'  => 'Settings',
            'activeMenu' => 'settings',
            'canUpdate'  => $this->auth->can('settings.update'),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('settings.view')) {
            return $denied;
        }

        return $this->jsonSuccess('Settings loaded.', model(SettingModel::class)->getMap());
    }

    public function update()
    {
        if ($denied = $this->requirePermission('settings.update')) {
            return $denied;
        }

        $keys = [
            'site_name', 'contact_email', 'contact_phone', 'contact_address',
            'facebook_url', 'instagram_url', 'youtube_url', 'whatsapp_number',
            'smtp_host', 'smtp_user', 'smtp_pass', 'smtp_port', 'smtp_from_email',
            'smtp_from_name', 'order_notify_email',
        ];

        $data = [];
        foreach ($keys as $key) {
            $data[$key] = (string) $this->request->getPost($key);
        }

        model(SettingModel::class)->setMany($data);

        return $this->jsonSuccess('Settings saved.');
    }
}
