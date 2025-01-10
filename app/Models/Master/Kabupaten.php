<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $table = "regencies";
    protected $fillable =[
        'province_id',
        'name',
        'area'
    ];

    public function customer()
    {
        return $this->hasMany('App\Models\Master\Customer', 'kabupaten_id', 'id');
    }
}
