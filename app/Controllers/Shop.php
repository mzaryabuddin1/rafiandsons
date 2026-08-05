<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Shop extends BaseStoreController
{
    public function index()
    {
        $search = trim((string) $this->request->getGet('q'));
        $categorySlug = trim((string) $this->request->getGet('category'));
        $sort = (string) $this->request->getGet('sort');

        $builder = db_connect()->table('products p')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->where('p.status', 1)
            ->where('p.deleted_at', null);

        $activeCategory = null;
        if ($categorySlug !== '') {
            $activeCategory = model(CategoryModel::class)->where('slug', $categorySlug)->where('status', 1)->first();
            if ($activeCategory) {
                $ids = model(CategoryModel::class)->idsWithChildren((int) $activeCategory['id']);
                $builder->whereIn('p.category_id', $ids);
            }
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('p.name', $search)
                ->orLike('p.sku', $search)
                ->orLike('p.description', $search)
                ->groupEnd();
        }

        switch ($sort) {
            case 'price_asc':
                $builder->orderBy('p.price', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('p.price', 'DESC');
                break;
            case 'name':
                $builder->orderBy('p.name', 'ASC');
                break;
            default:
                $builder->orderBy('p.id', 'DESC');
        }

        $products = $builder->get()->getResultArray();
        $products = $this->enrichProducts($products);

        return $this->storeView('shop', [
            'pageTitle'      => $activeCategory['name'] ?? 'Shop',
            'activeMenu'     => 'shop',
            'products'       => $products,
            'search'         => $search,
            'sort'           => $sort,
            'activeCategory' => $activeCategory,
            'bodyClass'      => 'store-qist',
            'cssFile'        => 'demo22.min.css',
        ]);
    }
}
