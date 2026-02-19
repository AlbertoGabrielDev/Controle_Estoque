<?php

namespace App\Models;

use App\Traits\HasDatatableConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, HasDatatableConfig;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'id_unidade_fk',
        'status',
        'profile_photo_path'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function canToggleStatus()
    {
        return $this->roles()->whereHas('permissions', function ($query) {
            $query->where('name', 'status');
        })->exists();
    }

    public function hasPermission(string $menuSlug, string $permissionName): bool
    {
        if ((int) $this->id === 1) {
            return true;
        }
        $roleIds = $this->roles()->pluck('roles.id');
        if ($roleIds->isEmpty()) {
            return false;
        }

        $exists = DB::table('role_menu_permissions as rmp')
            ->join('menus as m', 'm.id', '=', 'rmp.menu_id')
            ->join('permissions as p', 'p.id', '=', 'rmp.permission_id')
            ->whereIn('rmp.role_id', $roleIds->all())
            ->where('m.slug', $menuSlug)
            ->where('p.name', $permissionName)
            ->exists();

        return $exists;
    }

    public function unidade()
    {
        return $this->belongsTo(Unidades::class, 'id_unidade_fk');
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class, 'id_usuario_fk');
    }

    public static function dtColumns(): array
    {
        $t = (new static)->getTable();
        return [
            'id' => ['db' => "{$t}.id", 'label' => '#', 'order' => true, 'search' => false],
            'c1' => ['db' => "{$t}.name", 'label' => 'Nome', 'order' => true, 'search' => true],
            'c2' => ['db' => "{$t}.email", 'label' => 'E-mail', 'order' => true, 'search' => true],
            'st' => ['db' => "{$t}.status", 'label' => 'Status', 'order' => true, 'search' => false],
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
                    "{$t}.email",
                ],
            ],
            'status' => [
                'type' => 'select',
                'column' => "{$t}.status",
                'cast' => 'int',
                'operator' => '=',
                'nullable' => true,
            ],
        ];
    }
}
