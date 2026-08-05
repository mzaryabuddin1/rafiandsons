<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'category_id', 'name', 'slug', 'sku', 'price', 'images', 'description',
        'stock_status', 'installment_available', 'status', 'meta_title', 'meta_description',
    ];

    public function planIds(int $productId): array
    {
        $rows = $this->db->table('product_installment_plans')
            ->select('installment_plan_id')
            ->where('product_id', $productId)
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($rows, 'installment_plan_id'));
    }

    public function syncPlans(int $productId, array $planIds): void
    {
        $this->db->table('product_installment_plans')->where('product_id', $productId)->delete();
        $planIds = array_values(array_unique(array_filter(array_map('intval', $planIds))));
        foreach ($planIds as $planId) {
            $this->db->table('product_installment_plans')->insert([
                'product_id'          => $productId,
                'installment_plan_id' => $planId,
            ]);
        }
    }
}
