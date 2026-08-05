<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Home extends BaseStoreController
{
    public function index()
    {
        $productModel = model(ProductModel::class);
        $categories   = model(CategoryModel::class)->parentsOnly(true);
        $bannerModel  = model(BannerModel::class);

        $topCategories = [];
        foreach ($categories as $index => $cat) {
            $cat['icon'] = category_fa_icon($cat['slug'] ?? '', $cat['description'] ?? null);
            $cat['image_file'] = ! empty($cat['image'])
                ? $cat['image']
                : 'theme/images/demos/demo22/categories/' . (($index % 6) + 1) . '.png';
            $topCategories[] = $cat;
        }

        $featured = $this->enrichProducts(
            $productModel->where('status', 1)->orderBy('id', 'DESC')->findAll(12)
        );

        // Prefer Qist homepage section order when available
        $sectionOrder = ['mobiles', 'refrigerator', 'laptops', 'led-tv', 'bikes', 'air-conditioner'];
        $bySlug = [];
        foreach ($categories as $cat) {
            $bySlug[$cat['slug']] = $cat;
        }

        $sectionCats = [];
        foreach ($sectionOrder as $slug) {
            if (isset($bySlug[$slug])) {
                $sectionCats[] = $bySlug[$slug];
            }
        }
        // Fallback / extras
        foreach ($categories as $cat) {
            if (! in_array($cat['slug'], $sectionOrder, true)) {
                $sectionCats[] = $cat;
            }
            if (count($sectionCats) >= 8) {
                break;
            }
        }

        $sectionBanners = [];
        foreach ($bannerModel->activeByPosition(BannerModel::POSITION_CATEGORY_SECTION) as $banner) {
            $key = trim((string) ($banner['subtitle'] ?: $banner['title']));
            if ($key !== '') {
                $sectionBanners[strtolower($key)] = $banner;
            }
        }

        $categorySections = [];
        foreach ($sectionCats as $cat) {
            $products = $this->productsForCategory((int) $cat['id'], 8);
            if ($products) {
                $categorySections[] = [
                    'category' => $cat,
                    'products' => $products,
                    'banner'   => $sectionBanners[$cat['slug']] ?? null,
                ];
            }
        }

        return $this->storeView('home', [
            'pageTitle'        => 'Buy on Easy Installments',
            'activeMenu'       => 'home',
            'showFixedCats'    => false,
            'homeSliders'      => $bannerModel->activeByPosition(BannerModel::POSITION_HOME_SLIDER),
            'homeMid'          => $bannerModel->activeByPosition(BannerModel::POSITION_HOME_MID),
            'featured'         => $featured,
            'topCategories'    => $topCategories,
            'categorySections' => $categorySections,
            'bodyClass'        => 'home store-qist',
            'cssFile'          => 'demo22.min.css',
        ]);
    }
}
