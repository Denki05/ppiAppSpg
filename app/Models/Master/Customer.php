<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = "master_customers";
    protected $fillable =[
        'user_id', 
        'nama',
        'alamat', 
        'phone', 
        'owner', 
        'kecamatan',
        'kota', 
        'provinsi', 
        'created_by', 
        'updated_by', 
    ];

    const STATUS = [
    	0 => 'DELETED',
    	1 => 'ACTIVE',
    ];

    const ZONE = [

    ];
}
