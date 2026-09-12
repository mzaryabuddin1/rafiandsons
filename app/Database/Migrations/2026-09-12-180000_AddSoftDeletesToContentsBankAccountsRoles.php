<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeletesToContentsBankAccountsRoles extends Migration
{
    public function up()
    {
        $column = [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        foreach (['contents', 'bank_accounts', 'roles'] as $table) {
            if (! $this->db->tableExists($table)) {
                continue;
            }
            if (! $this->db->fieldExists('deleted_at', $table)) {
                $this->forge->addColumn($table, $column);
            }
        }
    }

    public function down()
    {
        foreach (['contents', 'bank_accounts', 'roles'] as $table) {
            if ($this->db->tableExists($table) && $this->db->fieldExists('deleted_at', $table)) {
                $this->forge->dropColumn($table, 'deleted_at');
            }
        }
    }
}
