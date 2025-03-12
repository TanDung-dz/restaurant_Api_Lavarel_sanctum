<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BangChiTietDatBan extends Model
{
    use HasFactory;

    protected $table = 'BangChiTietDatBan';
    protected $primaryKey = 'ID_ChiTietDatBan';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_ChiTietDatBan',
        'ID_Ban',
        'ID_ThongTinDatBan'
    ];

    /**
     * Lấy thông tin bàn
     */
    public function ban(): BelongsTo
    {
        return $this->belongsTo(Ban::class, 'ID_Ban', 'ID_Ban');
    }

    /**
     * Lấy thông tin đặt bàn
     */
    public function thongTinDatBan(): BelongsTo
    {
        return $this->belongsTo(ThongTinDatBan::class, 'ID_ThongTinDatBan', 'ID_ThongTinDatBan');
    }
}