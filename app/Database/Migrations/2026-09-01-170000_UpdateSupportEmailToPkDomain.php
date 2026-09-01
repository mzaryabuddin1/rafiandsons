<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSupportEmailToPkDomain extends Migration
{
    public function up()
    {
        $email = 'support@rafiandsonsnr.pk';
        $keys  = ['contact_email', 'smtp_from_email', 'order_notify_email'];

        foreach ($keys as $key) {
            $row = $this->db->table('settings')->where('key', $key)->get()->getRowArray();
            if (! $row) {
                continue;
            }

            $value = (string) ($row['value'] ?? '');
            if ($value === '' || str_contains($value, '@rafiandsonsnr.com')) {
                $this->db->table('settings')->where('key', $key)->update([
                    'value'      => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $smtpUser = $this->db->table('settings')->where('key', 'smtp_user')->get()->getRowArray();
        if ($smtpUser) {
            $userValue = (string) ($smtpUser['value'] ?? '');
            if ($userValue === '' || str_contains($userValue, '@rafiandsonsnr.com')) {
                $this->db->table('settings')->where('key', 'smtp_user')->update([
                    'value'      => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // No rollback — email domain change is intentional.
    }
}
