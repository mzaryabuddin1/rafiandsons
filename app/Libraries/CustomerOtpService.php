<?php

namespace App\Libraries;

use App\Models\CustomerModel;
use App\Models\CustomerOtpModel;

class CustomerOtpService
{
    public const PURPOSE_REGISTER = 'register';
    public const PURPOSE_RESET    = 'reset_password';

    private const OTP_TTL_MINUTES = 10;
    private const MAX_ATTEMPTS    = 5;
    private const RESEND_SECONDS  = 60;

    public function sendRegisterOtp(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));
        $email = $this->normalizeEmail((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $passwordConfirm = (string) ($data['password_confirm'] ?? '');

        if ($name === '') {
            return ['success' => false, 'message' => 'Full name is required.'];
        }
        if ($phone === '') {
            return ['success' => false, 'message' => 'Phone number is required.'];
        }
        if ($email === '') {
            return ['success' => false, 'message' => 'Email is required for OTP verification.'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
        }
        if ($password !== $passwordConfirm) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }

        $model = model(CustomerModel::class);
        $existing = $model->where('phone', $phone)->first();
        if ($existing && ! empty($existing['password'])) {
            return ['success' => false, 'message' => 'An account with this phone already exists. Please sign in.'];
        }

        $emailTaken = $model->where('email', $email)
            ->where('id !=', (int) ($existing['id'] ?? 0))
            ->first();
        if ($emailTaken && ! empty($emailTaken['password'])) {
            return ['success' => false, 'message' => 'An account with this email already exists. Please sign in.'];
        }

        $payload = [
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $sent = $this->issueOtp($email, self::PURPOSE_REGISTER, $payload, 'Verify your registration');
        if (! $sent['success']) {
            return $sent;
        }

        return [
            'success' => true,
            'message' => 'Verification code sent to ' . $this->maskEmail($email) . '.',
            'email'   => $email,
        ];
    }

    public function verifyRegisterOtp(string $email, string $otp): array
    {
        $email = $this->normalizeEmail($email);
        $record = $this->verifyOtpRecord($email, self::PURPOSE_REGISTER, $otp);
        if (! $record['success']) {
            return $record;
        }

        $payload = json_decode((string) ($record['data']['payload'] ?? ''), true);
        if (! is_array($payload) || empty($payload['password_hash'])) {
            return ['success' => false, 'message' => 'Registration session expired. Please start again.'];
        }

        return (new StoreAuth())->registerFromVerifiedPayload($payload);
    }

    public function sendResetOtp(string $login): array
    {
        $login = trim($login);
        if ($login === '') {
            return ['success' => false, 'message' => 'Email or phone is required.'];
        }

        $customer = model(CustomerModel::class)->where('status', 1)
            ->groupStart()
                ->where('email', $login)
                ->orWhere('phone', $login)
                ->orWhere('email', $this->normalizeEmail($login))
            ->groupEnd()
            ->first();

        if (! $customer || empty($customer['password'])) {
            return ['success' => false, 'message' => 'No registered account found with these details.'];
        }

        $email = $this->normalizeEmail((string) ($customer['email'] ?? ''));
        if ($email === '') {
            return ['success' => false, 'message' => 'This account has no email on file. Please contact support to reset your password.'];
        }

        $payload = ['customer_id' => (int) $customer['id']];
        $sent = $this->issueOtp($email, self::PURPOSE_RESET, $payload, 'Reset your password');
        if (! $sent['success']) {
            return $sent;
        }

        return [
            'success' => true,
            'message' => 'Verification code sent to ' . $this->maskEmail($email) . '.',
            'email'   => $email,
        ];
    }

    public function resetPasswordWithOtp(string $email, string $otp, string $password, string $passwordConfirm): array
    {
        $email = $this->normalizeEmail($email);
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
        }
        if ($password !== $passwordConfirm) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }

        $record = $this->verifyOtpRecord($email, self::PURPOSE_RESET, $otp);
        if (! $record['success']) {
            return $record;
        }

        $payload = json_decode((string) ($record['data']['payload'] ?? ''), true);
        $customerId = (int) ($payload['customer_id'] ?? 0);
        if ($customerId <= 0) {
            return ['success' => false, 'message' => 'Reset session expired. Please request a new code.'];
        }

        $model = model(CustomerModel::class);
        if (! $model->find($customerId)) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        $model->update($customerId, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return [
            'success'  => true,
            'message'  => 'Password updated successfully. You can sign in now.',
            'redirect' => site_url('account/login'),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function issueOtp(string $email, string $purpose, array $payload, string $mailLabel): array
    {
        $otpModel = model(CustomerOtpModel::class);
        $recent = $otpModel->where('email', $email)
            ->where('purpose', $purpose)
            ->where('verified_at', null)
            ->orderBy('id', 'DESC')
            ->first();

        if ($recent && strtotime((string) $recent['created_at']) > (time() - self::RESEND_SECONDS)) {
            return ['success' => false, 'message' => 'Please wait a minute before requesting another code.'];
        }

        $otp = (string) random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + (self::OTP_TTL_MINUTES * 60));

        $otpModel->where('email', $email)
            ->where('purpose', $purpose)
            ->where('verified_at', null)
            ->delete();

        $otpModel->insert([
            'email'      => $email,
            'purpose'    => $purpose,
            'otp_hash'   => password_hash($otp, PASSWORD_DEFAULT),
            'payload'    => json_encode($payload),
            'expires_at' => $expiresAt,
            'attempts'   => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (! (new AccountMailService())->sendOtp($email, $otp, $mailLabel)) {
            return ['success' => false, 'message' => 'Could not send verification email. Please try again later.'];
        }

        return ['success' => true];
    }

    /**
     * @return array{success:bool,message?:string,data?:array}
     */
    private function verifyOtpRecord(string $email, string $purpose, string $otp): array
    {
        $otp = trim($otp);
        if ($otp === '' || ! preg_match('/^\d{6}$/', $otp)) {
            return ['success' => false, 'message' => 'Please enter a valid 6-digit code.'];
        }

        $otpModel = model(CustomerOtpModel::class);
        $record = $otpModel->where('email', $email)
            ->where('purpose', $purpose)
            ->where('verified_at', null)
            ->orderBy('id', 'DESC')
            ->first();

        if (! $record) {
            return ['success' => false, 'message' => 'Verification code expired. Please request a new one.'];
        }

        if (strtotime((string) $record['expires_at']) < time()) {
            return ['success' => false, 'message' => 'Verification code has expired. Please request a new one.'];
        }

        if ((int) $record['attempts'] >= self::MAX_ATTEMPTS) {
            return ['success' => false, 'message' => 'Too many failed attempts. Please request a new code.'];
        }

        if (! password_verify($otp, (string) $record['otp_hash'])) {
            $otpModel->update($record['id'], ['attempts' => (int) $record['attempts'] + 1]);

            return ['success' => false, 'message' => 'Invalid verification code.'];
        }

        $otpModel->update($record['id'], ['verified_at' => date('Y-m-d H:i:s')]);

        return ['success' => true, 'data' => $record];
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, min(2, strlen($local)));

        return $visible . str_repeat('*', max(1, strlen($local) - 2)) . '@' . $domain;
    }
}
