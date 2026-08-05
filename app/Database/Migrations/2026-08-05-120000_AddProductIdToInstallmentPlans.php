<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductIdToInstallmentPlans extends Migration
{
    public function up()
    {
        $this->forge->addColumn('installment_plans', [
            'product_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        // Optional: product-specific months override on pivot
        if (! $this->db->fieldExists('months', 'product_installment_plans')) {
            $this->forge->addColumn('product_installment_plans', [
                'months' => [
                    'type'       => 'INT',
                    'null'       => true,
                    'after'      => 'monthly_installment',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('months', 'product_installment_plans')) {
            $this->forge->dropColumn('product_installment_plans', 'months');
        }
        $this->forge->dropColumn('installment_plans', 'product_id');
    }
}
