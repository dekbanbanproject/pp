<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Checkup_lab extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'checkup_lab';
    protected $primaryKey = 'checkup_lab_id';
    protected $fillable = [
        'vn',
        'hn',
        'order_date'         
    ];

  
}
