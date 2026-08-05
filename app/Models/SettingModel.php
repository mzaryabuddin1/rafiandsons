<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['key', 'value'];

    public function getMap(): array
    {
        $rows = $this->findAll();
        $map = [];
        foreach ($rows as $row) {
            $map[$row['key']] = $row['value'];
        }

        return $map;
    }

    public function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            $existing = $this->where('key', $key)->first();
            if ($existing) {
                $this->update($existing['id'], ['value' => $value]);
            } else {
                $this->insert(['key' => $key, 'value' => $value]);
            }
        }
    }
}
