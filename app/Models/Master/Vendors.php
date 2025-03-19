<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
    use HasFactory;

    protected $table = "master_vendors";
    protected $fillable = [
        'nama', 
        'alamat', 
        'owner', 
        'phone', 
        'provinsi_id', 
        'kabupaten_id',
        'kecamatan_id',
        'kelurahan_id',
        'shift_start',
        'shift_end',
        'is_cash',
        'created_by',
        'updated_by',
    ];

    public function provinsi()
    {
        return $this->BelongsTo('App\Models\Master\Provinsi', 'provinsi_id', 'id');
    }

    public function kabupaten()
    {
        return $this->BelongsTo('App\Models\Master\Kabupaten', 'kabupaten_id', 'id');
    }

    public function kecamatan()
    {
        return $this->BelongsTo('App\Models\Master\Kecamatan', 'kecamatan_id', 'id');
    }

    public function kelurahan()
    {
        return $this->BelongsTo('App\Models\Master\Kelurahan', 'kelurahan_id', 'id');
    }

    public function user()
    {
        return $this->hasMany('App\Models\User', 'vendor_id', 'id');
    }
}