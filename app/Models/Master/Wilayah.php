<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = "wilayah";
    protected $fillable =[
        'provinsi_id', 
        'nama_kawasan',
        'kota_id', 
        'created_by', 
        'updated_by',
    ];

    public function provinsi()
    {
        return $this->BelongsTo('App\Models\Master\Provinsi', 'provinsi_id', 'id');
    }

    public function kabupaten()
    {
        return $this->BelongsTo('App\Models\Master\Kabupaten', 'kota_id', 'id');
    }
}
