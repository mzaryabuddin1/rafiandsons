<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Converts linked shared plans + pivot overrides into product-owned plans.
 */
class ConvertToProductOwnedPlansSeeder extends Seeder
{
    public function run()
    {
        if (! $this->db->fieldExists('product_id', 'installment_plans')) {
            throw new \RuntimeException('Run migrations first (installment_plans.product_id missing).');
        }

        $now = date('Y-m-d H:i:s');
        $productModel = model(\App\Models\ProductModel::class);
        $products = $this->db->table('products')->where('deleted_at', null)->where('status', 1)->get()->getResultArray();

        foreach ($products as $product) {
            $resolved = $productModel->plansForProduct((int) $product['id']);
            if ($resolved === []) {
                continue;
            }

            $ownedPayload = [];
            foreach ($resolved as $plan) {
                $ownedPayload[] = [
                    'id'                  => null, // force new product-owned copy
                    'name'                => $plan['name'],
                    'down_payment'        => $plan['down_payment'],
                    'monthly_installment' => $plan['monthly_installment'],
                    'months'              => $plan['months'],
                ];
            }
            $productModel->syncProductPlans((int) $product['id'], $ownedPayload);
        }

        // Soft-delete leftover global plans that are only placeholders if desired — keep 6/12/18 templates
        $this->db->table('installment_plans')
            ->where('product_id', null)
            ->where('deleted_at', null)
            ->whereNotIn('name', ['6 Month Plan', '12 Month Plan', '18 Month Plan'])
            ->update([
                'status'     => 0,
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
    }
}
