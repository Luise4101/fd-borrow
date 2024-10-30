<?php

namespace App\Models\Asset;

use App\Models\Account\TableList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'table_list_id', 'active'];

    public function table_list() {
        return $this->belongsTo(TableList::class);
    }
}
