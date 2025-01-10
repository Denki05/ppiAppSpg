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
        'area',
        'kabupaten_id', 
        'created_by', 
        'updated_by',
    ];

    protected $casts = [
        'kabupaten_id' => 'array',
    ];

    public function provinsi()
    {
        return $this->BelongsTo('App\Models\Master\Provinsi', 'provinsi_id', 'id');
    }

    public function kabupaten()
    {
        return $this->BelongsTo('App\Models\Master\Kabupaten', 'kabupaten_id', 'id');
    }
}
