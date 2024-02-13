<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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

    public function run(): void
    {
        Role::create(['name' => 'admin']);

        $userPermission = ["create_users", "read_users", "update_users", "delete_users"];
        $stockPermission = ["create_stock", "read_stock", "update_stock", "delete_stock"];
        $salesPermission = ["create_sales", "read_sales", "update_sales", "delete_sales"];
        $managerPermission = ["create_manager", "read_manager", "update_manager", "delete_manager"];

        $ownerRole = Role::create(['name' => 'owner']);
        $ownerRole->givePermissionTo($userPermission);
        $ownerRole->givePermissionTo($stockPermission);
        $ownerRole->givePermissionTo($salesPermission);
        $ownerRole->givePermissionTo($managerPermission);


        $stockManagerRole = Role::create(['name' => 'stockManager']);
        $stockManagerRole->givePermissionTo($stockPermission);

        $workerRole = Role::create(['name' => 'worker']);
        $workerRole->givePermissionTo($salesPermission);
        $workerRole->givePermissionTo("read_stock");
    }
}
