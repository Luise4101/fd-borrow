<?php

namespace App\Models\Main;

use App\Models\Main\BorrowHead;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BorrowInfo extends Model
{
    use HasFactory, SortableTrait;

    protected $table = 'borrow_infos';
    protected $fillable = ['borrow_head_id','sort','purpose','q_use'];
    protected static function booted() {
        static::creating(function($borrowInfo) {
            $maxSort = BorrowInfo::max('sort');
            $borrowInfo->sort = $maxSort + 1;
        });
    }

    public $timestamps = false;
    public $sortable = ['order_column_name' => 'sort', 'sort_when_creating' => true];
    public function borrowhead() {
        return $this->belongsTo(BorrowHead::class, 'borrow_head_id', 'id');
    }
}
