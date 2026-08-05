<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlanOverridesToProductPlans extends Migration
{
    public function up()
    {
        $this->forge->addColumn('product_installment_plans', [
            'down_payment' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'after'      => 'installment_plan_id',
            ],
            'monthly_installment' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'after'      => 'down_payment',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('product_installment_plans', ['down_payment', 'monthly_installment']);
    }
}
