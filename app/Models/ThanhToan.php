<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToan extends Model
{
    use HasFactory;

    protected $table = 'ThanhToan';
    protected $primaryKey = 'ID_ThanhToan';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_ThanhToan',
        'ID_ThongTinDatBan',
        'SoLuong',
        'PhuongThucThanhToan',
        'TrangThaiThanhToan',
        'NgayThanhToan',
        'MaGiaoDich',
        'NgayTao',
        'NgayCapNhap'
    ];

    protected $casts = [
        'NgayThanhToan' => 'datetime',
        'NgayTao' => 'datetime',
        'NgayCapNhap' => 'datetime',
    ];

    /**
     * Lấy thông tin đặt bàn
     */
    public function thongTinDatBan(): BelongsTo
    {
        return $this->belongsTo(ThongTinDatBan::class, 'ID_ThongTinDatBan', 'ID_ThongTinDatBan');
    }
}