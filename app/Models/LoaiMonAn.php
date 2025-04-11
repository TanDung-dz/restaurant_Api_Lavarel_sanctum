<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoaiMonAn extends Model
{
    use HasFactory;

    protected $table = 'LoaiMonAn';
    protected $primaryKey = 'MaLoai';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'MaLoai',
        'TenLoai',
        'MoTa',
        'Hide'
    ];

    /**
     * Lấy các món ăn thuộc loại này
     */
    public function monAns(): HasMany
    {
        return $this->hasMany(MonAn::class, 'MaLoai', 'MaLoai');
    }
}