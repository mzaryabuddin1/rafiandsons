<?php

namespace App\Libraries;

use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class AdminAuth
{
    public const SESSION_KEY = 'admin_user';

    public function attempt(string $email, string $password): array
    {
        $userModel = model(UserModel::class);
        $user = $userModel->where('email', $email)->where('status', 1)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        $role = model(RoleModel::class)->find($user['role_id']);
        if (! $role || (int) $role['status'] !== 1) {
            return ['success' => false, 'message' => 'Your role is inactive.'];
        }

        $permissions = model(PermissionModel::class)->slugsForRole((int) $user['role_id']);

        $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        session()->set(self::SESSION_KEY, [
            'id'          => (int) $user['id'],
            'name'        => $user['name'],
            'email'       => $user['email'],
            'role_id'     => (int) $user['role_id'],
            'role_name'   => $role['name'],
            'role_slug'   => $role['slug'],
            'is_super'    => (int) $role['is_super'] === 1,
            'permissions' => $permissions,
        ]);

        return ['success' => true, 'message' => 'Login successful.', 'redirect' => site_url('admin/dashboard')];
    }

    public function logout(): void
    {
        session()->remove(self::SESSION_KEY);
    }

    public function user(): ?array
    {
        return session()->get(self::SESSION_KEY);
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function id(): ?int
    {
        $user = $this->user();

        return $user['id'] ?? null;
    }

    public function can(string $permission): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        if (! empty($user['is_super'])) {
            return true;
        }

        return in_array($permission, $user['permissions'] ?? [], true);
    }

    public function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }

        return false;
    }
}
