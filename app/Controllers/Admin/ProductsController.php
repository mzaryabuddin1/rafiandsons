<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\InstallmentPlanModel;
use App\Models\ProductModel;

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
            'plans'      => model(InstallmentPlanModel::class)->globalActive(),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('products.view')) {
            return $denied;
        }

        $search = trim((string) $this->request->getGet('search'));
        $builder = db_connect()->table('products p')
            ->select('p.*, c.name as category_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->where('p.deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()
                ->like('p.name', $search)
                ->orLike('p.sku', $search)
                ->orLike('p.slug', $search)
                ->groupEnd();
        }

        $rows = $builder->orderBy('p.id', 'DESC')->get()->getResultArray();

        return $this->jsonSuccess('Products loaded.', ['items' => $rows]);
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

        $row['plan_ids'] = $model->planIds((int) $id);
        $row['plans'] = $model->plansForProduct((int) $id);
        $row['images_list'] = $row['images'] ? json_decode($row['images'], true) : [];

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

        $model->update($id, $payload['data']);
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
        $name = trim((string) $this->request->getPost('name'));
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

        $planIds = $this->request->getPost('plan_ids');
        if (! is_array($planIds)) {
            $planIds = $planIds ? [$planIds] : [];
        }

        // Preferred: full plan rows from product form
        $plansRaw = $this->request->getPost('plans');
        $plans = [];
        if (is_array($plansRaw)) {
            foreach ($plansRaw as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $name = trim((string) ($row['name'] ?? ''));
                $months = (int) ($row['months'] ?? 0);
                $down = (float) ($row['down_payment'] ?? 0);
                $monthly = (float) ($row['monthly_installment'] ?? 0);
                if ($name === '' && $months <= 0 && $down <= 0 && $monthly <= 0) {
                    continue;
                }
                $plans[] = [
                    'id'                  => ! empty($row['id']) ? (int) $row['id'] : null,
                    'name'                => $name !== '' ? $name : ($months . ' Month Plan'),
                    'down_payment'        => $down,
                    'monthly_installment' => $monthly,
                    'months'              => max(1, $months ?: 12),
                ];
            }
        } elseif ($planIds) {
            // Backward compatible: selected global template IDs → copy as product plans
            foreach ($planIds as $pid) {
                $template = model(InstallmentPlanModel::class)->find((int) $pid);
                if (! $template) {
                    continue;
                }
                $plans[] = [
                    'id'                  => null,
                    'name'                => $template['name'],
                    'down_payment'        => (float) $template['down_payment'],
                    'monthly_installment' => (float) $template['monthly_installment'],
                    'months'              => (int) $template['months'],
                ];
            }
        }

        return [
            'data' => [
                'category_id'           => $this->request->getPost('category_id') ?: null,
                'name'                  => $name,
                'slug'                  => $this->makeSlug($name, 'products', $id),
                'sku'                   => $this->request->getPost('sku'),
                'price'                 => (float) $this->request->getPost('price'),
                'images'                => json_encode(array_values($images)),
                'description'           => $this->request->getPost('description'),
                'stock_status'          => $this->request->getPost('stock_status') === 'out_of_stock' ? 'out_of_stock' : 'in_stock',
                'installment_available' => (int) $this->request->getPost('installment_available') === 1 ? 1 : 0,
                'status'                => (int) $this->request->getPost('status') === 1 ? 1 : 0,
                'meta_title'            => $this->request->getPost('meta_title'),
                'meta_description'      => $this->request->getPost('meta_description'),
            ],
            'plans' => $plans,
        ];
    }
}
