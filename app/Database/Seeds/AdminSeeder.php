<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'is_super' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Admin', 'slug' => 'admin', 'is_super' => 0, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Staff', 'slug' => 'staff', 'is_super' => 0, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('roles')->insertBatch($roles);

        $modules = [
            'dashboard'         => ['view'],
            'categories'        => ['view', 'create', 'update', 'delete'],
            'products'          => ['view', 'create', 'update', 'delete'],
            'installment_plans' => ['view', 'create', 'update', 'delete'],
            'orders'            => ['view', 'create', 'update', 'delete'],
            'customers'         => ['view', 'create', 'update', 'delete'],
            'contents'          => ['view', 'create', 'update', 'delete'],
            'banners'           => ['view', 'create', 'update', 'delete'],
            'bank_accounts'     => ['view', 'create', 'update', 'delete'],
            'vendors'           => ['view', 'create', 'update', 'delete'],
            'settings'          => ['view', 'update'],
            'users'             => ['view', 'create', 'update', 'delete'],
            'roles'             => ['view', 'create', 'update', 'delete'],
        ];

        $permissionRows = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionRows[] = [
                    'module'      => $module,
                    'action'      => $action,
                    'slug'        => $module . '.' . $action,
                    'description' => ucfirst($action) . ' ' . str_replace('_', ' ', $module),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }
        $this->db->table('permissions')->insertBatch($permissionRows);

        $allPermissions = $this->db->table('permissions')->get()->getResultArray();
        $roleMap = [];
        foreach ($this->db->table('roles')->get()->getResultArray() as $role) {
            $roleMap[$role['slug']] = (int) $role['id'];
        }

        $rolePermissions = [];
        foreach ($allPermissions as $permission) {
            // Super admin: all
            $rolePermissions[] = [
                'role_id'       => $roleMap['super-admin'],
                'permission_id' => (int) $permission['id'],
            ];

            // Admin: everything except users/roles
            if (! in_array($permission['module'], ['users', 'roles'], true)) {
                $rolePermissions[] = [
                    'role_id'       => $roleMap['admin'],
                    'permission_id' => (int) $permission['id'],
                ];
            }

            // Staff: dashboard view + orders/customers view/update
            if (
                $permission['slug'] === 'dashboard.view'
                || (in_array($permission['module'], ['orders', 'customers'], true) && in_array($permission['action'], ['view', 'update'], true))
            ) {
                $rolePermissions[] = [
                    'role_id'       => $roleMap['staff'],
                    'permission_id' => (int) $permission['id'],
                ];
            }
        }
        $this->db->table('role_permissions')->insertBatch($rolePermissions);

        $this->db->table('users')->insert([
            'role_id'    => $roleMap['super-admin'],
            'name'       => 'Super Admin',
            'email'      => 'admin@rafiandsons.test',
            'password'   => password_hash('Admin@123', PASSWORD_DEFAULT),
            'status'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $contents = [
            ['slug' => 'homepage', 'title' => 'Homepage', 'body' => 'Welcome to Rafi & Sons.', 'status' => 1],
            ['slug' => 'about-us', 'title' => 'About Us', 'body' => 'About Rafi & Sons content goes here.', 'status' => 1],
            ['slug' => 'contact-us', 'title' => 'Contact Us', 'body' => 'Contact Rafi & Sons content goes here.', 'status' => 1],
            ['slug' => 'privacy-policy', 'title' => 'Privacy Policy', 'body' => 'Privacy policy content goes here.', 'status' => 1],
            ['slug' => 'terms-and-conditions', 'title' => 'Terms and Conditions', 'body' => 'Terms and conditions content goes here.', 'status' => 1],
            ['slug' => 'installment-terms', 'title' => 'Installment Terms', 'body' => 'Installment terms content goes here.', 'status' => 1],
        ];
        foreach ($contents as &$content) {
            $content['created_at'] = $now;
            $content['updated_at'] = $now;
        }
        $this->db->table('contents')->insertBatch($contents);

        $settings = [
            'site_name'       => 'Rafi & Sons',
            'contact_email'   => 'info@rafiandsonsnr.com',
            'contact_phone'   => '',
            'contact_address' => '',
            'facebook_url'    => '',
            'instagram_url'   => '',
            'youtube_url'     => '',
            'whatsapp_number' => '',
            'smtp_host'       => '',
            'smtp_user'       => '',
            'smtp_pass'       => '',
            'smtp_port'       => '587',
            'smtp_from_email' => '',
            'smtp_from_name'  => 'Rafi & Sons',
            'order_notify_email' => 'admin@rafiandsons.test',
        ];
        $settingRows = [];
        foreach ($settings as $key => $value) {
            $settingRows[] = [
                'key'        => $key,
                'value'      => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db->table('settings')->insertBatch($settingRows);
    }
}
