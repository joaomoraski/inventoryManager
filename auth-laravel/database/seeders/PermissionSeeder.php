<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin can do everthing.

        // Owner
        // CRUD Users
        // All the permission below

        // Stock Manager
        // CRUD Stock

        // Worker
        // Create sale
        // See products(stock)
        // See sales history from one buyer(based on cpf)
        $permissionPattern = ['create_', 'read_', 'update_', 'delete_'];
        $typePermissions = ['users', 'stock', 'sales'];

        foreach ($permissionPattern as $pattern) {
            foreach ($typePermissions as $typePermission) {
                Permission::create(['name' => $pattern . $typePermission]);
            }
        }
    }
}
