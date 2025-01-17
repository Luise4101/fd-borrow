<?php

namespace App\Models\Main;

use App\Models\User;
use App\Models\Asset\Status;
use App\Models\Main\BorrowHead;
use App\Models\Main\ReturnItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnHead extends Model
{
    use HasFactory;

    protected $table = 'return_heads';
    protected $fillable = ['borrow_head_id', 'status_id', 'user_id', 'user_fullname',
        'user_tel', 'user_lineid', 'price_fine_all', 'received_by', 'note'
    ];

    public function borrowhead() {
        return $this->belongsTo(BorrowHead::class, 'borrow_head_id', 'id');
    }
    public function status() {
        return $this->belongsTo(Status::class)->where('table_list_id', '16');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function returnitems(): HasMany {
        return $this->hasMany(ReturnItem::class, 'return_head_id');
    }

    public function getDisplayUsernameAttribute() {
        if($this->user_id) {
            return $this->user ? $this->user->name : null;
        }
        return $this->user_fullname;
    }
}
