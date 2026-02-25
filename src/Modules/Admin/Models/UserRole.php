<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{

    protected $table = 'user_roles';
    protected $fillable = [
        'id',
        'user_id',
        'role_id'
    ];

    use HasFactory;
}
