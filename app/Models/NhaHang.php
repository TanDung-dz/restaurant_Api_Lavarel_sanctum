<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaHang extends Model
{
    use HasFactory;

    protected $table = 'NhaHang';
    protected $primaryKey = 'ID_NhaHang';
    public $timestamps = false;
    public $incrementing = false; // Thêm dòng này

    protected $fillable = [
        'ID_NhaHang', // Thêm dòng này
        'TenNhaHang',
        'DiaChi',
        'Sdt',
        'Email',
        'MieuTa',
        'OpenTime',
        'CloseTime',
        'DungTich',
        'XepHangTrungBinh',
        'Anh1',
        'Anh2',
        'Anh3',
        'NgayTao',
        'NgayCapNhap',
    ];
}