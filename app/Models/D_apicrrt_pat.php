<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class D_apicrrt_pat extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'd_apicrrt_pat';
    protected $primaryKey = 'd_apicrrt_pat_id';
    protected $fillable = [
        'blobName',
        'blobType',
        'blob'         
    ];

  
}
