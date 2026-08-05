<?php

namespace App\Controllers\Admin;

use App\Models\RoleModel;
use App\Models\UserModel;

class UsersController extends BaseAdminController
{
    public function index()
    {
        return $this->adminView('users/index', [
            'pageTitle'  => 'Users',
            'activeMenu' => 'users',
            'canCreate'  => $this->auth->can('users.create'),
            'canUpdate'  => $this->auth->can('users.update'),
            'canDelete'  => $this->auth->can('users.delete'),
            'roles'      => model(RoleModel::class)->where('status', 1)->findAll(),
        ]);
    }

    public function list()
    {
        if ($denied = $this->requirePermission('users.view')) {
            return $denied;
        }

        $search = trim((string) $this->request->getGet('search'));
        $builder = db_connect()->table('users u')
            ->select('u.id, u.name, u.email, u.status, u.role_id, u.last_login_at, u.created_at, r.name as role_name')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('u.deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()->like('u.name', $search)->orLike('u.email', $search)->groupEnd();
        }

        return $this->jsonSuccess('Users loaded.', [
            'items' => $builder->orderBy('u.id', 'DESC')->get()->getResultArray(),
        ]);
    }

    public function show($id)
    {
        if ($denied = $this->requirePermission('users.view')) {
            return $denied;
        }

        $row = model(UserModel::class)->select('id, role_id, name, email, status, last_login_at, created_at')->find($id);
        if (! $row) {
            return $this->jsonError('User not found.', null, 404);
        }

        return $this->jsonSuccess('User loaded.', $row);
    }

    public function store()
    {
        if ($denied = $this->requirePermission('users.create')) {
            return $denied;
        }

        $data = $this->payload();
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $id = model(UserModel::class)->insert($data);

        return $this->jsonSuccess('User created.', ['id' => $id]);
    }

    public function update($id)
    {
        if ($denied = $this->requirePermission('users.update')) {
            return $denied;
        }

        $model = model(UserModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('User not found.', null, 404);
        }

        $data = $this->payload((int) $id);
        if (isset($data['error'])) {
            return $this->jsonError($data['error']);
        }

        $model->update($id, $data);

        return $this->jsonSuccess('User updated.');
    }

    public function delete($id)
    {
        if ($denied = $this->requirePermission('users.delete')) {
            return $denied;
        }

        if ((int) $id === (int) $this->auth->id()) {
            return $this->jsonError('You cannot delete your own account.');
        }

        $model = model(UserModel::class);
        if (! $model->find($id)) {
            return $this->jsonError('User not found.', null, 404);
        }

        $model->delete($id);

        return $this->jsonSuccess('User deleted.');
    }

    private function payload(?int $id = null): array
    {
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $roleId = (int) $this->request->getPost('role_id');
        $password = (string) $this->request->getPost('password');

        if ($name === '' || $email === '' || ! $roleId) {
            return ['error' => 'Name, email, and role are required.'];
        }

        $exists = model(UserModel::class)->where('email', $email);
        if ($id) {
            $exists->where('id !=', $id);
        }
        if ($exists->first()) {
            return ['error' => 'Email already exists.'];
        }

        if (! $id && $password === '') {
            return ['error' => 'Password is required for new users.'];
        }

        $data = [
            'name'    => $name,
            'email'   => $email,
            'role_id' => $roleId,
            'status'  => (int) $this->request->getPost('status') === 1 ? 1 : 0,
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $data;
    }
}
