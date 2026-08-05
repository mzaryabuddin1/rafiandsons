<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

/**
 * One-shot seeder for all Qist Bazaar clone data:
 * sliders, categories, products, section banners, subcategory images,
 * and per-product installment plans.
 *
 * Requires assets under public/assets/qist/ (manifest.json, _next_data.json, images).
 *
 * Usage:
 *   php spark db:seed QistBazaarSeeder
 */
class QistBazaarSeeder extends Seeder
{
    public function run()
    {
        $required = [
            FCPATH . 'assets/qist/manifest.json',
            FCPATH . 'assets/qist/_next_data.json',
            FCPATH . 'assets/qist/subcategory-images.json',
            FCPATH . 'assets/qist/section-banners.json',
        ];

        foreach ($required as $path) {
            if (! is_file($path)) {
                throw new \RuntimeException('Missing Qist asset: ' . $path);
            }
        }

        $this->step('1/3 Importing Qist sliders, categories, products...');
        $this->call('QistCloneSeeder');

        $this->step('2/3 Attaching section banners + subcategory images + shared plans...');
        $this->call('FixQistHomeAssetsSeeder');

        $this->step('3/3 Converting to product-owned installment plans...');
        $this->call('ConvertToProductOwnedPlansSeeder');

        $this->step('Done. Qist Bazaar catalog seeded successfully.');
    }

    private function step(string $message): void
    {
        if (is_cli()) {
            CLI::write($message, 'green');
        }
    }
}
