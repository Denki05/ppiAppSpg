<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $table = "provinces";
    protected $fillable =[
        'name',
    ];

    public function customer()
    {
        return $this->hasMany('App\Models\Master\Customer', 'provinsi_id', 'id');
    }
}
