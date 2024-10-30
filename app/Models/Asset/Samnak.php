<?php

namespace App\Models\Asset;

use App\Models\Asset\Section;
use App\Models\Main\BorrowHead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Samnak extends Model
{
    use HasFactory;

    protected $fillable = ['qsamnak', 'csamnak'];

    public $timestamps = false;
    public function sections() {
        return $this->hasMany(Section::class);
    }
    public function borrowheads() {
        return $this->hasMany(BorrowHead::class);
    }
}
