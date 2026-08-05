<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminSchema extends Migration
{
    public function up()
    {
        // Roles
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'is_super'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('roles', true);

        // Permissions
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'module'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('permissions', true);

        // Role permissions
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'role_id'       => ['type' => 'INT', 'unsigned' => true],
            'permission_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['role_id', 'permission_id']);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_permissions', true);

        // Admin users
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'role_id'       => ['type' => 'INT', 'unsigned' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 191],
            'password'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'status'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('users', true);

        // Categories
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug'            => ['type' => 'VARCHAR', 'constraint' => 180],
            'image'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description'     => ['type' => 'TEXT', 'null' => true],
            'status'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order'      => ['type' => 'INT', 'default' => 0],
            'meta_title'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description'=> ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('categories', true);

        // Products
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category_id'           => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 200],
            'slug'                  => ['type' => 'VARCHAR', 'constraint' => 220],
            'sku'                   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'price'                 => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'images'                => ['type' => 'TEXT', 'null' => true],
            'description'           => ['type' => 'TEXT', 'null' => true],
            'stock_status'          => ['type' => 'ENUM', 'constraint' => ['in_stock', 'out_of_stock'], 'default' => 'in_stock'],
            'installment_available' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'status'                => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'meta_title'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('sku');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('products', true);

        // Installment plans
        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'                 => ['type' => 'VARCHAR', 'constraint' => 150],
            'down_payment'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'monthly_installment'  => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'months'               => ['type' => 'INT', 'default' => 1],
            'total_payable'        => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'processing_charges'   => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'terms'                => ['type' => 'TEXT', 'null' => true],
            'status'               => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('installment_plans', true);

        // Product installment plans
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'          => ['type' => 'INT', 'unsigned' => true],
            'installment_plan_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['product_id', 'installment_plan_id']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('installment_plan_id', 'installment_plans', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_installment_plans', true);

        // Customers
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'cnic'       => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'address'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'city'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'notes'      => ['type' => 'TEXT', 'null' => true],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('phone');
        $this->forge->createTable('customers', true);

        // Orders
        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_number'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'customer_id'          => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'customer_name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'customer_email'       => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'customer_phone'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'customer_cnic'        => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'customer_address'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'customer_city'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'installment_plan_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'plan_name'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'down_payment'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'monthly_installment'  => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'months'               => ['type' => 'INT', 'default' => 0],
            'processing_charges'   => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'total_payable'        => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'subtotal'             => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'status'               => ['type' => 'ENUM', 'constraint' => [
                'new', 'under_review', 'customer_contacted', 'approved', 'rejected', 'processing', 'completed', 'cancelled',
            ], 'default' => 'new'],
            'admin_notes'          => ['type' => 'TEXT', 'null' => true],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_number');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('installment_plan_id', 'installment_plans', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('orders', true);

        // Order items
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'     => ['type' => 'INT', 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 200],
            'sku'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'unit_price'   => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'quantity'     => ['type' => 'INT', 'default' => 1],
            'line_total'   => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('order_items', true);

        // Contents
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'body'       => ['type' => 'LONGTEXT', 'null' => true],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('contents', true);

        // Banners
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'link'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('banners', true);

        // Settings
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'key'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('key');
        $this->forge->createTable('settings', true);
    }

    public function down()
    {
        $tables = [
            'settings', 'banners', 'contents', 'order_items', 'orders', 'customers',
            'product_installment_plans', 'installment_plans', 'products', 'categories',
            'users', 'role_permissions', 'permissions', 'roles',
        ];
        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
