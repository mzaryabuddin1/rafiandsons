<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVendorsAndProductVendorTagging extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'business_name' => ['type' => 'VARCHAR', 'constraint' => 191],
            'contact_name'  => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 191],
            'phone'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'password'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'cnic'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'city'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'address'       => ['type' => 'TEXT', 'null' => true],
            'notes'         => ['type' => 'TEXT', 'null' => true],
            'status'        => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'admin_notes'   => ['type' => 'TEXT', 'null' => true],
            'reviewed_at'   => ['type' => 'DATETIME', 'null' => true],
            'reviewed_by'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('vendors', true);

        $this->forge->addColumn('products', [
            'vendor_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'category_id',
            ],
        ]);

        $this->forge->addColumn('order_items', [
            'vendor_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'product_id',
            ],
            'vendor_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
                'null'       => true,
                'after'      => 'vendor_id',
            ],
        ]);

        $now = date('Y-m-d H:i:s');
        foreach (['view', 'create', 'update', 'delete'] as $action) {
            $slug = 'vendors.' . $action;
            if ($this->db->table('permissions')->where('slug', $slug)->countAllResults() > 0) {
                continue;
            }
            $this->db->table('permissions')->insert([
                'module'      => 'vendors',
                'action'      => $action,
                'slug'        => $slug,
                'description' => ucfirst($action) . ' vendors',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $permissionIds = $this->db->table('permissions')
            ->where('module', 'vendors')
            ->get()
            ->getResultArray();
        $roles = $this->db->table('roles')
            ->whereIn('slug', ['super-admin', 'admin'])
            ->get()
            ->getResultArray();

        foreach ($roles as $role) {
            foreach ($permissionIds as $permission) {
                $exists = $this->db->table('role_permissions')
                    ->where('role_id', $role['id'])
                    ->where('permission_id', $permission['id'])
                    ->countAllResults();
                if ($exists) {
                    continue;
                }
                $this->db->table('role_permissions')->insert([
                    'role_id'       => $role['id'],
                    'permission_id' => $permission['id'],
                ]);
            }
        }
    }

    public function down()
    {
        $permissionIds = array_column(
            $this->db->table('permissions')->where('module', 'vendors')->get()->getResultArray(),
            'id'
        );
        if ($permissionIds) {
            $this->db->table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
            $this->db->table('permissions')->where('module', 'vendors')->delete();
        }

        $this->forge->dropColumn('order_items', ['vendor_id', 'vendor_name']);
        $this->forge->dropColumn('products', 'vendor_id');
        $this->forge->dropTable('vendors', true);
    }
}
