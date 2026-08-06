<?php

namespace App\Controllers;

use App\Libraries\CartService;
use App\Libraries\StoreAuth;
use App\Models\CategoryModel;
use App\Models\ContentModel;
use App\Models\SettingModel;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseStoreController extends BaseController
{
    protected CartService $cart;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['url', 'form', 'text', 'store']);
        $this->cart = new CartService();
    }

    protected function storeView(string $view, array $data = [])
    {
        $settings = model(SettingModel::class)->getMap();
        $data['settings'] = $settings;
        $data['categoryTree'] = model(CategoryModel::class)->tree(true);
        $data['categories'] = model(CategoryModel::class)->parentsOnly(true);
        $data['cartCount'] = $this->cart->count();
        $data['cartItems'] = $this->cart->items();
        $data['cartSubtotal'] = $this->cart->subtotal();
        $data['storeCustomer'] = (new StoreAuth())->user();
        $data['pageTitle'] = $data['pageTitle'] ?? ($settings['site_name'] ?? 'Rafi & Sons');
        $data['activeMenu'] = $data['activeMenu'] ?? '';
        $data['showFixedCats'] = $data['showFixedCats'] ?? false;
        $data['cssFile'] = $data['cssFile'] ?? 'demo22.min.css';
        $data['bodyClass'] = $data['bodyClass'] ?? 'store-qist';

        return view('store/' . $view, $data);
    }

    protected function jsonSuccess(string $message = 'OK', $data = null, int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function jsonError(string $message = 'Error', $data = null, int $code = 400): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function productImage(?string $imagesJson): string
    {
        $images = $imagesJson ? json_decode($imagesJson, true) : [];
        $path = $images[0] ?? 'theme/images/demos/demo22/products/1.jpg';

        return base_url($path);
    }

    protected function getContent(string $slug): ?array
    {
        return model(ContentModel::class)->where('slug', $slug)->where('status', 1)->first();
    }

    /**
     * Attach category names and minimum plan down payment (Advance).
     */
    protected function enrichProducts(array $products): array
    {
        if ($products === []) {
            return [];
        }

        $db  = db_connect();
        $ids = array_map('intval', array_column($products, 'id'));

        $catIds = array_values(array_unique(array_filter(array_column($products, 'category_id'))));
        $catMap = [];
        if ($catIds) {
            foreach ($db->table('categories')->whereIn('id', $catIds)->get()->getResultArray() as $cat) {
                $catMap[(int) $cat['id']] = $cat;
            }
        }

        $advanceMap = [];
        $installmentIds = array_values(array_filter($ids, static function ($id) use ($products) {
            foreach ($products as $product) {
                if ((int) ($product['id'] ?? 0) === $id) {
                    return (int) ($product['installment_available'] ?? 0) === 1;
                }
            }

            return false;
        }));

        if ($installmentIds) {
            $rows = $db->table('product_installment_plans pip')
                ->select('pip.product_id, MIN(COALESCE(pip.down_payment, ip.down_payment)) as min_advance')
                ->join('installment_plans ip', 'ip.id = pip.installment_plan_id')
                ->whereIn('pip.product_id', $installmentIds)
                ->groupBy('pip.product_id')
                ->get()
                ->getResultArray();
            foreach ($rows as $row) {
                $advanceMap[(int) $row['product_id']] = (float) $row['min_advance'];
            }
        }

        foreach ($products as &$product) {
            $cat = $catMap[(int) ($product['category_id'] ?? 0)] ?? null;
            $product['category_name'] = $cat['name'] ?? 'Product';
            $product['category_slug'] = $cat['slug'] ?? '';
            $product['cash_available'] = (int) ($product['cash_available'] ?? 1);
            $product['installment_available'] = (int) ($product['installment_available'] ?? 0);
            $product['compare_price'] = isset($product['compare_price']) && $product['compare_price'] !== ''
                ? (float) $product['compare_price']
                : null;
            $product['min_advance'] = $product['installment_available'] === 1
                ? ($advanceMap[(int) $product['id']] ?? null)
                : null;
        }
        unset($product);

        return $products;
    }

    protected function productsForCategory(int $categoryId, int $limit = 8): array
    {
        $ids = model(CategoryModel::class)->idsWithChildren($categoryId);
        $products = db_connect()->table('products')
            ->where('status', 1)
            ->where('deleted_at', null)
            ->whereIn('category_id', $ids)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        return $this->enrichProducts($products);
    }
}
