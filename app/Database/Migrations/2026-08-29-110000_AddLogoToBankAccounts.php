<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLogoToBankAccounts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bank_accounts', [
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'bank_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bank_accounts', 'logo');
    }
}
