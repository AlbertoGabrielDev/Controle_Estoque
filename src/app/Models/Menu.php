<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'menu_permissions');
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu_permissions')->withPivot('permission_id');
    }

    // Adicione este método
    public function hasPermissions($permission)
    {
        return $this->permissions->contains($permission);
    }
}