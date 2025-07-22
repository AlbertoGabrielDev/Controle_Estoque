<?php

namespace App\Models;

use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasStatus;
    protected $table= 'users';
    protected $primaryKey = 'id';

    protected $fillable=[
        'name',
        'email',
        'password',
        'profile_photo_path'
    ];

    use HasFactory;
}
