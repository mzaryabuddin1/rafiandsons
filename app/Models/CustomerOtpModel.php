<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerOtpModel extends Model
{
    protected $table            = 'customer_otps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'email', 'purpose', 'otp_hash', 'payload', 'expires_at', 'attempts', 'verified_at', 'created_at',
    ];
}
