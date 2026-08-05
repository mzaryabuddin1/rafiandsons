<?php

namespace App\Controllers\Admin;

use App\Models\ContentModel;

class ContentsController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('contents/index', [
            'pageTitle'  => 'Website Contents',
            'activeMenu' => 'contents',
            'canCreate'  => $this->auth->can('contents.create'),
            'canUpdate'  => $this->auth->can('contents.update'),
            'canDelete'  => $this->auth->can('contents.delete'),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('contents.view')) {
            return $denied;
        }

        $search = trim((string) $this->request->getGet('search'));
        $model = model(ContentModel::class);
        if ($search !== '') {
            $model->groupStart()->like('title', $search)->orLike('slug', $search)->groupEnd();
        }

        return $this->jsonSuccess('Contents loaded.', ['items' => $model->orderBy('id', 'ASC')->findAll()]);
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('contents.view')) {
            return $denied;
        }

        $row = model(ContentModel::class)->find($id);
        if (! $row) {
            return $this->jsonError('Content not found.', null, 404);
        }

        return $this->jsonSuccess('Content loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('contents.create')) {
            return $denied;
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $id = model(ContentModel::class)->insert($data);

        return $this->jsonSuccess('Content created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('contents.update')) {
            return $denied;
        }

        $model = model(ContentModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Content not found.', null, 404);
        }

        $data = $this->payload((int) $id);
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $model->update($id, $data);

        return $this->jsonSuccess('Content updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('contents.delete')) {
            return $denied;
        }

        $model = model(ContentModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('Content not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('Content deleted.');
    }

    private function payload(?int $id = null): array
    {
        $title = trim((string) $this->request->getPost('title'));
        $slugInput = trim((string) $this->request->getPost('slug'));
        if ($title === '') {
            return ['error' => 'Title is required.'];
        }

        $slug = $slugInput !== '' ? url_title($slugInput, '-', true) : $this->makeSlug($title, 'contents', $id);

        return [
            'title'  => $title,
            'slug'   => $slug,
            'body'   => $this->request->getPost('body'),
            'status' => (int) $this->request->getPost('status') === 1 ? 1 : 0,
        ];
    }
}
