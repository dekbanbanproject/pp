<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Dapi_lgo_pat extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'dapi_lgo_pat';
    protected $primaryKey = 'dapi_lgo_pat_id';
    protected $fillable = [
        'blobName',
        'blobType',
        'blob'         
    ];

  
}
