<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_menu_permissions')
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

    public function hasPermissionByNames(string $menuSlug, string $permissionName): bool
    {
        return $this->menus()
            ->where('menus.slug', $menuSlug)
            ->whereHas('permissions', function ($q) use ($permissionName) {
                $q->where('permissions.name', $permissionName);
            })
            ->exists();
    }

    public function hasMenuPermission(int $menuId, int $permissionId): bool
    {
        return $this->menus()
            ->where('menus.id', $menuId)
            ->wherePivot('permission_id', $permissionId)
            ->exists();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_menu_permissions', 'role_id', 'menu_id')
            ->withPivot('permission_id');
    }
}
