<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KhuVuc extends Model
{
    use HasFactory;

    protected $table = 'KhuVuc';
    protected $primaryKey = 'ID_KhuVuc';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_KhuVuc',
        'ID_NhaHang',
        'Ten',
        'DiaChi',
        'Tang'
    ];

    /**
     * Lấy nhà hàng của khu vực
     */
    public function nhaHang(): BelongsTo
    {
        return $this->belongsTo(NhaHang::class, 'ID_NhaHang', 'ID_NhaHang');
    }

    /**
     * Lấy các bàn trong khu vực
     */
    public function bans(): HasMany
    {
        return $this->hasMany(Ban::class, 'ID_KhuVuc', 'ID_KhuVuc');
    }
}