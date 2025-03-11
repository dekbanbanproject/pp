<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Checkup_config extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'checkup_config';
    protected $primaryKey = 'checkup_config_id';
    protected $fillable = [
        'lab_items_code',
        'lab_items_name',
        'sex'         
    ];

  
}
