<?php

namespace App\Models\Account;

use App\Models\Asset\Status;
use App\Models\Asset\Category;
use App\Models\Account\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TableList extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function permissions(): HasMany {
        return $this->hasmany(Permission::class);
    }
    public function statues(): HasMany {
        return $this->hasmany(Status::class);
    }
    public function categories(): HasMany {
        return $this->hasmany(Category::class);
    }
}
