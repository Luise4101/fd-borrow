<?php

namespace App\Models\Account;

use App\Models\Account\TableList;
use App\Models\Account\RolePermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'table_list_id', 'active'];

    public function table_list() {
        return $this->belongsTo(TableList::class);
    }
    public function role_permissions(): HasMany {
        return $this->hasmany(RolePermission::class);
    }
}
