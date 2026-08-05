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
        $categories = model(CategoryModel::class)->parentsOnly(true);

        // Attach subcategories (or products) for "Top Categories" lists
        $topCategories = [];
        foreach (array_slice($categories, 0, 6) as $index => $cat) {
            $children = model(CategoryModel::class)->childrenOf((int) $cat['id'], true);
            $cat['products'] = [];
            if ($children) {
                foreach ($children as $child) {
                    $cat['products'][] = [
                        'slug' => $child['slug'],
                        'name' => $child['name'],
                    ];
                }
            } else {
                $products = $productModel
                    ->where('status', 1)
                    ->where('category_id', $cat['id'])
                    ->orderBy('id', 'DESC')
                    ->findAll(5);
                $cat['products'] = $products;
            }
            $cat['image_file'] = ! empty($cat['image'])
                ? $cat['image']
                : 'theme/images/demos/demo22/categories/' . (($index % 6) + 1) . '.png';
            $topCategories[] = $cat;
        }

        $bannerModel = model(BannerModel::class);

        return $this->storeView('home', [
            'pageTitle'     => 'Home',
            'activeMenu'    => 'home',
            'showFixedCats' => true,
            'homeSliders'   => $bannerModel->activeByPosition(BannerModel::POSITION_HOME_SLIDER),
            'homeSide'      => $bannerModel->activeByPosition(BannerModel::POSITION_HOME_SIDE),
            'homeMid'       => $bannerModel->activeByPosition(BannerModel::POSITION_HOME_MID),
            'featured'      => $productModel->where('status', 1)->orderBy('id', 'DESC')->findAll(8),
            'topCategories' => $topCategories,
            'cssFile'       => 'demo22.min.css',
        ]);
    }
}
