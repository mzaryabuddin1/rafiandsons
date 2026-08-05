<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'order_id', 'product_id', 'product_name', 'sku', 'payment_type', 'cash_price',
        'installment_plan_id', 'plan_name', 'down_payment', 'monthly_installment', 'months',
        'total_payable', 'unit_price', 'quantity', 'line_total',
    ];
}
