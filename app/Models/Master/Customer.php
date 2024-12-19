<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = "master_customer";
    protected $fillable =[
        'user_id', 
        'nama',
        'alamat', 
        'phone', 
        'owner', 
        'provinsi_id',
        'kabupaten_id', 
        'kecamatan_id', 
        'kelurahan_id', 
        'status', 
        'created_by', 
        'updated_by', 
    ];

    const STATUS = [
    	0 => 'DELETED',
    	1 => 'ACTIVE',
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

    public function so(){
        return $this->hasMany('App\Models\Penjualan\SalesOrder', 'customer_id', 'id');
    }
}
