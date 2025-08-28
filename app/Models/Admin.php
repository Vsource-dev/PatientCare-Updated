<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'password',
        'username',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    
        'email_verified_at' => 'datetime',
    ];
}