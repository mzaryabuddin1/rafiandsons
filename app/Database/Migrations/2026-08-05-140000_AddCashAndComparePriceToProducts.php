<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCashAndComparePriceToProducts extends Migration
{
    public function up()
    {
        $fields = [
            'compare_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'after'      => 'price',
            ],
            'cash_available' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'installment_available',
            ],
        ];

        $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['compare_price', 'cash_available']);
    }
}
