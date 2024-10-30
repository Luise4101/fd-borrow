<?php

namespace App\Models\Asset;

use App\Models\Asset\Samnak;
use App\Models\Asset\Section;
use App\Models\Main\BorrowHead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kong extends Model
{
    use HasFactory;

    protected $fillable = ['samnak_id', 'section_id', 'qkong', 'ckong', 'credit'];

    public $timestamps = false;
    public function section() {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function samnak() {
        return $this->belongsTo(Samnak::class, 'samnak_id', 'id');
    }
    public function borrowheads() {
        return $this->hasMany(BorrowHead::class);
    }
}
