<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThongTinDatBan extends Model
{
    use HasFactory;

    protected $table = 'ThongTinDatBan';
    protected $primaryKey = 'ID_ThongTinDatBan';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_ThongTinDatBan',
        'ID_USER',
        'ThoiGianDatBan',
        'SoLuongKhach',
        'YeuCau',
        'NgayTao',
        'NgayCapNhap',
        'TrangThai', // Thêm trường trạng thái: 0 = chờ xác nhận, 1 = đã xác nhận, 2 = đã hủy
    ];

    protected $casts = [
        'ThoiGianDatBan' => 'datetime',
        'NgayTao' => 'datetime',
        'NgayCapNhap' => 'datetime',
    ];

    /**
     * Lấy thông tin người dùng đặt bàn
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ID_USER', 'ID_USER');
    }

    /**
     * Lấy thông tin chi tiết đặt bàn
     */
    public function chiTietDatBans(): HasMany
    {
        return $this->hasMany(BangChiTietDatBan::class, 'ID_ThongTinDatBan', 'ID_ThongTinDatBan');
    }

    /**
     * Lấy thông tin thanh toán
     */
    public function thanhToan(): HasOne
    {
        return $this->hasOne(ThanhToan::class, 'ID_ThongTinDatBan', 'ID_ThongTinDatBan');
    }

    /**
     * Lấy thông tin món ăn đã đặt
     */
    public function chiTietDatMons(): HasMany
    {
        return $this->hasMany(ChiTietDatMon::class, 'ID_ThongTinDatBan', 'ID_ThongTinDatBan');
    }
}