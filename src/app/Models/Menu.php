<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'route', 'parent_id', 'order'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }


    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_menu_permissions', 'menu_id', 'role_id')
            ->withPivot('permission_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_menu_permissions', 'menu_id', 'permission_id')
            ->withPivot('role_id');
    }

    // Adicione este método
    public function hasPermissions($permission)
    {
        return $this->permissions->contains($permission);
    }
}