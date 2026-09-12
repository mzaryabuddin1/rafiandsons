<?php

namespace App\Controllers\Admin;

use App\Models\BannerModel;

class BannersController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('banners/index', [
            'pageTitle'  => 'Banners',
            'activeMenu' => 'banners',
            'positions'  => BannerModel::positions(),
            'canCreate'  => $this->auth->can('banners.create'),
            'canUpdate'  => $this->auth->can('banners.update'),
            'canDelete'  => $this->auth->can('banners.delete'),
        ]);
    }

    public function create()
    {
        if ($denied = $this->requirePagePermission('banners.create', 'admin/banners')) {
            return $denied;
        }

        return $this->adminView('banners/form', [
            'pageTitle'  => 'Add Banner',
            'activeMenu' => 'banners',
            'isEdit'     => false,
            'recordId'   => null,
            'positions'  => BannerModel::positions(),
            'imageSizes' => BannerModel::recommendedImageSizes(),
            'categories' => model(\App\Models\CategoryModel::class)->parentsOnly(false),
        ]);
    }

    public function edit($id)
    {
        if ($denied = $this->requirePagePermission('banners.update', 'admin/banners')) {
            return $denied;
        }

        return $this->adminView('banners/form', [
            'pageTitle'  => 'Edit Banner',
            'activeMenu' => 'banners',
            'isEdit'     => true,
            'recordId'   => (int) $id,
            'positions'  => BannerModel::positions(),
            'imageSizes' => BannerModel::recommendedImageSizes(),
            'categories' => model(\App\Models\CategoryModel::class)->parentsOnly(false),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('banners.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $position = trim((string) $this->request->getGet('position'));
        $model    = model(BannerModel::class);

        if ($query['search'] !== '') {
            $model->groupStart()
                ->like('title', $query['search'])
                ->orLike('subtitle', $query['search'])
                ->orLike('link', $query['search'])
                ->groupEnd();
        }
        if ($position !== '' && array_key_exists($position, BannerModel::positions())) {
            $model->where('position', $position);
        }

        $model->orderBy('position', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC');
        [$items, $total] = $this->paginateModel($model, $query);

        $labels = BannerModel::positions();
        foreach ($items as &$item) {
            $item['position_label'] = $labels[$item['position']] ?? $item['position'];
        }
        unset($item);

        if ($query['export']) {
            $csvRows = [];
            foreach ($items as $item) {
                $csvRows[] = [
                    'id'             => $item['id'],
                    'position_label' => $item['position_label'],
                    'title'          => $item['title'] ?? '',
                    'subtitle'       => $item['subtitle'] ?? '',
                    'button_text'    => $item['button_text'] ?? '',
                    'link'           => $item['link'] ?? '',
                    'sort_order'     => $item['sort_order'] ?? 0,
                    'status'         => (int) ($item['status'] ?? 0) === 1 ? 'Active' : 'Inactive',
                ];
            }

            return $this->csvDownload('banners.csv', [
                'id'             => 'ID',
                'position_label' => 'Position',
                'title'          => 'Title',
                'subtitle'       => 'Subtitle',
                'button_text'    => 'Button',
                'link'           => 'Link',
                'sort_order'     => 'Sort',
                'status'         => 'Status',
            ], $csvRows);
        }

        return $this->jsonSuccess('Banners loaded.', $this->paginatedData($items, $total, $query));
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('banners.view')) {
            return $denied;
        }

        $row = model(BannerModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Banner not found.', null, 404);
        }

        return $this->jsonSuccess('Banner loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('banners.create')) {
            return $denied;
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $id = model(BannerModel::class)->insert($data);

        return $this->jsonSuccess('Banner created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('banners.update')) {
            return $denied;
        }

        $model = model(BannerModel::class);
        $row = $model->find($id);
        if (! $row) {
            return $this->jsonError('Banner not found.', null, 404);
        }

        $data = $this->payload($row);
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $model->update($id, $data);

        return $this->jsonSuccess('Banner updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('banners.delete')) {
            return $denied;
        }

        $model = model(BannerModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Banner not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('Banner deleted.');
    }

    private function payload(?array $existing = null): array
    {
        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return ['error' => 'Banner title is required.'];
        }

        $position = trim((string) $this->request->getPost('position'));
        if (! array_key_exists($position, BannerModel::positions())) {
            return ['error' => 'Please select a valid banner position.'];
        }

        $style = $this->request->getPost('style') === 'dark' ? 'dark' : 'light';
        $link  = trim((string) $this->request->getPost('link'));

        $data = [
            'position'    => $position,
            'title'       => $title,
            'subtitle'    => trim((string) $this->request->getPost('subtitle')) ?: null,
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'badge_text'  => trim((string) $this->request->getPost('badge_text')) ?: null,
            'button_text' => trim((string) $this->request->getPost('button_text')) ?: null,
            'bg_color'    => trim((string) $this->request->getPost('bg_color')) ?: null,
            'style'       => $style,
            'link'        => $link !== '' ? $link : null,
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'status'      => (int) $this->request->getPost('status') === 1 ? 1 : 0,
        ];

        $image = $this->storeUpload('image', 'banners');
        if ($image) {
            $data['image'] = $image;
        } elseif ($existing) {
            $data['image'] = $existing['image'] ?? null;
        }

        return $data;
    }
}
