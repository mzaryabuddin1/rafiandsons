<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentTypeToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'payment_type' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'installment', 'mixed'],
                'default'    => 'cash',
                'after'      => 'customer_city',
            ],
        ]);

        $this->forge->addColumn('order_items', [
            'payment_type' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'installment'],
                'default'    => 'cash',
                'after'      => 'sku',
            ],
            'cash_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
                'after'      => 'payment_type',
            ],
            'installment_plan_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'cash_price',
            ],
            'plan_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'installment_plan_id',
            ],
            'down_payment' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
                'after'      => 'plan_name',
            ],
            'monthly_installment' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
                'after'      => 'down_payment',
            ],
            'months' => [
                'type'       => 'INT',
                'default'    => 0,
                'after'      => 'monthly_installment',
            ],
            'total_payable' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
                'after'      => 'months',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'payment_type');
        $this->forge->dropColumn('order_items', [
            'payment_type',
            'cash_price',
            'installment_plan_id',
            'plan_name',
            'down_payment',
            'monthly_installment',
            'months',
            'total_payable',
        ]);
    }
}
