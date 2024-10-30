<?php

namespace App\Models\Main;

use Exception;
use App\Models\User;
use App\Models\Asset\Kong;
use App\Enums\BorrowStatus;
use App\Models\Asset\Samnak;
use App\Models\Asset\Status;
use App\Models\Asset\Section;
use App\Models\Main\BorrowInfo;
use App\Models\Main\BorrowItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BorrowHead extends Model
{
    use HasFactory;
    protected $table = 'borrow_heads';
    protected $casts = ['attachment'=>'array', 'proof_payment'=>'array', 'status_id'=>BorrowStatus::class];
    protected $fillable = ['borrower_id', 'borrower_tel', 'borrower_lineid', 'status_id', 'qsamnak', 'qsection', 'qkong', 'qhead',
        'chead', 'approved_at', 'pickup_at', 'return_schedule', 'return_at', 'activity_name', 'q_attendee', 'q_staff',
        'activity_place', 'attachment', 'price_borrow_all', 'price_fine', 'proof_payment', 'updated_by', 'note'
    ];
    protected static function booted() {
        static::saving(function($borrowHead) {
        });
        static::deleting(function($borrowHead) {
            foreach($borrowHead->borrowitems as $borrowItem) {
                $borrowItem->reverseStoreQuantity();
            }
        });
    }

    public function borrower() {
        return $this->belongsTo(User::class, 'borrower_id', 'id');
    }
    public function status() {
        return $this->belongsTo(Status::class)->where('table_list_id', '15');
    }
    public function samnak() {
        return $this->belongsTo(Samnak::class, 'qsamnak', 'qsamnak');
    }
    public function section() {
        return $this->belongsTo(Section::class, 'qsection', 'qsection');
    }
    public function kong() {
        return $this->belongsTo(Kong::class, 'qkong', 'qkong');
    }
    public function borrowitems(): HasMany {
        return $this->hasMany(BorrowItem::class, 'borrow_head_id');
    }
    public function borrowinfos(): HasMany {
        return $this->hasMany(BorrowInfo::class, 'borrow_head_id');
    }
    public function updateSerialStatus() {
        $statusMappings = [8 => 1, 9 => 2, 10 => 1, 11 => 3, 12 => 6, 13 => 1, 14 => 1];
    }
}
