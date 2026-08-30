<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table            = 'vendors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'business_name', 'contact_name', 'email', 'phone', 'password', 'cnic', 'city', 'address',
        'notes', 'status', 'admin_notes', 'reviewed_at', 'reviewed_by', 'last_login_at',
    ];

    public const STATUSES = [
        'pending'  => 'Pending Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    public function approvedOptions(): array
    {
        return $this->where('status', 'approved')
            ->orderBy('business_name', 'ASC')
            ->findAll();
    }

    public function displayName(array $vendor): string
    {
        $business = trim((string) ($vendor['business_name'] ?? ''));

        return $business !== '' ? $business : (string) ($vendor['contact_name'] ?? 'Vendor');
    }
}
