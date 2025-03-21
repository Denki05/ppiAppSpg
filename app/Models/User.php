<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_DEV = 'dev';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SPG = 'spg';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'area',
        'kota', 
        'provinsi_id', 
        'kabupaten_id', 
        'kecamatan_id',
        'kelurahan_id',
        'vendor_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->BelongsTo('App\Models\Master\Vendors', 'vendor_id', 'id');
    }

    public function kabupaten()
    {
        return $this->BelongsTo('App\Models\Master\Kabupaten', 'kabupaten_id', 'id');
    }

    public function stock_ga(){
        return $this->hasMany('App\Models\Master\StockGa', 'user_id', 'id');
    }
}
