<?php

namespace App\Models;

use CodeIgniter\Model;

class InstallmentPlanModel extends Model
{
    protected $table            = 'installment_plans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'product_id', 'name', 'down_payment', 'monthly_installment', 'months', 'total_payable',
        'processing_charges', 'terms', 'status',
    ];

    /** Global reusable templates (not tied to one product). */
    public function globalActive(): array
    {
        return $this->where('status', 1)
            ->where('product_id', null)
            ->orderBy('months', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
