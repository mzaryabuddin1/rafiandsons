<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Imports Qist Bazaar homepage data into local DB:
 * sliders, categories (with subcategories), products, and installment pivots.
 *
 * Requires: public/assets/qist/manifest.json, _next_data.json
 * Run via: php spark db:seed QistBazaarSeeder
 */
class QistCloneSeeder extends Seeder
{
    public function run()
    {
        $manifestPath = FCPATH . 'assets/qist/manifest.json';
        $nextPath     = FCPATH . 'assets/qist/_next_data.json';

        if (! is_file($manifestPath) || ! is_file($nextPath)) {
            throw new \RuntimeException('Qist assets missing. Ensure public/assets/qist/manifest.json and _next_data.json exist.');
        }

        $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
        $raw      = preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($nextPath));
        $next     = json_decode($raw, true);
        $pp       = $next['props']['pageProps'] ?? [];
        $now      = date('Y-m-d H:i:s');

        $this->seedSliders($manifest['sliders'] ?? [], $now);
        $categoryMap = $this->seedCategories($pp['categroiesCollection'] ?? [], $now);
        $this->seedProducts($pp['items'] ?? [], $categoryMap, $now);
    }

    private function seedSliders(array $sliders, string $now): void
    {
        $this->db->table('banners')
            ->where('position', 'home_slider')
            ->update(['deleted_at' => $now, 'status' => 0, 'updated_at' => $now]);

        usort($sliders, static fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        $sort = 1;
        foreach ($sliders as $slide) {
            $this->db->table('banners')->insert([
                'position'    => 'home_slider',
                'title'       => '',
                'subtitle'    => '',
                'description' => '',
                'badge_text'  => '',
                'button_text' => '',
                'image'       => $slide['file'],
                'bg_color'    => null,
                'style'       => 'image',
                'link'        => 'shop',
                'sort_order'  => $sort++,
                'status'      => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }

    private function seedCategories(array $collection, string $now): array
    {
        $old = $this->db->table('categories')->where('deleted_at', null)->get()->getResultArray();
        foreach ($old as $row) {
            $this->db->table('categories')->where('id', $row['id'])->update([
                'slug'       => $row['slug'] . '-old-' . $row['id'],
                'status'     => 0,
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $map = [];
        $sort = 1;

        foreach ($collection as $cat) {
            $slug = trim((string) ($cat['slug'] ?? ''));
            $name = trim((string) ($cat['CategoryName'] ?? ''));
            if ($slug === '' || $name === '') {
                continue;
            }

            $imagePath = ltrim((string) ($cat['categoryImage'] ?? ''), '/');
            $localImage = $imagePath !== ''
                ? 'assets/qist/categories/' . basename($imagePath)
                : null;

            $this->db->table('categories')->insert([
                'parent_id'   => null,
                'name'        => $name,
                'slug'        => $slug,
                'image'       => $localImage,
                'description' => $cat['catIcon'] ?? null,
                'status'      => 1,
                'sort_order'  => $sort++,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $parentId = (int) $this->db->insertID();
            $map[$slug] = $parentId;

            $childSort = 1;
            foreach ($cat['subCategories'] ?? [] as $sub) {
                $childSlug = trim((string) ($sub['slug'] ?? ''));
                $childName = trim((string) ($sub['name'] ?? ''));
                if ($childSlug === '' || $childName === '') {
                    continue;
                }
                if (isset($map[$childSlug])) {
                    $childSlug .= '-' . $parentId;
                }

                $subImagePath = ltrim((string) ($sub['subCategoryImage'] ?? ''), '/');
                $subLocal = $subImagePath !== ''
                    ? 'assets/qist/categories/' . basename($subImagePath)
                    : null;

                $this->db->table('categories')->insert([
                    'parent_id'   => $parentId,
                    'name'        => $childName,
                    'slug'        => $childSlug,
                    'image'       => $subLocal,
                    'description' => null,
                    'status'      => 1,
                    'sort_order'  => $childSort++,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $map[$childSlug] = (int) $this->db->insertID();
            }
        }

        return $map;
    }

    private function seedProducts(array $items, array $categoryMap, string $now): void
    {
        $old = $this->db->table('products')->where('deleted_at', null)->get()->getResultArray();
        foreach ($old as $row) {
            $this->db->table('product_installment_plans')->where('product_id', (int) $row['id'])->delete();
            $this->db->table('products')->where('id', $row['id'])->update([
                'slug'       => $row['slug'] . '-old-' . $row['id'],
                'status'     => 0,
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $subsByParent = $this->loadSubcategoriesByParent();
        $sharedPlanId = $this->ensureSharedPlan($now);
        $sku          = 1000;
        $productIndex = 0;

        foreach ($items as $collection) {
            $sectionSlug = trim((string) ($collection['collectionSlug'] ?? ''));

            foreach ($collection['data'] ?? [] as $product) {
                $slug  = trim((string) ($product['slug'] ?? ''));
                $title = trim((string) ($product['title'] ?? ''));
                if ($slug === '' || $title === '') {
                    continue;
                }

                $baseSlug = $slug;
                $i        = 1;
                while ($this->db->table('products')->where('slug', $slug)->countAllResults() > 0) {
                    $slug = $baseSlug . '-' . $i++;
                }

                $imgPath  = ltrim((string) ($product['productImage'] ?? ''), '/');
                $localImg = $imgPath !== '' ? 'assets/qist/products/' . basename($imgPath) : null;

                $monthly    = max(0, (float) ($product['DisplayAmount'] ?? 0));
                $oldMonthly = max(0, (float) ($product['OldDisplayAmount'] ?? 0));
                $productCost = max(0, (float) ($product['productCost'] ?? 0));

                $price = $productCost > 0
                    ? $productCost
                    : ($monthly > 0 ? round($monthly * 12, 2) : 0);

                $compare = null;
                if ($oldMonthly > 0 && ($oldMonthly * 12) > $price) {
                    $compare = round($oldMonthly * 12, 2);
                } elseif ($price > 0) {
                    $compare = round($price * 1.08, -2);
                    if ($compare <= $price) {
                        $compare = null;
                    }
                }

                [$cashAvailable, $installmentAvailable] = $this->resolvePaymentModes($monthly, $productIndex);

                $categoryId = $this->resolveCategoryId($sectionSlug, $title, $categoryMap, $subsByParent, $productIndex);

                $this->db->table('products')->insert([
                    'category_id'           => $categoryId,
                    'name'                  => $title,
                    'slug'                  => $slug,
                    'sku'                   => 'QB-' . ($sku++),
                    'price'                 => $price,
                    'compare_price'         => $compare,
                    'images'                => $localImg ? json_encode([$localImg]) : null,
                    'description'           => $title,
                    'stock_status'          => 'in_stock',
                    'cash_available'        => $cashAvailable,
                    'installment_available' => $installmentAvailable,
                    'status'                => 1,
                    'meta_title'            => $title . ' | Rafi & Sons',
                    'meta_description'      => $title,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
                $productId = (int) $this->db->insertID();

                if ($installmentAvailable && $monthly > 0) {
                    $pivot = [
                        'product_id'          => $productId,
                        'installment_plan_id' => $sharedPlanId,
                    ];
                    if ($this->db->fieldExists('down_payment', 'product_installment_plans')) {
                        $pivot['down_payment']        = $monthly;
                        $pivot['monthly_installment'] = $monthly;
                    }
                    if ($this->db->fieldExists('months', 'product_installment_plans')) {
                        $pivot['months'] = 12;
                    }
                    $this->db->table('product_installment_plans')->insert($pivot);
                }

                $productIndex++;
            }
        }
    }

    /**
     * @return array<int, list<array<string, mixed>>>
     */
    private function loadSubcategoriesByParent(): array
    {
        $grouped = [];
        $rows = $this->db->table('categories')
            ->where('parent_id IS NOT NULL', null, false)
            ->where('status', 1)
            ->where('deleted_at', null)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $parentId = (int) $row['parent_id'];
            $grouped[$parentId][] = $row;
        }

        return $grouped;
    }

    private function resolveCategoryId(
        string $sectionSlug,
        string $title,
        array $categoryMap,
        array $subsByParent,
        int $productIndex
    ): ?int {
        $parentSlug = $sectionSlug === 'deal-of-the-day' ? 'mobiles' : $sectionSlug;
        $parentId   = $categoryMap[$parentSlug] ?? null;

        if (! $parentId) {
            return reset($categoryMap) ?: null;
        }

        $subs = $subsByParent[$parentId] ?? [];
        if ($subs === []) {
            return $parentId;
        }

        $titleNorm = strtolower(preg_replace('/\s+/', ' ', $title) ?? $title);

        foreach ($subs as $sub) {
            $name = strtolower(trim((string) $sub['name']));
            if ($name === '') {
                continue;
            }
            if (str_contains($titleNorm, $name)) {
                return (int) $sub['id'];
            }
        }

        $brandHints = [
            'samsung'  => ['samsung'],
            'infinix'  => ['infinix'],
            'tecno'    => ['tecno'],
            'itel'     => ['itel'],
            'vivo'     => ['vivo'],
            'redmi'    => ['redmi'],
            'oppo'     => ['oppo'],
            'dawlance' => ['dawlance'],
            'haier'    => ['haier'],
            'pel'      => ['pel', ' pels '],
            'tcl'      => ['tcl'],
            'xiaomi'   => ['xiaomi'],
            'hisense'  => ['hisense'],
            'hp'       => ['hp ', ' hp-', 'elitebook', 'probook'],
            'dell'     => ['dell', 'latitude'],
            'lenovo'   => ['lenovo', 'thinkpad', 'chromebook'],
            'acer'     => ['acer'],
            'apple'    => ['apple', 'ipad', 'macbook'],
            'fujitsu'  => ['fujitsu'],
            'super'    => ['superpower', 'super power'],
            'revoo'    => ['revoo'],
            'united'   => ['united'],
            'evee'     => ['evee'],
            'crown'    => ['crown'],
            'hi-speed' => ['hi-speed', 'hispeed'],
            'westpoint'=> ['westpoint'],
            'anex'     => ['anex'],
            'exide'    => ['exide'],
        ];

        foreach ($subs as $sub) {
            $subSlug = strtolower((string) $sub['slug']);
            foreach ($brandHints as $key => $needles) {
                if (! str_contains($subSlug, $key)) {
                    continue;
                }
                foreach ($needles as $needle) {
                    if (str_contains($titleNorm, $needle)) {
                        return (int) $sub['id'];
                    }
                }
            }
        }

        return (int) $subs[$productIndex % count($subs)]['id'];
    }

    /**
     * @return array{0:int,1:int} [cash_available, installment_available]
     */
    private function resolvePaymentModes(float $monthly, int $productIndex): array
    {
        if ($monthly <= 0) {
            return [1, 0];
        }

        // Most Qist products: cash + installment
        if ($productIndex % 12 === 0) {
            return [1, 0]; // cash only (no installment plans attached)
        }

        if ($productIndex % 17 === 0) {
            return [0, 1]; // installment only
        }

        return [1, 1];
    }

    private function ensureSharedPlan(string $now): int
    {
        $existing = $this->db->table('installment_plans')
            ->where('name', '12 Month Plan')
            ->where('deleted_at', null)
            ->where('status', 1)
            ->get()
            ->getFirstRow('array');

        if ($existing) {
            return (int) $existing['id'];
        }

        $this->db->table('installment_plans')->insert([
            'name'                => '12 Month Plan',
            'down_payment'        => 5000,
            'monthly_installment' => 5000,
            'months'              => 12,
            'total_payable'       => 60000,
            'processing_charges'  => 0,
            'terms'               => 'Subject to verification by Rafi & Sons.',
            'status'              => 1,
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        return (int) $this->db->insertID();
    }
}
