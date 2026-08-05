<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'parent_id', 'name', 'slug', 'image', 'description', 'status', 'sort_order',
        'meta_title', 'meta_description',
    ];

    public function parentsOnly(bool $activeOnly = false): array
    {
        $builder = $this->where('parent_id', null);
        if ($activeOnly) {
            $builder->where('status', 1);
        }

        return $builder->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->findAll();
    }

    public function childrenOf(int $parentId, bool $activeOnly = false): array
    {
        $builder = $this->where('parent_id', $parentId);
        if ($activeOnly) {
            $builder->where('status', 1);
        }

        return $builder->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Nested tree: parents with children[].
     */
    public function tree(bool $activeOnly = true): array
    {
        $builder = $this->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
        if ($activeOnly) {
            $builder->where('status', 1);
        }
        $all = $builder->findAll();

        $parents = [];
        $childrenMap = [];
        foreach ($all as $row) {
            if (empty($row['parent_id'])) {
                $row['children'] = [];
                $parents[(int) $row['id']] = $row;
            } else {
                $childrenMap[(int) $row['parent_id']][] = $row;
            }
        }

        foreach ($childrenMap as $parentId => $kids) {
            if (isset($parents[$parentId])) {
                $parents[$parentId]['children'] = $kids;
            }
        }

        return array_values($parents);
    }

    /**
     * Flat options for selects: "Parent" / "— Subcategory"
     */
    public function flatOptions(?int $excludeId = null): array
    {
        $tree = $this->tree(false);
        $options = [];
        foreach ($tree as $parent) {
            if ($excludeId && (int) $parent['id'] === $excludeId) {
                continue;
            }
            $options[] = [
                'id'        => (int) $parent['id'],
                'name'      => $parent['name'],
                'parent_id' => null,
                'label'     => $parent['name'],
                'is_parent' => true,
            ];
            foreach ($parent['children'] as $child) {
                if ($excludeId && (int) $child['id'] === $excludeId) {
                    continue;
                }
                $options[] = [
                    'id'        => (int) $child['id'],
                    'name'      => $child['name'],
                    'parent_id' => (int) $parent['id'],
                    'label'     => '— ' . $child['name'],
                    'is_parent' => false,
                ];
            }
        }

        return $options;
    }

    /**
     * IDs for a category including its children (for shop filters).
     */
    public function idsWithChildren(int $categoryId): array
    {
        $ids = [$categoryId];
        $children = $this->where('parent_id', $categoryId)->findAll();
        foreach ($children as $child) {
            $ids[] = (int) $child['id'];
        }

        return $ids;
    }
}
