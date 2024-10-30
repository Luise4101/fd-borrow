<?php

namespace App\Models\Account;

use App\Models\User;
use App\Models\Account\RolePermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active'];
    protected $casts = ['active' => 'boolean'];

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'role_users');
    }
    public function role_permissions(): HasMany {
        return $this->hasmany(RolePermission::class);
    }
}
