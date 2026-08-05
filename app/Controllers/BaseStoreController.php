<?php

namespace App\Controllers;

use App\Libraries\CartService;
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
        helper(['url', 'form', 'text']);
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
        $data['pageTitle'] = $data['pageTitle'] ?? ($settings['site_name'] ?? 'Rafi & Sons');
        $data['activeMenu'] = $data['activeMenu'] ?? '';
        $data['showFixedCats'] = $data['showFixedCats'] ?? false;
        $data['cssFile'] = $data['cssFile'] ?? 'demo22.min.css';

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
}
