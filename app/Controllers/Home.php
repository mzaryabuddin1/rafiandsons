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
        $catIcons = [
            'electronics'     => 'fa-mobile-alt',
            'home-appliances' => 'fa-blender',
            'computers'       => 'fa-laptop',
            'fashion'         => 'fa-tshirt',
            'beauty'          => 'fa-spa',
            'furniture'       => 'fa-couch',
        ];
        foreach ($categories as $index => $cat) {
            $cat['icon'] = $catIcons[$cat['slug']] ?? 'fa-box';
            $cat['image_file'] = ! empty($cat['image'])
                ? $cat['image']
                : 'theme/images/demos/demo22/categories/' . (($index % 6) + 1) . '.png';
            $topCategories[] = $cat;
        }

        $featured = $this->enrichProducts(
            $productModel->where('status', 1)->orderBy('id', 'DESC')->findAll(12)
        );

        $categorySections = [];
        foreach (array_slice($categories, 0, 6) as $cat) {
            $products = $this->productsForCategory((int) $cat['id'], 8);
            if ($products) {
                $categorySections[] = [
                    'category' => $cat,
                    'products' => $products,
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
