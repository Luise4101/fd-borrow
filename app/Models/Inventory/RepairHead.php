<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Asset\Status;
use App\Models\Asset\Supplier;
use App\Models\Inventory\RepairItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairHead extends Model
{
    use HasFactory;

    protected $table = 'repair_heads';
    protected $fillable = ['status_id', 'created_by', 'sended_at', 'returned_at', 'price_repair_all', 'supplier_id', 'attachment', 'updated_by', 'note'];
    protected $casts = ['attachment' => 'array'];
    protected static function booted() {
        static::deleting(function($repairHead) {
            foreach($repairHead->repairitems as $repairItem) {
                $repairItem->reverseStoreQuantity();
            }
        });
    }

    public function status() {
        return $this->belongsTo(Status::class)->where('table_list_id', '14');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function creater(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updater(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function repairitems(): HasMany {
        return $this->hasMany(RepairItem::class);
    }
}
