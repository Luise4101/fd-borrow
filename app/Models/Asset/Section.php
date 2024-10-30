<?php

namespace App\Models\Asset;

use App\Models\Asset\Kong;
use App\Models\Asset\Samnak;
use App\Models\Main\BorrowHead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['samnak_id', 'qsection', 'csection'];

    public $timestamps = false;
    public function samnak() {
        return $this->belongsTo(Samnak::class, 'samnak_id', 'id');
    }
    public function kongs() {
        return $this->hasMany(Kong::class);
    }
    public function borrowheads() {
        return $this->hasMany(BorrowHead::class);
    }
}
