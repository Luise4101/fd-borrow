<?php

namespace App\Models\Main;

use App\Models\Main\BorrowItem;
use App\Models\Main\ReturnHead;
use App\Models\Inventory\Serial;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReturnItem extends Model
{
    use HasFactory, SortableTrait;

    protected $table = 'return_items';
    protected $fillable = ['return_head_id', 'borrow_item_id', 'sort', 'q_return',
        'q_broken', 'fine_broken', 'q_missing', 'fine_missing', 'note'
    ];
    protected static function booted() {
        static::creating(function($returnItem) {
            $maxSort = ReturnItem::where('return_head_id', $returnItem->return_head_id)->max('sort');
            $returnItem->sort = $maxSort ? $maxSort + 1 : 1;
        });
    }

    public $sortable = ['order_column_name' => 'sort', 'sort_when_creating' => true];
    public function returnhead() {
        return $this->belongsTo(ReturnHead::class, 'return_head_id', 'id');
    }
    public function borrowitem() {
        return $this->belongsTo(BorrowItem::class, 'borrow_item_id', 'id');
    }
    public function serials(): BelongsToMany {
        return $this->belongsToMany(Serial::class, 'returnitem_serials')->withPivot('status_id')->withTimestamps();
    }
    public function returnedSerials(): BelongsToMany {
        return $this->serials()->wherePivot('status_id', 21);
    }
    public function brokenSerials(): BelongsToMany {
        return $this->serials()->wherePivot('status_id', 22);
    }
    public function missingSerials(): BelongsToMany {
        return $this->serials()->wherePivot('status_id', 23);
    }
}
