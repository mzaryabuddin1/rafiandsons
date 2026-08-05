<?php

namespace App\Models;

use CodeIgniter\Model;

class ContentModel extends Model
{
    protected $table            = 'contents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['slug', 'title', 'body', 'status'];
}
