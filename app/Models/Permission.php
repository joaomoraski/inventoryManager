<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;


class Permission extends SpatiePermission
{
    use HasFactory, HasUuids;
    private $permissionPattern = [
        "create" => 'create_',
        "read" => 'read_',
        "update" => 'update_',
        "delete" => 'delete_'
    ];
    private $typePermissions = [
        "users" => 'users',
        "stock" => 'stock',
        "sales" => 'sales'
    ];
    public function getFullNameFromPermission(string $pattern, string $type) : string
    {
        return $this->getPermissionPattern($pattern).$this->getTypePermissions($type);
    }

    public function getPermissionPattern(string $key): string
    {
        return $this->permissionPattern[$key];
    }

    public function getTypePermissions(string $key): string
    {
        return $this->typePermissions[$key];
    }


}
