<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIconToCategories extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('icon', 'categories')) {
            $this->forge->addColumn('categories', [
                'icon' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 80,
                    'null'       => true,
                    'after'      => 'image',
                ],
            ]);
        }

        // Move FA class values previously stored in description into icon
        $rows = $this->db->table('categories')->select('id, description, icon')->get()->getResultArray();
        foreach ($rows as $row) {
            if (! empty($row['icon'])) {
                continue;
            }
            $desc = trim((string) ($row['description'] ?? ''));
            if ($desc === '') {
                continue;
            }

            $class = $this->extractFaClass($desc);
            if ($class === null) {
                continue;
            }

            $update = ['icon' => $class, 'updated_at' => date('Y-m-d H:i:s')];
            // Clear description only when it was solely an icon class
            if ($this->isIconOnlyDescription($desc)) {
                $update['description'] = null;
            }

            $this->db->table('categories')->where('id', $row['id'])->update($update);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('icon', 'categories')) {
            $this->forge->dropColumn('categories', 'icon');
        }
    }

    private function extractFaClass(string $value): ?string
    {
        $aliases = [
            'fa-snowflake-o'      => 'fa-snowflake',
            'fa-refresh'          => 'fa-sync-alt',
            'fa-sun-o'            => 'fa-sun',
            'fa-dot-circle-o'     => 'fa-dot-circle',
            'fa-phone-square'     => 'fa-mobile-alt',
            'fa-thermometer-full' => 'fa-temperature-high',
            'fa-battery-full'     => 'fa-car-battery',
            'fa-desktop'          => 'fa-tv',
            'fa-server'           => 'fa-door-closed',
            'fa-cube'             => 'fa-tshirt',
            'fa-bicycle'          => 'fa-motorcycle',
            'fa-bars'             => 'fa-box',
            'fa-tablet'           => 'fa-tablet-alt',
        ];

        $parts = preg_split('/\s+/', $value) ?: [];
        foreach ($parts as $part) {
            if (str_starts_with($part, 'fa-')) {
                return $aliases[$part] ?? $part;
            }
        }

        return null;
    }

    private function isIconOnlyDescription(string $value): bool
    {
        $clean = preg_replace('/\s+/', ' ', trim($value));
        if ($clean === null || $clean === '') {
            return false;
        }

        return (bool) preg_match('/^(fa\s+)?fa-[\w-]+$/i', $clean);
    }
}
