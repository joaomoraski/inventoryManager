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

        $users_permission = ["create_users", "read_users", "update_users", "delete_users"];
        $stock_permission = ["create_stock", "read_stock", "update_stock", "delete_stock"];
        $sales_permission = ["create_sales", "read_sales", "update_sales", "delete_sales"];

        $ownerRole = Role::create(['name' => 'owner']);
        $ownerRole->givePermissionTo($users_permission);
        $ownerRole->givePermissionTo($stock_permission);
        $ownerRole->givePermissionTo($sales_permission);

        $stockManagerRole = Role::create(['name' => 'stockManager']);
        $stockManagerRole->givePermissionTo($stock_permission);

        $workerRole = Role::create(['name' => 'worker']);
        $workerRole->givePermissionTo($sales_permission);
        $workerRole->givePermissionTo("read_stock");
    }
}
