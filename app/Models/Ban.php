<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ban extends Model
{
    use HasFactory;

    protected $table = 'Ban';
    protected $primaryKey = 'ID_Ban';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_Ban',
        'SoBang',
        'DungTich',
        'ID_KhuVuc'
    ];

    /**
     * Lấy khu vực của bàn
     */
    public function khuVuc(): BelongsTo
    {
        return $this->belongsTo(KhuVuc::class, 'ID_KhuVuc', 'ID_KhuVuc');
    }
}