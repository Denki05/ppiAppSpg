<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = "districts";
    protected $fillable =[
        'regency_id',
        'name',
    ];
    
    public function customer()
    {
        return $this->hasMany('App\Models\Master\Customer', 'kecamatan_id', 'id');
    }
}
