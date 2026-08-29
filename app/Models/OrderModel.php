<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'order_number', 'customer_id', 'customer_name', 'customer_email', 'customer_phone',
        'customer_cnic', 'customer_address', 'customer_city', 'payment_type', 'installment_plan_id',
        'plan_name', 'down_payment', 'monthly_installment', 'months', 'processing_charges',
        'total_payable', 'subtotal', 'status', 'admin_notes',
        'receipt_image', 'payment_verified', 'payment_verified_at',
    ];

    public const STATUSES = [
        'new'                 => 'New',
        'under_review'        => 'Under Review',
        'customer_contacted'  => 'Customer Contacted',
        'approved'            => 'Approved',
        'rejected'            => 'Rejected',
        'processing'          => 'Processing',
        'completed'           => 'Completed',
        'cancelled'           => 'Cancelled',
    ];
}
