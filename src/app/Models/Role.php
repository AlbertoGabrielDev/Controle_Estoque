<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_menu_permission')
            ->withPivot('menu_id');
    }

    public function hasPermission($permissionName)
    {
        return $this->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    public function roleMenuPermissions()
    {
        return $this->hasMany(RoleMenuPermission::class);
    }

    public function hasMenuPermission($menuId, $permissionId)
    {
        return $this->roleMenuPermissions()
            ->where('menu_id', $menuId)
            ->where('permission_id', $permissionId)
            ->exists();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu_permission')
            ->withPivot('permission_id');
    }
}
