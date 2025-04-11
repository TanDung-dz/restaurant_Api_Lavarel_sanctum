<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonAn extends Model
{
    use HasFactory;

    protected $table = 'MonAn';
    protected $primaryKey = 'ID_MonAn';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_MonAn',
        'ID_NhaHang',
        'MaLoai',
        'TenMonAn',
        'MoTa',
        'Gia',
        'TrangThai',
        'Anh1',
        'Anh2',
        'Anh3',
        'Anh4',
        'Anh5',
        'NgayTao',
        'NgayCapNhap'
    ];

    /**
     * Lấy loại món ăn
     */
    public function loaiMonAn(): BelongsTo
    {
        return $this->belongsTo(LoaiMonAn::class, 'MaLoai', 'MaLoai');
    }

    /**
     * Lấy nhà hàng
     */
    public function nhaHang(): BelongsTo
    {
        return $this->belongsTo(NhaHang::class, 'ID_NhaHang', 'ID_NhaHang');
    }
}