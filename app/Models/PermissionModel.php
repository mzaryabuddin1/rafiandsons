<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table            = 'permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['module', 'action', 'slug', 'description'];

    public function slugsForRole(int $roleId): array
    {
        $rows = $this->db->table('role_permissions rp')
            ->select('p.slug')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $roleId)
            ->get()
            ->getResultArray();

        return array_column($rows, 'slug');
    }

    public function grouped(): array
    {
        $rows = $this->orderBy('module')->orderBy('action')->findAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['module']][] = $row;
        }

        return $grouped;
    }
}
