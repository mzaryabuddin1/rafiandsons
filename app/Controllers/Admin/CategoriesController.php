<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;

class CategoriesController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('categories/index', [
            'pageTitle'  => 'Categories',
            'activeMenu' => 'categories',
            'canCreate'  => $this->auth->can('categories.create'),
            'canUpdate'  => $this->auth->can('categories.update'),
            'canDelete'  => $this->auth->can('categories.delete'),
        ]);
    }

    public function create()
    {
        if ($denied = $this->requirePagePermission('categories.create', 'categories')) {
            return $denied;
        }

        return $this->adminView('categories/form', [
            'pageTitle'  => 'Add Category',
            'activeMenu' => 'categories',
            'isEdit'     => false,
            'recordId'   => null,
            'parents'    => model(CategoryModel::class)->parentsOnly(),
        ]);
    }

    public function edit($id)
    {
        if ($denied = $this->requirePagePermission('categories.update', 'categories')) {
            return $denied;
        }

        return $this->adminView('categories/form', [
            'pageTitle'  => 'Edit Category',
            'activeMenu' => 'categories',
            'isEdit'     => true,
            'recordId'   => (int) $id,
            'parents'    => model(CategoryModel::class)->parentsOnly(),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('categories.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $db = db_connect();
        $builder = $db->table('categories c')
            ->select('c.*, p.name as parent_name')
            ->join('categories p', 'p.id = c.parent_id', 'left');
        $this->scopeArchivedBuilder($builder, 'c.deleted_at');

        if ($query['search'] !== '') {
            $builder->groupStart()
                ->like('c.name', $query['search'])
                ->orLike('c.slug', $query['search'])
                ->orLike('p.name', $query['search'])
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('c.parent_id', 'ASC')
            ->orderBy('c.sort_order', 'ASC')
            ->orderBy('c.name', 'ASC')
            ->get()
            ->getResultArray();

        // Parents first, then their children grouped
        $parents = [];
        $children = [];
        foreach ($rows as $row) {
            if (empty($row['parent_id'])) {
                $parents[] = $row;
            } else {
                $children[(int) $row['parent_id']][] = $row;
            }
        }
        $ordered = [];
        foreach ($parents as $parent) {
            $ordered[] = $parent;
            foreach ($children[(int) $parent['id']] ?? [] as $child) {
                $ordered[] = $child;
            }
        }
        // Orphaned subcategories (parent filtered out by search)
        foreach ($children as $pid => $kids) {
            $parentIds = array_column($parents, 'id');
            if (! in_array((string) $pid, $parentIds, true) && ! in_array($pid, array_map('intval', $parentIds), true)) {
                foreach ($kids as $child) {
                    $ordered[] = $child;
                }
            }
        }

        foreach ($ordered as &$row) {
            $row['type'] = empty($row['parent_id']) ? 'Category' : 'Subcategory';
            $row['display_name'] = empty($row['parent_id'])
                ? $row['name']
                : '— ' . $row['name'];
        }
        unset($row);

        [$pageItems, $total] = $this->paginateArray($ordered, $query);

        if ($query['export']) {
            $csvRows = [];
            foreach ($pageItems as $row) {
                $csvRows[] = [
                    'id'          => $row['id'],
                    'name'        => $row['name'],
                    'parent_name' => $row['parent_name'] ?? '',
                    'type'        => $row['type'],
                    'slug'        => $row['slug'],
                    'sort_order'  => $row['sort_order'] ?? 0,
                    'status'      => (int) ($row['status'] ?? 0) === 1 ? 'Active' : 'Inactive',
                ];
            }

            return $this->csvDownload('categories.csv', [
                'id'          => 'ID',
                'name'        => 'Name',
                'parent_name' => 'Parent',
                'type'        => 'Type',
                'slug'        => 'Slug',
                'sort_order'  => 'Sort',
                'status'      => 'Status',
            ], $csvRows);
        }

        return $this->jsonSuccess('Categories loaded.', $this->paginatedData($pageItems, $total, $query, [
            'parents' => model(CategoryModel::class)->parentsOnly(),
            'tree'    => model(CategoryModel::class)->tree(false),
        ]));
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('categories.view')) {
            return $denied;
        }

        $row = model(CategoryModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Category not found.', null, 404);
        }

        return $this->jsonSuccess('Category loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('categories.create')) {
            return $denied;
        }

        $payload = $this->validatedPayload();
        if (isset($payload['error'])) {
            return $this->jsonError($payload['error']);
        }

        $id = model(CategoryModel::class)->insert($payload['data']);

        return $this->jsonSuccess('Category created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('categories.update')) {
            return $denied;
        }

        $model = model(CategoryModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Category not found.', null, 404);
        }

        $payload = $this->validatedPayload((int) $id, $row);
        if (isset($payload['error'])) {
            return $this->jsonError($payload['error']);
        }

        $model->update($id, $payload['data']);

        return $this->jsonSuccess('Category updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('categories.delete')) {
            return $denied;
        }

        $model = model(CategoryModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Category not found.', null, 404);
        }

        // Soft-delete children when deleting a parent
        if (empty($row['parent_id'])) {
            $children = $model->where('parent_id', $id)->findAll();
            foreach ($children as $child) {
                $model->delete($child['id']);
            }
        }

        $model->delete($id);

        return $this->jsonSuccess('Category archived.');
    }

    public function restore($id)
    {
        if ($denied = $this->requirePermission('categories.delete')) {
            return $denied;
        }

        $result = $this->restoreSoftDeleted(model(CategoryModel::class), $id, 'Category not found.');
        if ($result !== true) {
            return $result;
        }

        return $this->jsonSuccess('Category restored.');
    }

    private function validatedPayload(?int $id = null, ?array $existing = null): array
    {
        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return ['error' => 'Category name is required.'];
        }

        $parentId = $this->request->getPost('parent_id');
        $parentId = ($parentId === '' || $parentId === null) ? null : (int) $parentId;

        if ($parentId) {
            if ($id && $parentId === $id) {
                return ['error' => 'A category cannot be its own parent.'];
            }

            $parent = model(CategoryModel::class)->find($parentId);
            if (! $parent) {
                return ['error' => 'Parent category not found.'];
            }
            if (! empty($parent['parent_id'])) {
                return ['error' => 'Only top-level categories can be parents (2 levels max).'];
            }

            // If this category already has children, it cannot become a subcategory
            if ($id) {
                $childCount = model(CategoryModel::class)->where('parent_id', $id)->countAllResults();
                if ($childCount > 0) {
                    return ['error' => 'This category has subcategories and cannot become a subcategory.'];
                }
            }
        }

        $data = [
            'parent_id'        => $parentId,
            'name'             => $name,
            'slug'             => $this->makeSlug($name, 'categories', $id),
            'icon'             => $this->normalizeIcon((string) $this->request->getPost('icon')),
            'description'      => $this->request->getPost('description'),
            'status'           => (int) $this->request->getPost('status') === 1 ? 1 : 0,
            'sort_order'       => (int) $this->request->getPost('sort_order'),
            'meta_title'       => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
        ];

        $image = $this->storeUpload('image', 'categories');
        if ($image) {
            $data['image'] = $image;
        } elseif ($existing && isset($existing['image'])) {
            // keep existing on update when no new file
        }

        return ['data' => $data];
    }

    private function normalizeIcon(string $icon): ?string
    {
        $icon = trim($icon);
        if ($icon === '') {
            return null;
        }

        $parts = preg_split('/\s+/', $icon) ?: [];
        foreach ($parts as $part) {
            if (str_starts_with($part, 'fa-')) {
                return $part;
            }
        }

        return null;
    }
}
