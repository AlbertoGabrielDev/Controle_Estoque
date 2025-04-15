<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleMenuPermission extends Model
{
    use HasFactory;

    protected $table = 'role_menu_permission';
    
    protected $fillable = ['role_id', 'menu_id', 'permission_id'];
}
