<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerAuthFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('customers', [
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email',
            ],
            'profile_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'password',
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'profile_image',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('customers', ['password', 'profile_image', 'last_login_at']);
    }
}
