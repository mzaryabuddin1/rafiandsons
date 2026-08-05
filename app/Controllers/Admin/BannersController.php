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
            'imageSizes' => BannerModel::recommendedImageSizes(),
            'canCreate'  => $this->auth->can('banners.create'),
            'canUpdate'  => $this->auth->can('banners.update'),
            'canDelete'  => $this->auth->can('banners.delete'),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('banners.view')) {
            return $denied;
        }

        $search   = trim((string) $this->request->getGet('search'));
        $position = trim((string) $this->request->getGet('position'));
        $model    = model(BannerModel::class);

        if ($search !== '') {
            $model->groupStart()
                ->like('title', $search)
                ->orLike('subtitle', $search)
                ->orLike('link', $search)
                ->groupEnd();
        }
        if ($position !== '' && array_key_exists($position, BannerModel::positions())) {
            $model->where('position', $position);
        }

        $items = $model->orderBy('position', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $labels = BannerModel::positions();
        foreach ($items as &$item) {
            $item['position_label'] = $labels[$item['position']] ?? $item['position'];
        }

        return $this->jsonSuccess('Banners loaded.', ['items' => $items]);
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
