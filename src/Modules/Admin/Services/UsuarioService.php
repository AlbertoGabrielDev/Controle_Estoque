<?php

namespace Modules\Admin\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Models\User;

class UsuarioService
{
    public function create(array $data, ?UploadedFile $photo = null): User
    {
        $imageName = $this->storeProfilePhoto($photo);

        $usuario = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make((string) $data['password']),
            'id_unidade_fk' => $data['id_unidade'],
            'profile_photo_path' => $imageName,
        ]);

        $usuario->roles()->sync($data['roles'] ?? []);

        return $usuario;
    }

    public function update(User $usuario, array $data, ?UploadedFile $photo = null): User
    {
        $imageName = $this->storeProfilePhoto($photo);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'id_unidade_fk' => $data['id_unidade'],
        ];

        if ($imageName !== null) {
            $updateData['profile_photo_path'] = $imageName;
        }

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make((string) $data['password']);
        }

        $usuario->update($updateData);
        $usuario->roles()->sync($data['roles'] ?? []);

        return $usuario;
    }

    public function resolveAvatarPath(?string $avatar): string
    {
        if (!$avatar) {
            return asset('img/default-avatar.png');
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://') || str_starts_with($avatar, '/')) {
            return $avatar;
        }

        return asset('img/usuario/' . ltrim($avatar, '/'));
    }

    private function storeProfilePhoto(?UploadedFile $photo): ?string
    {
        if (!$photo || !$photo->isValid()) {
            return null;
        }

        $extension = $photo->extension();
        $imageName = md5($photo->getClientOriginalName() . strtotime('now')) . '.' . $extension;
        $photo->move(public_path('img/usuario'), $imageName);

        return $imageName;
    }
}
