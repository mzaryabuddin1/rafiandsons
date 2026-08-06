<?php

namespace App\Controllers;

use App\Libraries\CustomerOtpService;
use App\Libraries\StoreAuth;
use App\Models\CustomerModel;
use App\Models\OrderModel;

class AccountController extends BaseStoreController
{
    protected StoreAuth $auth;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->auth = new StoreAuth();
    }

    public function login()
    {
        $redirect = $this->safeRedirect($this->request->getGet('redirect'));

        return $this->storeView('account/login', [
            'pageTitle'  => 'Sign In',
            'activeMenu' => '',
            'redirect'   => $redirect,
        ]);
    }

    public function attemptLogin()
    {
        $login = trim((string) $this->request->getPost('login'));
        $password = (string) $this->request->getPost('password');
        $redirect = $this->safeRedirect($this->request->getPost('redirect'));

        $result = $this->auth->attempt($login, $password);
        if (! $result['success']) {
            return $this->jsonError($result['message']);
        }

        $result['redirect'] = $redirect ?: ($result['redirect'] ?? site_url('account/profile'));

        return $this->jsonSuccess($result['message'], ['redirect' => $result['redirect']]);
    }

    public function register()
    {
        $redirect = $this->safeRedirect($this->request->getGet('redirect'));

        return $this->storeView('account/register', [
            'pageTitle'  => 'Create Account',
            'activeMenu' => '',
            'redirect'   => $redirect,
        ]);
    }

    public function attemptRegister()
    {
        return $this->jsonError('Please verify your email with OTP first.', null, 400);
    }

    public function sendRegisterOtp()
    {
        $result = (new CustomerOtpService())->sendRegisterOtp([
            'name'              => $this->request->getPost('name'),
            'phone'             => $this->request->getPost('phone'),
            'email'             => $this->request->getPost('email'),
            'password'          => $this->request->getPost('password'),
            'password_confirm'  => $this->request->getPost('password_confirm'),
        ]);

        return $result['success']
            ? $this->jsonSuccess($result['message'], ['email' => $result['email'] ?? ''])
            : $this->jsonError($result['message']);
    }

    public function verifyRegisterOtp()
    {
        $email = trim((string) $this->request->getPost('email'));
        $otp = trim((string) $this->request->getPost('otp'));
        $redirect = $this->safeRedirect($this->request->getPost('redirect'));

        $result = (new CustomerOtpService())->verifyRegisterOtp($email, $otp);
        if (! $result['success']) {
            return $this->jsonError($result['message']);
        }

        $result['redirect'] = $redirect ?: ($result['redirect'] ?? site_url('account/profile'));

        return $this->jsonSuccess($result['message'], ['redirect' => $result['redirect']]);
    }

    public function forgotPassword()
    {
        return $this->storeView('account/forgot_password', [
            'pageTitle'  => 'Forgot Password',
            'activeMenu' => '',
        ]);
    }

    public function sendForgotOtp()
    {
        $result = (new CustomerOtpService())->sendResetOtp((string) $this->request->getPost('login'));

        return $result['success']
            ? $this->jsonSuccess($result['message'], ['email' => $result['email'] ?? ''])
            : $this->jsonError($result['message']);
    }

    public function resetPasswordWithOtp()
    {
        $result = (new CustomerOtpService())->resetPasswordWithOtp(
            (string) $this->request->getPost('email'),
            (string) $this->request->getPost('otp'),
            (string) $this->request->getPost('password'),
            (string) $this->request->getPost('password_confirm')
        );

        if (! $result['success']) {
            return $this->jsonError($result['message']);
        }

        return $this->jsonSuccess($result['message'], ['redirect' => $result['redirect'] ?? site_url('account/login')]);
    }

    public function logout()
    {
        $this->auth->logout();

        return $this->jsonSuccess('Signed out successfully.', ['redirect' => site_url('home')]);
    }

    public function profile()
    {
        $customer = model(CustomerModel::class)->find($this->auth->id());

        return $this->storeView('account/profile', [
            'pageTitle'  => 'My Profile',
            'activeMenu' => 'account',
            'customer'   => $customer,
        ]);
    }

    public function updateProfile()
    {
        $customerId = $this->auth->id();
        if (! $customerId) {
            return $this->jsonError('Please sign in again.', null, 401);
        }

        $model = model(CustomerModel::class);
        $customer = $model->find($customerId);
        if (! $customer) {
            return $this->jsonError('Account not found.', null, 404);
        }

        $name = trim((string) $this->request->getPost('name'));
        $phone = trim((string) $this->request->getPost('phone'));
        $email = trim((string) $this->request->getPost('email'));
        $cnic = trim((string) $this->request->getPost('cnic'));
        $address = trim((string) $this->request->getPost('address'));
        $city = trim((string) $this->request->getPost('city'));
        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');
        $newPasswordConfirm = (string) $this->request->getPost('new_password_confirm');

        if ($name === '' || $phone === '') {
            return $this->jsonError('Name and phone are required.');
        }

        $phoneTaken = $model->where('phone', $phone)->where('id !=', $customerId)->first();
        if ($phoneTaken && ! empty($phoneTaken['password'])) {
            return $this->jsonError('This phone number is already used by another account.');
        }

        if ($email !== '') {
            $emailTaken = $model->where('email', $email)->where('id !=', $customerId)->first();
            if ($emailTaken && ! empty($emailTaken['password'])) {
                return $this->jsonError('This email is already used by another account.');
            }
        }

        $payload = [
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email !== '' ? $email : null,
            'cnic'    => $cnic !== '' ? $cnic : null,
            'address' => $address !== '' ? $address : null,
            'city'    => $city !== '' ? $city : null,
        ];

        $image = $this->storeUpload('profile_image', 'customers');
        if ($image) {
            $this->deleteUpload($customer['profile_image'] ?? null);
            $payload['profile_image'] = $image;
        }

        if ($newPassword !== '') {
            if (! password_verify($currentPassword, $customer['password'])) {
                return $this->jsonError('Current password is incorrect.');
            }
            if (strlen($newPassword) < 6) {
                return $this->jsonError('New password must be at least 6 characters.');
            }
            if ($newPassword !== $newPasswordConfirm) {
                return $this->jsonError('New passwords do not match.');
            }
            $payload['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $model->update($customerId, $payload);
        $this->auth->refreshFromDb();

        return $this->jsonSuccess('Profile updated successfully.', [
            'profile_image' => $this->auth->profileImageUrl(),
        ]);
    }

    public function orders()
    {
        $orders = model(OrderModel::class)
            ->where('customer_id', $this->auth->id())
            ->orderBy('id', 'DESC')
            ->findAll(50);

        return $this->storeView('account/orders', [
            'pageTitle'  => 'My Orders',
            'activeMenu' => 'account',
            'orders'     => $orders,
        ]);
    }

    protected function safeRedirect(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);
        if ($url === '' || str_starts_with($url, '//') || preg_match('#^https?://#i', $url)) {
            return null;
        }

        return $url;
    }

    protected function storeUpload(string $field, string $folder): ?string
    {
        $file = $this->request->getFile($field);
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (! in_array($file->getMimeType(), $allowed, true)) {
            return null;
        }

        $target = FCPATH . 'uploads/' . $folder;
        if (! is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $name = $file->getRandomName();
        $file->move($target, $name);

        return 'uploads/' . $folder . '/' . $name;
    }

    protected function deleteUpload(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'uploads/')) {
            return;
        }

        $full = FCPATH . $path;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
