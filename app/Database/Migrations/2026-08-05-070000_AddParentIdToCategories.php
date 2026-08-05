<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToCategories extends Migration
{
    public function up()
    {
        $this->forge->addColumn('categories', [
            'parent_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'id',
            ],
        ]);

        $this->db->query('ALTER TABLE categories ADD INDEX idx_categories_parent_id (parent_id)');
        // Self-referencing FK (nullable)
        $this->db->query('ALTER TABLE categories ADD CONSTRAINT categories_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE categories DROP FOREIGN KEY categories_parent_id_foreign');
        $this->forge->dropColumn('categories', 'parent_id');
    }
}
