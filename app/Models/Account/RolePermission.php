<?php

namespace App\Models\Account;

use App\Models\Account\Role;
use App\Models\Account\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = "role_permissions";
    protected $fillable = ['role_id', 'permission_id', 'is_create', 'is_read', 'is_update', 'is_delete', 'active'];

    public function role() {
        return $this->belongsTo(Role::class);
    }
    public function permission() {
        return $this->belongsTo(Permission::class);
    }
}
