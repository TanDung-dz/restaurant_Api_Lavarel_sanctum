<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'User';
    protected $primaryKey = 'ID_USER';
    public $timestamps = false;

    protected $fillable = [
        'TenDangNhap',
        'MatKhau',
        'HoVaTen',
        'Email',
        'Sdt',
        'Quyen',
        'NgayDK',
        'Anh',
        'Hide',
        'NgayTao',
        'NgayCapNhap',
    ];

    protected $hidden = [
        'MatKhau',
    ];

    // Đổi tên cột mật khẩu để Laravel biết
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }
}