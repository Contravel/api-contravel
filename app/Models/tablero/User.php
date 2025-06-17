<?php

namespace App\Models\tablero;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    protected $filliable = [
        'userName',
        'firstName',
        'lastName1',
        'lastName2',
        'email',
        'admin',
        'nameAgency',
        'refenrence',
        'emailAgency',
        'status',
    ];
}
