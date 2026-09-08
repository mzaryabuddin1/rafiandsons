<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\VendorModel;

class ProductsController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('products/index', [
            'pageTitle'  => 'Products',
            'activeMenu' => 'products',
            'canCreate'  => $this->auth->can('products.create'),
            'canUpdate'  => $this->auth->can('products.update'),
            'canDelete'  => $this->auth->can('products.delete'),
            'categories' => model(CategoryModel::class)->flatOptions(),
            'vendors'    => model(VendorModel::class)->approvedOptions(),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('products.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $builder = db_connect()->table('products p')
            ->select('p.*, c.name as category_name, v.business_name as vendor_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('vendors v', 'v.id = p.vendor_id AND v.deleted_at IS NULL', 'left')
            ->where('p.deleted_at', null);

        if ($query['search'] !== '') {
            $builder->groupStart()
                ->like('p.name', $query['search'])
                ->orLike('p.sku', $query['search'])
                ->orLike('p.slug', $query['search'])
                ->orLike('v.business_name', $query['search'])
                ->groupEnd();
        }

        [$rows, $total] = $this->paginateBuilder($builder, $query, static function ($b) {
            $b->orderBy('p.id', 'DESC');
        });

        if ($query['export']) {
            $csvRows = [];
            foreach ($rows as $row) {
                $cash = (int) ($row['cash_available'] ?? 0) === 1;
                $inst = (int) ($row['installment_available'] ?? 0) === 1;
                $payment = $cash && $inst ? 'Cash + Installment' : ($cash ? 'Cash only' : ($inst ? 'Installment only' : '-'));
                $csvRows[] = [
                    'id'            => $row['id'],
                    'name'          => $row['name'],
                    'sku'           => $row['sku'] ?? '',
                    'category_name' => $row['category_name'] ?? '',
                    'vendor_name'   => $row['vendor_name'] ?? '',
                    'price'         => $row['price'],
                    'compare_price' => $row['compare_price'] ?? '',
                    'payment'       => $payment,
                    'stock_status'  => $row['stock_status'],
                    'status'        => (int) $row['status'] === 1 ? 'Active' : 'Inactive',
                ];
            }

            return $this->csvDownload('products.csv', [
                'id'            => 'ID',
                'name'          => 'Name',
                'sku'           => 'SKU',
                'category_name' => 'Category',
                'vendor_name'   => 'Vendor',
                'price'         => 'Price',
                'compare_price' => 'Compare Price',
                'payment'       => 'Payment',
                'stock_status'  => 'Stock',
                'status'        => 'Status',
            ], $csvRows);
        }

        return $this->jsonSuccess('Products loaded.', $this->paginatedData($rows, $total, $query));
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('products.view')) {
            return $denied;
        }

        $model = model(ProductModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Product not found.', null, 404);
        }

        $row['plans'] = $model->plansForProduct((int) $id);
        $row['images_list'] = $row['images'] ? json_decode($row['images'], true) : [];
        if (! empty($row['vendor_id'])) {
            $vendor = model(VendorModel::class)->find($row['vendor_id']);
            $row['vendor_name'] = $vendor ? model(VendorModel::class)->displayName($vendor) : null;
        }

        return $this->jsonSuccess('Product loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('products.create')) {
            return $denied;
        }

        $payload = $this->validatedPayload();
        if (isset($payload['error'])) {
            return $this->jsonError($payload['error']);
        }

        $model = model(ProductModel::class);
        $id = $model->insert($payload['data']);
        $model->syncProductPlans((int) $id, $payload['plans']);

        return $this->jsonSuccess('Product created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('products.update')) {
            return $denied;
        }

        $model = model(ProductModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Product not found.', null, 404);
        }

        $payload = $this->validatedPayload((int) $id, $row);
        if (isset($payload['error'])) {
            return $this->jsonError($payload['error']);
        }

        if (! $model->update($id, $payload['data'])) {
            $errors = $model->errors();
            $msg = $errors ? implode(' ', $errors) : 'Failed to update product.';

            return $this->jsonError($msg);
        }
        $model->syncProductPlans((int) $id, $payload['plans']);

        return $this->jsonSuccess('Product updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('products.delete')) {
            return $denied;
        }

        $model = model(ProductModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Product not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('Product deleted.');
    }

    private function validatedPayload(?int $id = null, ?array $existing = null): array
    {
        $name = trim((string) $this->request->getVar('name'));
        if ($name === '') {
            return ['error' => 'Product name is required.'];
        }

        $images = $existing && ! empty($existing['images']) ? json_decode($existing['images'], true) : [];
        if (! is_array($images)) {
            $images = [];
        }

        $files = $this->request->getFileMultiple('images');
        if ($files) {
            foreach ($files as $file) {
                if ($file && $file->isValid() && ! $file->hasMoved()) {
                    $target = FCPATH . 'uploads/products';
                    if (! is_dir($target)) {
                        mkdir($target, 0755, true);
                    }
                    $filename = $file->getRandomName();
                    $file->move($target, $filename);
                    $images[] = 'uploads/products/' . $filename;
                }
            }
        }

        $cashAvailable = (int) $this->request->getPost('cash_available') === 1 ? 1 : 0;
        $installmentAvailable = (int) $this->request->getPost('installment_available') === 1 ? 1 : 0;
        if ($cashAvailable === 0 && $installmentAvailable === 0) {
            return ['error' => 'Enable at least cash purchase or installment for this product.'];
        }

        $priceRaw = trim((string) $this->request->getPost('price'));
        if ($priceRaw === '' || ! is_numeric($priceRaw)) {
            return ['error' => 'Price is required.'];
        }
        $price = (float) $priceRaw;
        if ($price < 0) {
            return ['error' => 'Price cannot be negative.'];
        }

        $comparePriceRaw = trim((string) $this->request->getPost('compare_price'));
        $comparePrice = $comparePriceRaw !== '' ? (float) $comparePriceRaw : null;
        if ($comparePrice !== null && $comparePrice < 0) {
            return ['error' => 'Compare price cannot be negative.'];
        }
        if ($comparePrice !== null && $comparePrice <= 0) {
            $comparePrice = null;
        }

        $plansRaw = $this->request->getPost('plans');
        $plans = [];
        if (is_array($plansRaw)) {
            foreach ($plansRaw as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $planName = trim((string) ($row['name'] ?? ''));
                $months = (int) ($row['months'] ?? 0);
                $down = (float) ($row['down_payment'] ?? 0);
                $monthly = (float) ($row['monthly_installment'] ?? 0);
                if ($planName === '' && $months <= 0 && $down <= 0 && $monthly <= 0) {
                    continue;
                }
                if ($down < 0 || $monthly < 0) {
                    return ['error' => 'Plan amounts cannot be negative.'];
                }
                if ($months < 1) {
                    return ['error' => 'Plan months must be at least 1.'];
                }
                $plans[] = [
                    'id'                  => ! empty($row['id']) ? (int) $row['id'] : null,
                    'name'                => $planName !== '' ? $planName : ($months . ' Month Plan'),
                    'down_payment'        => $down,
                    'monthly_installment' => $monthly,
                    'months'              => $months,
                ];
            }
        }

        if (! $installmentAvailable) {
            $plans = [];
        } elseif ($plans === []) {
            return ['error' => 'Add at least one installment plan, or set Installment to Not Available.'];
        }

        $vendorIdRaw = trim((string) $this->request->getPost('vendor_id'));
        $vendorId = $vendorIdRaw !== '' ? (int) $vendorIdRaw : null;
        if ($vendorId) {
            $vendor = model(VendorModel::class)->where('status', 'approved')->find($vendorId);
            if (! $vendor) {
                return ['error' => 'Selected vendor is not available.'];
            }
        } else {
            $vendorId = null;
        }

        return [
            'data' => [
                'category_id'           => $this->request->getPost('category_id') ?: null,
                'vendor_id'             => $vendorId,
                'name'                  => $name,
                'slug'                  => $this->makeSlug($name, 'products', $id),
                'sku'                   => $this->request->getPost('sku'),
                'price'                 => $price,
                'compare_price'         => $comparePrice,
                'images'                => json_encode(array_values($images)),
                'description'           => $this->request->getPost('description'),
                'stock_status'          => $this->request->getPost('stock_status') === 'out_of_stock' ? 'out_of_stock' : 'in_stock',
                'cash_available'        => $cashAvailable,
                'installment_available' => $installmentAvailable,
                'status'                => (int) $this->request->getPost('status') === 1 ? 1 : 0,
                'meta_title'            => $this->request->getPost('meta_title'),
                'meta_description'      => $this->request->getPost('meta_description'),
            ],
            'plans' => $plans,
        ];
    }
}
