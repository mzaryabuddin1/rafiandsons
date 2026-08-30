<?php

namespace App\Libraries;

use App\Models\VendorModel;

class VendorAuth
{
    public const SESSION_KEY = 'store_vendor';

    public function attempt(string $email, string $password): array
    {
        $email = trim($email);
        if ($email === '' || $password === '') {
            return ['success' => false, 'message' => 'Email and password are required.'];
        }

        $vendor = model(VendorModel::class)->where('email', $email)->first();
        if (! $vendor || empty($vendor['password']) || ! password_verify($password, $vendor['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        $status = (string) ($vendor['status'] ?? '');
        if ($status === 'pending') {
            return ['success' => false, 'message' => 'Your application is still under review.'];
        }
        if ($status === 'rejected') {
            return ['success' => false, 'message' => 'Your vendor application was not approved.'];
        }
        if ($status !== 'approved') {
            return ['success' => false, 'message' => 'Your vendor account is not active.'];
        }

        model(VendorModel::class)->update($vendor['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $this->setSession($vendor);

        return ['success' => true, 'message' => 'Welcome back!', 'redirect' => site_url('vendor/dashboard')];
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

        return isset($user['id']) ? (int) $user['id'] : null;
    }

    public function setSession(array $vendor): void
    {
        session()->set(self::SESSION_KEY, [
            'id'            => (int) $vendor['id'],
            'business_name' => $vendor['business_name'] ?? '',
            'contact_name'  => $vendor['contact_name'] ?? '',
            'email'         => $vendor['email'] ?? '',
            'phone'         => $vendor['phone'] ?? '',
            'status'        => $vendor['status'] ?? '',
        ]);
    }
}
