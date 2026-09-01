<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetHostingerSmtpDefaults extends Migration
{
    public function up()
    {
        $updates = [
            'smtp_host'       => 'smtp.hostinger.com',
            'smtp_user'       => 'support@rafiandsonsnr.pk',
            'smtp_port'       => '465',
            'smtp_from_email' => 'support@rafiandsonsnr.pk',
            'contact_email'   => 'support@rafiandsonsnr.pk',
            'order_notify_email' => 'support@rafiandsonsnr.pk',
        ];

        foreach ($updates as $key => $value) {
            $row = $this->db->table('settings')->where('key', $key)->get()->getRowArray();
            if (! $row) {
                continue;
            }

            $current = (string) ($row['value'] ?? '');
            if ($current === '' || str_contains($current, 'sendgrid') || str_contains($current, '@rafiandsonsnr.com')) {
                $this->db->table('settings')->where('key', $key)->update([
                    'value'      => $value,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($this->db->fieldExists('value', 'settings')) {
            $crypto = $this->db->table('settings')->where('key', 'smtp_crypto')->get()->getRowArray();
            if (! $crypto) {
                $this->db->table('settings')->insert([
                    'key'        => 'smtp_crypto',
                    'value'      => 'ssl',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // No rollback.
    }
}
