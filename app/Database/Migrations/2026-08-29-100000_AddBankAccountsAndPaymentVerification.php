<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBankAccountsAndPaymentVerification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'bank_name'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'account_title'  => ['type' => 'VARCHAR', 'constraint' => 150],
            'account_number' => ['type' => 'VARCHAR', 'constraint' => 100],
            'iban'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'branch'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'sort_order'     => ['type' => 'INT', 'default' => 0],
            'status'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('bank_accounts', true);

        $this->forge->addColumn('orders', [
            'receipt_image'       => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'admin_notes',
            ],
            'payment_verified'    => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'receipt_image',
            ],
            'payment_verified_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'payment_verified',
            ],
        ]);

        $now = date('Y-m-d H:i:s');
        $actions = ['view', 'create', 'update', 'delete'];
        foreach ($actions as $action) {
            $exists = $this->db->table('permissions')
                ->where('slug', 'bank_accounts.' . $action)
                ->countAllResults();
            if ($exists) {
                continue;
            }

            $this->db->table('permissions')->insert([
                'module'      => 'bank_accounts',
                'action'      => $action,
                'slug'        => 'bank_accounts.' . $action,
                'description' => ucfirst($action) . ' bank accounts',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $permissionIds = $this->db->table('permissions')
            ->where('module', 'bank_accounts')
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
            $this->db->table('permissions')->where('module', 'bank_accounts')->get()->getResultArray(),
            'id'
        );
        if ($permissionIds) {
            $this->db->table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
            $this->db->table('permissions')->where('module', 'bank_accounts')->delete();
        }

        $this->forge->dropColumn('orders', ['receipt_image', 'payment_verified', 'payment_verified_at']);
        $this->forge->dropTable('bank_accounts', true);
    }
}
