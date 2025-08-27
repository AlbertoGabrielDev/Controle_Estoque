<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

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

    public function roles()
    {
        // padronizado para 'user_role' (ajuste se seu banco usar 'user_roles')
        return $this->belongsToMany(Role::class, 'user_roles');
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

    public function hasPermission($menuSlug, $permissionName)
    {
        return Gate::allows('has-permission', [$menuSlug, $permissionName]);
    }

    public function unidade()
    {
        return $this->belongsTo(Unidades::class, 'id_unidade_fk');
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class, 'id_usuario_fk');
    }
}
