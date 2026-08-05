<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * - Attaches Qist section banners + subcategory images
 * - Collapses duplicate installment plans into shared plans with per-product overrides
 */
class FixQistHomeAssetsSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $this->fixInstallmentPlans($now);
        $this->attachSubcategoryImages();
        $this->attachSectionBanners($now);
    }

    private function fixInstallmentPlans(string $now): void
    {
        $db = $this->db;

        // Ensure override columns exist (migration should have run)
        if (! $db->fieldExists('down_payment', 'product_installment_plans')) {
            throw new \RuntimeException('Run migrations first (product_installment_plans.down_payment missing).');
        }

        // Copy current plan amounts onto pivots as product-specific overrides
        $links = $db->query(
            'SELECT pip.id, pip.product_id, pip.installment_plan_id, ip.down_payment, ip.monthly_installment
             FROM product_installment_plans pip
             JOIN installment_plans ip ON ip.id = pip.installment_plan_id'
        )->getResultArray();

        foreach ($links as $row) {
            $db->table('product_installment_plans')->where('id', $row['id'])->update([
                'down_payment'        => $row['down_payment'],
                'monthly_installment' => $row['monthly_installment'],
            ]);
        }

        // Soft-delete / rename all existing plans
        foreach ($db->table('installment_plans')->where('deleted_at', null)->get()->getResultArray() as $plan) {
            $db->table('installment_plans')->where('id', $plan['id'])->update([
                'name'       => $plan['name'] . ' (old #' . $plan['id'] . ')',
                'status'     => 0,
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Create 3 shared plans
        $shared = [
            ['name' => '6 Month Plan', 'months' => 6, 'down_payment' => 5000, 'monthly_installment' => 5000, 'total_payable' => 30000],
            ['name' => '12 Month Plan', 'months' => 12, 'down_payment' => 5000, 'monthly_installment' => 5000, 'total_payable' => 60000],
            ['name' => '18 Month Plan', 'months' => 18, 'down_payment' => 5000, 'monthly_installment' => 5000, 'total_payable' => 90000],
        ];
        $sharedIds = [];
        foreach ($shared as $plan) {
            $db->table('installment_plans')->insert([
                'name'                => $plan['name'],
                'down_payment'        => $plan['down_payment'],
                'monthly_installment' => $plan['monthly_installment'],
                'months'              => $plan['months'],
                'total_payable'       => $plan['total_payable'],
                'processing_charges'  => 0,
                'terms'               => 'Subject to verification by Rafi & Sons.',
                'status'              => 1,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
            $sharedIds[$plan['months']] = (int) $db->insertID();
        }

        // Rebuild pivots: one row per product pointing to 12-month shared plan, keep override amounts
        $byProduct = [];
        foreach ($links as $row) {
            $pid = (int) $row['product_id'];
            if (! isset($byProduct[$pid])) {
                $byProduct[$pid] = $row;
            }
        }

        $db->table('product_installment_plans')->emptyTable();
        foreach ($byProduct as $pid => $row) {
            $db->table('product_installment_plans')->insert([
                'product_id'          => $pid,
                'installment_plan_id' => $sharedIds[12],
                'down_payment'        => $row['down_payment'],
                'monthly_installment' => $row['monthly_installment'],
            ]);
        }
    }

    private function attachSubcategoryImages(): void
    {
        $path = FCPATH . 'assets/qist/subcategory-images.json';
        if (! is_file($path)) {
            return;
        }
        $rows = json_decode(file_get_contents($path), true) ?: [];
        foreach ($rows as $row) {
            $slug = $row['slug'] ?? '';
            $file = $row['file'] ?? '';
            if ($slug === '' || $file === '' || ! is_file(FCPATH . $file)) {
                continue;
            }
            $this->db->table('categories')
                ->where('slug', $slug)
                ->where('deleted_at', null)
                ->update(['image' => $file, 'updated_at' => date('Y-m-d H:i:s')]);
        }
    }

    private function attachSectionBanners(string $now): void
    {
        $path = FCPATH . 'assets/qist/section-banners.json';
        if (! is_file($path)) {
            return;
        }
        $rows = json_decode(file_get_contents($path), true) ?: [];

        // Soft-delete old category section banners
        $this->db->table('banners')
            ->where('position', 'category_section')
            ->where('deleted_at', null)
            ->update(['deleted_at' => $now, 'status' => 0, 'updated_at' => $now]);

        $sort = 1;
        foreach ($rows as $row) {
            $this->db->table('banners')->insert([
                'position'    => 'category_section',
                'title'       => $row['title'] ?? $row['slug'],
                'subtitle'    => $row['slug'] ?? '',
                'description' => null,
                'badge_text'  => null,
                'button_text' => 'View All',
                'image'       => $row['file'],
                'bg_color'    => null,
                'style'       => 'image',
                'link'        => 'shop?category=' . urlencode($row['slug'] ?? ''),
                'sort_order'  => $sort++,
                'status'      => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
