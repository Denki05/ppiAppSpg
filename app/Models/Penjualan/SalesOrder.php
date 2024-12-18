<?php

namespace App\Models\Penjualan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SalesOrder extends Model
{
    protected $table = "penjualan_so";
    protected $fillable =[
        'brand_name',
        'kode', 
        'type', 
        'tanggal_order',
        'brand', 
        'customer_id', 
        'status'
    ];

    const STATUS = [
    	0 => 'DELETED',
    	1 => 'ACTIVE',
    	2 => 'REVIEW',
    	3 => 'SETTEL',
    	4 => 'REVISI',
    ];

    const TYPE = [
    	0 => 'CUSTOMER',
    	1 => 'CASH',
    ];

    public function status()
    {
        return array_search($this->status, self::STATUS);
    }

    public function item(){
        return $this->hasMany('App\Models\Penjualan\SalesOrderItem', 'so_id', 'id');
    }

    public function ga(){
        return $this->hasMany('App\Models\Penjualan\SalesOrderGa', 'so_id', 'id');
    }

    public function createdByUser()
    {
        $user = User::find($this->created_by);

        if($user){
            return $user->name ?? $user->email;
        }
    }

    public static function generateSO()
    {
        // Break down the current date to get the necessary parts
        $parts = explode('-', date("d-m-Y"));
        $p1 = substr($parts[2], -1); // Get the last digit of the year
        $abjadMonth = array('-', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');
        $p2 = $abjadMonth[date('n')]; // Get the alphabetical month

        // Generate the prefix for the code
        $yearMonth = $p1 . $p2;
        
        // Query the maximum existing code that starts with this prefix
        $get_max = DB::table('penjualan_so')
                     ->where('kode', 'LIKE', '%' . $yearMonth . '%')
                     ->whereNull('deleted_at')
                     ->max('kode');

        if (!$get_max) {
            // Start from 001 if no previous codes exist for this month
            $latestNumber = $yearMonth . '001';
        } else {
            // Extract and increment the numeric portion of the code
            $id = (int) substr($get_max, strlen($yearMonth)) + 1;
            $latestNumber = $yearMonth . str_pad($id, 3, '0', STR_PAD_LEFT);
        }
        
        return $latestNumber;
    }
}
