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
        'category_id', 'vendor_id', 'name', 'slug', 'sku', 'price', 'compare_price', 'images', 'description',
        'stock_status', 'cash_available', 'installment_available', 'status', 'meta_title', 'meta_description',
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

    /**
     * Resolved plans for a product (pivot overrides win over template values).
     *
     * @return list<array<string, mixed>>
     */
    public function plansForProduct(int $productId): array
    {
        $hasPivotDown    = $this->db->fieldExists('down_payment', 'product_installment_plans');
        $hasPivotMonthly = $this->db->fieldExists('monthly_installment', 'product_installment_plans');
        $hasPivotMonths  = $this->db->fieldExists('months', 'product_installment_plans');

        $downExpr    = $hasPivotDown ? 'COALESCE(pip.down_payment, ip.down_payment)' : 'ip.down_payment';
        $monthlyExpr = $hasPivotMonthly ? 'COALESCE(pip.monthly_installment, ip.monthly_installment)' : 'ip.monthly_installment';
        $monthsExpr  = $hasPivotMonths ? 'COALESCE(pip.months, ip.months)' : 'ip.months';

        $rows = $this->db->table('product_installment_plans pip')
            ->select("ip.id, ip.product_id, ip.name, ip.processing_charges, ip.terms, ip.status,
                {$downExpr} AS down_payment,
                {$monthlyExpr} AS monthly_installment,
                {$monthsExpr} AS months,
                ip.total_payable", false)
            ->join('installment_plans ip', 'ip.id = pip.installment_plan_id')
            ->where('pip.product_id', $productId)
            ->where('ip.deleted_at', null)
            ->where('ip.status', 1)
            ->orderBy('months', 'ASC')
            ->orderBy('ip.name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $down    = (float) $row['down_payment'];
            $monthly = (float) $row['monthly_installment'];
            $months  = max(1, (int) $row['months']);
            $row['down_payment']        = $down;
            $row['monthly_installment'] = $monthly;
            $row['months']              = $months;
            $row['total_payable']       = $down + ($monthly * $months) + (float) ($row['processing_charges'] ?? 0);
        }
        unset($row);

        return $rows;
    }

    /**
     * Resolve one plan for a product (for cart/checkout snapshots).
     */
    public function resolvePlan(int $productId, int $planId): ?array
    {
        foreach ($this->plansForProduct($productId) as $plan) {
            if ((int) $plan['id'] === $planId) {
                return $plan;
            }
        }

        return null;
    }

    /**
     * @deprecated Use syncProductPlans()
     */
    public function syncPlans(int $productId, array $planIds): void
    {
        $plans = [];
        foreach ($planIds as $planId) {
            $plans[] = ['id' => (int) $planId];
        }
        $this->syncProductPlans($productId, $plans);
    }

    /**
     * Sync product installment plans.
     * Each item: id? (existing plan), name, down_payment, monthly_installment, months
     * Creates product-owned plans (installment_plans.product_id = product).
     *
     * @param list<array<string, mixed>> $plans
     */
    public function syncProductPlans(int $productId, array $plans): void
    {
        $now = date('Y-m-d H:i:s');
        $keepIds = [];

        foreach ($plans as $plan) {
            $name    = trim((string) ($plan['name'] ?? ''));
            $down    = (float) ($plan['down_payment'] ?? 0);
            $monthly = (float) ($plan['monthly_installment'] ?? 0);
            $months  = max(1, (int) ($plan['months'] ?? 12));
            $total   = $down + ($monthly * $months);
            $planId  = ! empty($plan['id']) ? (int) $plan['id'] : 0;

            if ($name === '') {
                $name = $months . ' Month Plan';
            }

            $payload = [
                'product_id'          => $productId,
                'name'                => $name,
                'down_payment'        => $down,
                'monthly_installment' => $monthly,
                'months'              => $months,
                'total_payable'       => $total,
                'processing_charges'  => (float) ($plan['processing_charges'] ?? 0),
                'terms'               => $plan['terms'] ?? 'Subject to verification by Rafi & Sons.',
                'status'              => 1,
                'updated_at'          => $now,
                'deleted_at'          => null,
            ];

            if ($planId > 0) {
                $existing = $this->db->table('installment_plans')
                    ->where('id', $planId)
                    ->where('deleted_at', null)
                    ->get()
                    ->getFirstRow('array');

                // Only update if this plan belongs to the product, or convert a linked global copy into product-owned
                if ($existing && ((int) ($existing['product_id'] ?? 0) === $productId || $existing['product_id'] === null)) {
                    // If it was global, create a product-owned copy instead of mutating the template
                    if ($existing['product_id'] === null) {
                        $payload['created_at'] = $now;
                        $this->db->table('installment_plans')->insert($payload);
                        $planId = (int) $this->db->insertID();
                    } else {
                        $this->db->table('installment_plans')->where('id', $planId)->update($payload);
                    }
                } else {
                    $payload['created_at'] = $now;
                    $this->db->table('installment_plans')->insert($payload);
                    $planId = (int) $this->db->insertID();
                }
            } else {
                $payload['created_at'] = $now;
                $this->db->table('installment_plans')->insert($payload);
                $planId = (int) $this->db->insertID();
            }

            $keepIds[] = $planId;

            // Upsert pivot with same amounts as overrides (for COALESCE consistency)
            $pivot = $this->db->table('product_installment_plans')
                ->where('product_id', $productId)
                ->where('installment_plan_id', $planId)
                ->get()
                ->getFirstRow('array');

            $pivotData = [
                'product_id'          => $productId,
                'installment_plan_id' => $planId,
            ];
            if ($this->db->fieldExists('down_payment', 'product_installment_plans')) {
                $pivotData['down_payment'] = $down;
            }
            if ($this->db->fieldExists('monthly_installment', 'product_installment_plans')) {
                $pivotData['monthly_installment'] = $monthly;
            }
            if ($this->db->fieldExists('months', 'product_installment_plans')) {
                $pivotData['months'] = $months;
            }

            if ($pivot) {
                $this->db->table('product_installment_plans')->where('id', $pivot['id'])->update($pivotData);
            } else {
                $this->db->table('product_installment_plans')->insert($pivotData);
            }
        }

        // Remove pivot links not kept
        if ($keepIds === []) {
            $this->db->table('product_installment_plans')->where('product_id', $productId)->delete();
        } else {
            $this->db->table('product_installment_plans')
                ->where('product_id', $productId)
                ->whereNotIn('installment_plan_id', $keepIds)
                ->delete();
        }

        // Soft-delete product-owned plans no longer linked
        $owned = $this->db->table('installment_plans')
            ->where('product_id', $productId)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();
        foreach ($owned as $row) {
            if (! in_array((int) $row['id'], $keepIds, true)) {
                $this->db->table('installment_plans')->where('id', $row['id'])->update([
                    'status'     => 0,
                    'deleted_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
