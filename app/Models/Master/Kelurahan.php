<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    protected $table = "villages";
    protected $fillable =[
        'district_id',
        'name',
    ];

    public function customer()
    {
        return $this->hasMany('App\Models\Master\Customer', 'kelurahan_id', 'id');
    }
}
