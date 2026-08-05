<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Search extends BaseStoreController
{
    public function index()
    {
        $params = array_filter([
            'q'        => trim((string) $this->request->getGet('q')),
            'category' => trim((string) $this->request->getGet('category')),
        ], static fn ($v) => $v !== '');

        $url = site_url('shop') . ($params !== [] ? '?' . http_build_query($params) : '');

        return redirect()->to($url);
    }

    public function suggest(): ResponseInterface
    {
        $query    = trim((string) $this->request->getGet('q'));
        $category = trim((string) $this->request->getGet('category'));

        if (mb_strlen($query) < 2) {
            return $this->jsonSuccess('OK', [
                'items' => [],
                'total' => 0,
                'query' => $query,
            ]);
        }

        $builder = db_connect()->table('products p')
            ->select('p.id, p.name, p.slug, p.price, p.images, c.name as category_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->where('p.status', 1)
            ->where('p.deleted_at', null);

        if ($category !== '') {
            $cat = model(\App\Models\CategoryModel::class)->where('slug', $category)->where('status', 1)->first();
            if ($cat) {
                $ids = model(\App\Models\CategoryModel::class)->idsWithChildren((int) $cat['id']);
                $builder->whereIn('p.category_id', $ids);
            }
        }

        $builder->groupStart()
            ->like('p.name', $query)
            ->orLike('p.sku', $query)
            ->orLike('p.description', $query)
            ->orLike('c.name', $query)
            ->groupEnd();

        $total = (int) (clone $builder)->countAllResults(false);

        $products = $builder->orderBy('p.id', 'DESC')->limit(8)->get()->getResultArray();
        $products = $this->enrichProducts($products);

        $items = [];
        foreach ($products as $product) {
            $images = $product['images'] ? json_decode($product['images'], true) : [];
            $img    = $images[0] ?? 'theme/images/demos/demo22/products/1.jpg';

            $items[] = [
                'id'                    => (int) $product['id'],
                'name'                  => $product['name'],
                'slug'                  => $product['slug'],
                'price'                 => (float) $product['price'],
                'price_label'           => 'PKR ' . number_format((float) $product['price'], 0),
                'category_name'         => $product['category_name'] ?? '',
                'image'                 => base_url($img),
                'url'                   => site_url('product/' . $product['slug']),
                'min_advance'           => $product['min_advance'] ?? null,
                'cash_available'        => (int) ($product['cash_available'] ?? 1),
                'installment_available' => (int) ($product['installment_available'] ?? 0),
            ];
        }

        return $this->jsonSuccess('OK', [
            'items' => $items,
            'total' => $total,
            'query' => $query,
        ]);
    }
}
