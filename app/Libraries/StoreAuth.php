<?php

namespace App\Libraries;

use App\Models\CustomerModel;

class StoreAuth
{
    public const SESSION_KEY = 'store_customer';

    public function attempt(string $login, string $password): array
    {
        $login = trim($login);
        if ($login === '' || $password === '') {
            return ['success' => false, 'message' => 'Email/phone and password are required.'];
        }

        $model = model(CustomerModel::class);
        $customer = $model->where('status', 1)
            ->groupStart()
                ->where('email', $login)
                ->orWhere('phone', $login)
            ->groupEnd()
            ->first();

        if (! $customer || empty($customer['password']) || ! password_verify($password, $customer['password'])) {
            return ['success' => false, 'message' => 'Invalid email/phone or password.'];
        }

        $model->update($customer['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $this->setSession($customer);

        return ['success' => true, 'message' => 'Welcome back!', 'redirect' => site_url('account/profile')];
    }

    public function register(array $data): array
    {
        $model = model(CustomerModel::class);
        $phone = trim((string) ($data['phone'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));

        if ($phone === '') {
            return ['success' => false, 'message' => 'Phone number is required.'];
        }

        $existing = $model->where('phone', $phone)->first();
        if ($existing && ! empty($existing['password'])) {
            return ['success' => false, 'message' => 'An account with this phone already exists. Please sign in.'];
        }

        if ($email !== '') {
            $emailTaken = $model->where('email', $email)
                ->where('id !=', (int) ($existing['id'] ?? 0))
                ->first();
            if ($emailTaken && ! empty($emailTaken['password'])) {
                return ['success' => false, 'message' => 'An account with this email already exists. Please sign in.'];
            }
        }

        $payload = [
            'name'    => trim((string) ($data['name'] ?? '')),
            'phone'   => $phone,
            'email'   => $email !== '' ? $email : null,
            'password' => password_hash((string) ($data['password'] ?? ''), PASSWORD_DEFAULT),
            'status'  => 1,
        ];

        if ($existing) {
            $model->update($existing['id'], $payload);
            $customer = $model->find($existing['id']);
        } else {
            $id = $model->insert($payload);
            $customer = $model->find($id);
        }

        $this->setSession($customer);

        return ['success' => true, 'message' => 'Account created successfully.', 'redirect' => site_url('account/profile')];
    }

    /**
     * @param array{name:string,phone:string,email:string,password_hash:string} $payload
     */
    public function registerFromVerifiedPayload(array $payload): array
    {
        $model = model(CustomerModel::class);
        $phone = trim((string) ($payload['phone'] ?? ''));
        $email = trim((string) ($payload['email'] ?? ''));
        $existing = $model->where('phone', $phone)->first();

        $data = [
            'name'     => trim((string) ($payload['name'] ?? '')),
            'phone'    => $phone,
            'email'    => $email !== '' ? $email : null,
            'password' => (string) ($payload['password_hash'] ?? ''),
            'status'   => 1,
        ];

        if ($existing) {
            $model->update($existing['id'], $data);
            $customer = $model->find($existing['id']);
        } else {
            $id = $model->insert($data);
            $customer = $model->find($id);
        }

        $this->setSession($customer);

        return ['success' => true, 'message' => 'Account created successfully.', 'redirect' => site_url('account/profile')];
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

    public function refreshFromDb(): void
    {
        $id = $this->id();
        if (! $id) {
            return;
        }

        $customer = model(CustomerModel::class)->find($id);
        if ($customer && (int) ($customer['status'] ?? 0) === 1) {
            $this->setSession($customer);
        } else {
            $this->logout();
        }
    }

    public function setSession(array $customer): void
    {
        session()->set(self::SESSION_KEY, [
            'id'            => (int) $customer['id'],
            'name'          => $customer['name'] ?? '',
            'email'         => $customer['email'] ?? '',
            'phone'         => $customer['phone'] ?? '',
            'cnic'          => $customer['cnic'] ?? '',
            'address'       => $customer['address'] ?? '',
            'city'          => $customer['city'] ?? '',
            'profile_image' => $customer['profile_image'] ?? '',
        ]);
    }

    public function profileImageUrl(?array $user = null): string
    {
        $user = $user ?? $this->user();
        $image = trim((string) ($user['profile_image'] ?? ''));

        return $image !== '' ? base_url($image) : base_url('assets/store/default-avatar.svg');
    }
}
