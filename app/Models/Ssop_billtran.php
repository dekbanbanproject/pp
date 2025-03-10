<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Ssop_billtran extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $connection = 'mysql';
    protected $table = 'ssop_billtran';
    protected $primaryKey = 'ssop_billtran_id';
    protected $fillable = [  
        'Station',  
        'Authencode',  
        'vstdate',
        'DTtran', 
        'Hcode', 
        'Invno' 
    ];

  
}
