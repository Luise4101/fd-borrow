<?php

namespace App\Filament\Resources\Account\RolePermissionResource\Pages;

use App\Models\Account\Role;
use App\Models\Account\Permission;
use App\Models\Account\RolePermission;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Account\RolePermissionResource;

class ListRolePermissions extends ListRecords
{
    protected static string $resource = RolePermissionResource::class;

    public function __construct() {
        $roles = Role::get();
        $permissions = Permission::get();
        foreach($roles as $ro) {
            foreach($permissions as $per) {
                $found = RolePermission::where('role_id', $ro->id)
                    ->where('permission_id', $per->id)
                    ->exists();
                if(!$found) {
                    RolePermission::create([
                        'role_id' => $ro->id,
                        'permission_id' => $per->id,
                        'is_create' => 0,
                        'is_read' => 0,
                        'is_update' => 0,
                        'is_delete' => 0,
                        'active' => 1
                    ]);
                }
            }
        }
    }
}
