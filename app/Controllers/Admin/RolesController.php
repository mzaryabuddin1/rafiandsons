<?php

namespace App\Controllers\Admin;

use App\Models\PermissionModel;
use App\Models\RoleModel;

class RolesController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('roles/index', [
            'pageTitle'  => 'Roles',
            'activeMenu' => 'roles',
            'canCreate'  => $this->auth->can('roles.create'),
            'canUpdate'  => $this->auth->can('roles.update'),
            'canDelete'  => $this->auth->can('roles.delete'),
        ]);
    }

    public function create()
    {
        if ($denied = $this->requirePagePermission('roles.create', 'admin/roles')) {
            return $denied;
        }

        return $this->adminView('roles/form', [
            'pageTitle'  => 'Add Role',
            'activeMenu' => 'roles',
            'isEdit'     => false,
            'recordId'   => null,
        ]);
    }

    public function edit($id)
    {
        if ($denied = $this->requirePagePermission('roles.update', 'admin/roles')) {
            return $denied;
        }

        $role = model(RoleModel::class)->find($id);
        if (! $role || (int) ($role['is_super'] ?? 0) === 1) {
            return redirect()->to(site_url('admin/roles'));
        }

        return $this->adminView('roles/form', [
            'pageTitle'  => 'Edit Role',
            'activeMenu' => 'roles',
            'isEdit'     => true,
            'recordId'   => (int) $id,
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('roles.view')) {
            return $denied;
        }

        $query = $this->listQuery();
        $model = model(RoleModel::class);
        if ($query['search'] !== '') {
            $model->groupStart()
                ->like('name', $query['search'])
                ->orLike('slug', $query['search'])
                ->groupEnd();
        }
        $model->orderBy('id', 'ASC');
        [$items, $total] = $this->paginateModel($model, $query);

        if ($query['export']) {
            $csvRows = [];
            foreach ($items as $item) {
                $csvRows[] = [
                    'id'       => $item['id'],
                    'name'     => $item['name'],
                    'slug'     => $item['slug'],
                    'is_super' => (int) ($item['is_super'] ?? 0) === 1 ? 'Yes' : 'No',
                    'status'   => (int) ($item['status'] ?? 0) === 1 ? 'Active' : 'Inactive',
                ];
            }

            return $this->csvDownload('roles.csv', [
                'id'       => 'ID',
                'name'     => 'Name',
                'slug'     => 'Slug',
                'is_super' => 'Super',
                'status'   => 'Status',
            ], $csvRows);
        }

        return $this->jsonSuccess('Roles loaded.', $this->paginatedData($items, $total, $query));
    }

    public function permissions()
    {
        if ($denied = $this->requirePermission('roles.view')) {
            return $denied;
        }

        return $this->jsonSuccess('Permissions loaded.', [
            'grouped' => model(PermissionModel::class)->grouped(),
        ]);
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('roles.view')) {
            return $denied;
        }

        $role = model(RoleModel::class)->find($id);
        if (! $role) {
            return $this->jsonError('Role not found.', null, 404);
        }

        $permissionIds = db_connect()->table('role_permissions')
            ->select('permission_id')
            ->where('role_id', $id)
            ->get()
            ->getResultArray();

        $role['permission_ids'] = array_map('intval', array_column($permissionIds, 'permission_id'));

        return $this->jsonSuccess('Role loaded.', $role);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('roles.create')) {
            return $denied;
        }

        $payload = $this->payload();
        if (isset($payload['error'])) {
            return $this->jsonError($payload['error']);
        }

        $db = db_connect();
        $db->transStart();
        $id = model(RoleModel::class)->insert($payload['role']);
        $this->syncPermissions((int) $id, $payload['permission_ids']);
        $db->transComplete();

        return $this->jsonSuccess('Role created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('roles.update')) {
            return $denied;
        }

        $model = model(RoleModel::class);
        $role = $model->find($id);
        if (! $role) {
            return $this->jsonError('Role not found.', null, 404);
        }

        if ((int) $role['is_super'] === 1) {
            return $this->jsonError('Super Admin role cannot be modified.');
        }

        $payload = $this->payload((int) $id);
        if (isset($payload['error'])) {
            return $this->jsonError($payload['error']);
        }

        $db = db_connect();
        $db->transStart();
        $model->update($id, $payload['role']);
        $this->syncPermissions((int) $id, $payload['permission_ids']);
        $db->transComplete();

        return $this->jsonSuccess('Role updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('roles.delete')) {
            return $denied;
        }

        $model = model(RoleModel::class);
        $role = $model->find($id);
        if (! $role) {
            return $this->jsonError('Role not found.', null, 404);
        }

        if ((int) $role['is_super'] === 1) {
            return $this->jsonError('Super Admin role cannot be deleted.');
        }

        $usersCount = db_connect()->table('users')->where('role_id', $id)->where('deleted_at', null)->countAllResults();
        if ($usersCount > 0) {
            return $this->jsonError('Role is assigned to users and cannot be deleted.');
        }

        $model->delete($id);

        return $this->jsonSuccess('Role deleted.');
    }

    private function payload(?int $id = null): array
    {
        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return ['error' => 'Role name is required.'];
        }

        $permissionIds = $this->request->getPost('permission_ids');
        if (! is_array($permissionIds)) {
            $permissionIds = $permissionIds ? [$permissionIds] : [];
        }

        return [
            'role' => [
                'name'     => $name,
                'slug'     => url_title($name, '-', true),
                'is_super' => 0,
                'status'   => (int) $this->request->getPost('status') === 1 ? 1 : 0,
            ],
            'permission_ids' => array_map('intval', $permissionIds),
        ];
    }

    private function syncPermissions(int $roleId, array $permissionIds): void
    {
        $db = db_connect();
        $db->table('role_permissions')->where('role_id', $roleId)->delete();
        foreach (array_unique($permissionIds) as $permissionId) {
            if ($permissionId > 0) {
                $db->table('role_permissions')->insert([
                    'role_id'       => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }
}
