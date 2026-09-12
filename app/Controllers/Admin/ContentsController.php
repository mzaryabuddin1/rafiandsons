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

    public function create()
    {
        if ($denied = $this->requirePagePermission('contents.create', 'contents')) {
            return $denied;
        }

        return $this->adminView('contents/form', [
            'pageTitle'  => 'Add Content',
            'activeMenu' => 'contents',
            'isEdit'     => false,
            'recordId'   => null,
        ]);
    }

    public function edit($id)
    {
        if ($denied = $this->requirePagePermission('contents.update', 'contents')) {
            return $denied;
        }

        return $this->adminView('contents/form', [
            'pageTitle'  => 'Edit Content',
            'activeMenu' => 'contents',
            'isEdit'     => true,
            'recordId'   => (int) $id,
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('contents.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $model = $this->scopeArchivedModel(model(ContentModel::class));
        if ($query['search'] !== '') {
            $model->groupStart()->like('title', $query['search'])->orLike('slug', $query['search'])->groupEnd();
        }
        $model->orderBy('id', 'ASC');
        [$items, $total] = $this->paginateModel($model, $query);

        if ($query['export']) {
            $csvRows = [];
            foreach ($items as $item) {
                $csvRows[] = [
                    'id'     => $item['id'],
                    'title'  => $item['title'],
                    'slug'   => $item['slug'],
                    'status' => (int) ($item['status'] ?? 0) === 1 ? 'Active' : 'Inactive',
                ];
            }

            return $this->csvDownload('contents.csv', [
                'id'     => 'ID',
                'title'  => 'Title',
                'slug'   => 'Slug',
                'status' => 'Status',
            ], $csvRows);
        }

        return $this->jsonSuccess('Contents loaded.', $this->paginatedData($items, $total, $query));
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

        return $this->jsonSuccess('Content archived.');
    }

    public function restore($id)
    {
        if ($denied = $this->requirePermission('contents.delete')) {
            return $denied;
        }

        $result = $this->restoreSoftDeleted(model(ContentModel::class), $id, 'Content not found.');
        if ($result !== true) {
            return $result;
        }

        return $this->jsonSuccess('Content restored.');
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
            'body'   => $this->normalizeBodyHtml((string) $this->request->getPost('body')),
            'status' => (int) $this->request->getPost('status') === 1 ? 1 : 0,
        ];
    }

    /**
     * Keep editor HTML but strip pasted oversized font styles.
     */
    private function normalizeBodyHtml(string $html): string
    {
        $html = preg_replace('/\s*font-size\s*:\s*[^;"\']+;?/i', '', $html) ?? $html;
        $html = preg_replace('/\s*line-height\s*:\s*[^;"\']+;?/i', '', $html) ?? $html;
        $html = preg_replace('/\sstyle=("|\')\s*\1/i', '', $html) ?? $html;
        $html = preg_replace('/<\/?font\b[^>]*>/i', '', $html) ?? $html;

        return $html;
    }
}
