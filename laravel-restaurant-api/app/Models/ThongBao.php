<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'ThongBao';
    protected $primaryKey = 'ID_ThongBao';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_ThongBao',
        'ID_USER',
        'Ten',
        'MoTa',
        'NoiDung',
        'TheLoai',
        'DieuKienKichHoat',
        'DaDoc',
        'Hide',
        'NgayTao',
        'NgayCapNhap'
    ];

    protected $casts = [
        'NgayTao' => 'datetime',
        'NgayCapNhap' => 'datetime',
        'DaDoc' => 'boolean',
        'Hide' => 'boolean',
    ];

    /**
     * Lấy thông tin người dùng
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ID_USER', 'ID_USER');
    }
}