<?php

namespace Modules\Admin\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Role extends Model
{
    use HasFactory;
    use HasDatatableConfig;
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
        $menuSlug = mb_strtolower($menuSlug);

        return $this->menus()
            ->whereRaw('LOWER(menus.slug) = ?', [$menuSlug])
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
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_menu_permissions', 'role_id', 'menu_id')
            ->withPivot('permission_id');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.name", 'label' => 'Perfil', 'order' => true, 'search' => true],
            'acoes' => ['computed' => true],
        ];
    }

    public static function dtFilters(): array
    {
        $t = (new static)->getTable();
        return [
            'q' => [
                'type' => 'text',
                'columns' => [
                    "{$t}.name",
                ],
            ],
        ];
    }
}
