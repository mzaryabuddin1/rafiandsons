<?php

namespace App\Controllers;

use App\Libraries\VendorMailService;
use App\Models\VendorModel;

class VendorPortalController extends BaseStoreController
{
    public function apply()
    {
        return $this->storeView('vendor/apply', [
            'pageTitle'  => 'Become a Vendor',
            'activeMenu' => 'vendor',
            'bodyClass'  => 'store-qist',
            'cssFile'    => 'demo22.min.css',
        ]);
    }

    public function submitApplication()
    {
        $businessName = trim((string) $this->request->getPost('business_name'));
        $contactName = trim((string) $this->request->getPost('contact_name'));
        $email = trim((string) $this->request->getPost('email'));
        $phone = trim((string) $this->request->getPost('phone'));
        $password = (string) $this->request->getPost('password');
        $confirm = (string) $this->request->getPost('password_confirm');
        $cnic = trim((string) $this->request->getPost('cnic'));
        $city = trim((string) $this->request->getPost('city'));
        $address = trim((string) $this->request->getPost('address'));
        $notes = trim((string) $this->request->getPost('notes'));

        if ($businessName === '' || $contactName === '' || $email === '' || $phone === '' || $password === '') {
            return $this->jsonError('Business name, contact name, email, phone, and password are required.');
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonError('Please enter a valid email address.');
        }
        if (strlen($password) < 6) {
            return $this->jsonError('Password must be at least 6 characters.');
        }
        if ($password !== $confirm) {
            return $this->jsonError('Password confirmation does not match.');
        }

        $model = model(VendorModel::class);
        $existing = $model->where('email', $email)->first();
        if ($existing) {
            $status = (string) ($existing['status'] ?? '');
            if ($status === 'approved') {
                return $this->jsonError('A vendor account with this email already exists. Please sign in.');
            }
            if ($status === 'pending') {
                return $this->jsonError('An application with this email is already under review.');
            }

            return $this->jsonError('This email was used previously. Please contact support or use another email.');
        }

        $id = $model->insert([
            'business_name' => $businessName,
            'contact_name'  => $contactName,
            'email'         => $email,
            'phone'         => $phone,
            'password'      => password_hash($password, PASSWORD_DEFAULT),
            'cnic'          => $cnic !== '' ? $cnic : null,
            'city'          => $city !== '' ? $city : null,
            'address'       => $address !== '' ? $address : null,
            'notes'         => $notes !== '' ? $notes : null,
            'status'        => 'pending',
        ]);

        $vendor = $model->find($id);
        try {
            (new VendorMailService())->sendApplicationReceived($vendor);
        } catch (\Throwable $e) {
            log_message('error', 'Vendor application email error: ' . $e->getMessage());
        }

        return $this->jsonSuccess('Application submitted. Please check your email — your application is under review.', [
            'redirect' => site_url('vendor/apply'),
        ]);
    }
}
