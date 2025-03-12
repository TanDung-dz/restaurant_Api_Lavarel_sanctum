<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietDatMon extends Model
{
    use HasFactory;

    protected $table = 'ChiTietDatMon';
    protected $primaryKey = ['ID_MonAn', 'ID_ThongTinDatBan'];
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_MonAn',
        'ID_ThongTinDatBan',
        'ID_ChiTietDatMon',
        'SoLuong',
        'GhiChu',
        'DonGia',
        'ThanhTien'
    ];

    /**
     * Lấy thông tin món ăn
     */
    public function monAn(): BelongsTo
    {
        return $this->belongsTo(MonAn::class, 'ID_MonAn', 'ID_MonAn');
    }

    /**
     * Lấy thông tin đặt bàn
     */
    public function thongTinDatBan(): BelongsTo
    {
        return $this->belongsTo(ThongTinDatBan::class, 'ID_ThongTinDatBan', 'ID_ThongTinDatBan');
    }
    
    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}