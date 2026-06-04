<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class FormateurService extends BaseService
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get all formateurs.
     * 
     * @return Collection
     */
    public function getAllWithUsers(): Collection
    {
        return $this->model->role('formateur')->orderBy('id', 'desc')->get();
    }

    /**
     * Create a user with formateur role.
     * 
     * @param array $data
     * @return User
     */
    public function createFormateur(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'nom' => $data['nom'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Assign formateur role automatically to ensure they can login
            $user->syncRoles(['formateur']);

            return $user;
        });
    }

    /**
     * Update user details.
     * 
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateFormateur(int $id, array $data): User
    {
        $user = $this->findOrFail($id);

        return DB::transaction(function () use ($user, $data) {
            $userData = [
                'nom' => $data['nom'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $user->update($userData);

            return $user;
        });
    }

    /**
     * Delete user.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteFormateur(int $id): bool
    {
        $user = $this->findOrFail($id);

        return DB::transaction(function () use ($user) {
            $user->delete();
            return true;
        });
    }
}
