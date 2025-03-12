<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'DanhGia';
    protected $primaryKey = 'ID_DanhGia';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_DanhGia',
        'ID_USER',
        'XepHang',
        'BinhLuan',
        'TraLoi',
        'ID_NhaHang',
        'NgayTao',
        'NgayCapNhat'
    ];

    /**
     * Lấy thông tin người dùng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ID_USER', 'ID_USER');
    }

    /**
     * Lấy thông tin nhà hàng
     */
    public function nhaHang(): BelongsTo
    {
        return $this->belongsTo(NhaHang::class, 'ID_NhaHang', 'ID_NhaHang');
    }
}