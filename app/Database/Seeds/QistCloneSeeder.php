<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Imports downloaded Qist Bazaar homepage assets into local DB
 * (sliders, categories, featured products) for layout parity.
 */
class QistCloneSeeder extends Seeder
{
    public function run()
    {
        $manifestPath = FCPATH . 'assets/qist/manifest.json';
        $nextPath     = FCPATH . 'assets/qist/_next_data.json';

        if (! is_file($manifestPath) || ! is_file($nextPath)) {
            throw new \RuntimeException('Qist assets missing. Run _download_qist_assets.php first.');
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
        // Soft-delete existing home sliders
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
        // Free unique slugs: rename then soft-delete old categories
        $old = $this->db->table('categories')->where('deleted_at', null)->get()->getResultArray();
        foreach ($old as $row) {
            $this->db->table('categories')->where('id', $row['id'])->update([
                'slug'       => $row['slug'] . '-old-' . $row['id'],
                'status'     => 0,
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $map = []; // slug => id
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
        // Free unique product slugs
        $old = $this->db->table('products')->where('deleted_at', null)->get()->getResultArray();
        foreach ($old as $row) {
            $this->db->table('products')->where('id', $row['id'])->update([
                'slug'       => $row['slug'] . '-old-' . $row['id'],
                'status'     => 0,
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $sku = 1000;
        foreach ($items as $collection) {
            $sectionSlug = $collection['collectionSlug'] ?? '';
            $categoryId  = null;

            if ($sectionSlug === 'deal-of-the-day') {
                $categoryId = $categoryMap['mobiles'] ?? (reset($categoryMap) ?: null);
            } elseif (isset($categoryMap[$sectionSlug])) {
                $categoryId = $categoryMap[$sectionSlug];
            }

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
                $advance  = (float) ($product['DisplayAmount'] ?? 0);
                $price    = $advance > 0 ? $advance * 12 : 0;

                $this->db->table('products')->insert([
                    'category_id'           => $categoryId ?: null,
                    'name'                  => $title,
                    'slug'                  => $slug,
                    'sku'                   => 'RS-' . ($sku++),
                    'price'                 => $price,
                    'images'                => $localImg ? json_encode([$localImg]) : null,
                    'description'           => $title,
                    'stock_status'          => 'in_stock',
                    'installment_available' => 1,
                    'status'                => 1,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
                $productId = (int) $this->db->insertID();

                // Shared 12-month plan + per-product advance override (avoids plan duplicates)
                static $sharedPlanId = null;
                if ($sharedPlanId === null) {
                    $existing = $this->db->table('installment_plans')
                        ->where('name', '12 Month Plan')
                        ->where('deleted_at', null)
                        ->where('status', 1)
                        ->get()
                        ->getFirstRow('array');
                    if ($existing) {
                        $sharedPlanId = (int) $existing['id'];
                    } else {
                        $this->db->table('installment_plans')->insert([
                            'name'                => '12 Month Plan',
                            'down_payment'        => 5000,
                            'monthly_installment' => 5000,
                            'months'              => 12,
                            'total_payable'       => 60000,
                            'processing_charges'  => 0,
                            'terms'               => 'Subject to verification.',
                            'status'              => 1,
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ]);
                        $sharedPlanId = (int) $this->db->insertID();
                    }
                }

                $pivot = [
                    'product_id'          => $productId,
                    'installment_plan_id' => $sharedPlanId,
                ];
                if ($this->db->fieldExists('down_payment', 'product_installment_plans')) {
                    $pivot['down_payment']        = $advance;
                    $pivot['monthly_installment'] = $advance;
                }
                $this->db->table('product_installment_plans')->insert($pivot);
            }
        }
    }
}