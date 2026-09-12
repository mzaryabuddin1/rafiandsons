<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Flush catalog/order data for go-live while keeping contents, bank accounts, and settings.
 * Recreates a single Super Admin user.
 *
 * Usage:
 *   php spark app:prepare-live --email=you@example.com --password='Secret123!' --name="Super Admin"
 */
class PrepareLive extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'app:prepare-live';
    protected $description = 'Flush shop data (keep contents/bank_accounts/settings) and create Super Admin.';
    protected $usage       = 'app:prepare-live --email=email --password=password [--name=Name] [--force]';
    protected $options     = [
        '--email'    => 'Super admin email (required)',
        '--password' => 'Super admin password (required)',
        '--name'     => 'Super admin display name (default: Super Admin)',
        '--force'    => 'Skip confirmation prompt',
    ];

    /** Tables to empty (order does not matter when FK checks are off). */
    protected array $flushTables = [
        'order_items',
        'orders',
        'product_installment_plans',
        'installment_plans',
        'products',
        'categories',
        'banners',
        'customers',
        'customer_otps',
        'vendors',
        'users',
    ];

    /** Never touch these. */
    protected array $preserveTables = [
        'contents',
        'bank_accounts',
        'settings',
        'roles',
        'permissions',
        'role_permissions',
        'migrations',
    ];

    public function run(array $params)
    {
        $email    = trim((string) ($params['email'] ?? CLI::getOption('email') ?? ''));
        $password = (string) ($params['password'] ?? CLI::getOption('password') ?? '');
        $name     = trim((string) ($params['name'] ?? CLI::getOption('name') ?? 'Super Admin'));
        $force    = (bool) (CLI::getOption('force') ?? false);

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            CLI::error('Provide a valid --email=...');

            return;
        }
        if (strlen($password) < 6) {
            CLI::error('Provide --password=... (min 6 characters)');

            return;
        }
        if ($name === '') {
            $name = 'Super Admin';
        }

        $db = db_connect();
        CLI::write('Database: ' . $db->getDatabase(), 'yellow');
        CLI::write('Will KEEP: ' . implode(', ', $this->preserveTables), 'green');
        CLI::write('Will FLUSH: ' . implode(', ', $this->flushTables), 'red');
        CLI::write('Super Admin: ' . $email, 'yellow');

        if (! $force && CLI::prompt('Type YES to continue', '') !== 'YES') {
            CLI::write('Cancelled.', 'light_gray');

            return;
        }

        $existing = $db->listTables();
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->flushTables as $table) {
            if (! in_array($table, $existing, true)) {
                CLI::write("skip missing table: {$table}", 'light_gray');
                continue;
            }
            $db->table($table)->truncate();
            CLI::write("flushed: {$table}", 'green');
        }

        $db->query('SET FOREIGN_KEY_CHECKS=1');

        // Ensure super-admin role exists (including soft-deleted)
        $role = $db->table('roles')->where('slug', 'super-admin')->get()->getRowArray();
        if (! $role) {
            $now = date('Y-m-d H:i:s');
            $db->table('roles')->insert([
                'name'       => 'Super Admin',
                'slug'       => 'super-admin',
                'is_super'   => 1,
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
            $roleId = (int) $db->insertID();
            CLI::write('created super-admin role', 'green');
        } else {
            $roleId = (int) $role['id'];
            $db->table('roles')->where('id', $roleId)->update([
                'is_super'   => 1,
                'status'     => 1,
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Ensure super role has all permissions
        $permissionIds = array_column(
            $db->table('permissions')->select('id')->get()->getResultArray(),
            'id'
        );
        foreach ($permissionIds as $pid) {
            $exists = $db->table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', (int) $pid)
                ->countAllResults();
            if (! $exists) {
                $db->table('role_permissions')->insert([
                    'role_id'       => $roleId,
                    'permission_id' => (int) $pid,
                ]);
            }
        }

        $now = date('Y-m-d H:i:s');
        $db->table('users')->insert([
            'role_id'    => $roleId,
            'name'       => $name,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'status'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);
        CLI::write('Super Admin created: ' . $email, 'green');

        // Clear product/receipt uploads (keep bank logos)
        $this->clearUploadDir(FCPATH . 'uploads/products');
        $this->clearUploadDir(FCPATH . 'uploads/receipts');
        CLI::write('Cleared uploads/products and uploads/receipts', 'green');

        helper('admin');
        CLI::newLine();
        CLI::write('Done.', 'green');
        CLI::write('Login URL: ' . admin_url('login'), 'yellow');
        CLI::write('Preserved contents / bank_accounts / settings.', 'green');
    }

    protected function clearUploadDir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @rmdir($file->getPathname());
            } else {
                @unlink($file->getPathname());
            }
        }
    }
}
