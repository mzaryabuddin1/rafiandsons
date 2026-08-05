<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExtendBannersForHomepage extends Migration
{
    public function up()
    {
        $this->forge->addColumn('banners', [
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'default'    => 'home_slider',
                'after'      => 'id',
            ],
            'subtitle' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'title',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'subtitle',
            ],
            'badge_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'description',
            ],
            'button_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'badge_text',
            ],
            'bg_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'image',
            ],
            'style' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'light',
                'after'      => 'bg_color',
            ],
        ]);

        $this->db->query('ALTER TABLE banners ADD INDEX position_status_sort (position, status, sort_order)');
    }

    public function down()
    {
        $this->forge->dropColumn('banners', [
            'position', 'subtitle', 'description', 'badge_text',
            'button_text', 'bg_color', 'style',
        ]);
    }
}
