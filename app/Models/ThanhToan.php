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
        // Các trường hiện có
        'ID_ThanhToan',
        'ID_ThongTinDatBan',
        'SoLuong',
        'PhuongThucThanhToan',
        'TrangThaiThanhToan',
        'NgayThanhToan',
        'MaGiaoDich',
        'NgayTao',
        'NgayCapNhap',
        // Thêm các trường MoMo
        'MoMo_RequestId',
        'MoMo_OrderId',
        'MoMo_PaymentUrl',
        'MoMo_TransId',
        'MoMo_ResultCode',
        'MoMo_Message',
        'MoMo_ExtraData'
    ];

    protected $casts = [
        'NgayThanhToan' => 'datetime',
        'NgayTao' => 'datetime',
        'NgayCapNhap' => 'datetime',
        'MoMo_ExtraData' => 'array',
    ];

    /**
     * Lấy thông tin đặt bàn
     */
    public function thongTinDatBan(): BelongsTo
    {
        return $this->belongsTo(ThongTinDatBan::class, 'ID_ThongTinDatBan', 'ID_ThongTinDatBan');
    }
}