<?php

namespace App\Services;

use App\Models\Formateur;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class FormateurService extends BaseService
{
    public function __construct(Formateur $model)
    {
        $this->model = $model;
    }

    /**
     * Get all formateurs with user info.
     * 
     * @return Collection
     */
    public function getAllWithUsers(): Collection
    {
        return $this->model->with('user')->orderBy('id', 'desc')->get();
    }

    /**
     * Create user and associated formateur.
     * 
     * @param array $data
     * @return Formateur
     */
    public function createFormateur(array $data): Formateur
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'nom' => $data['nom'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Assign formateur role automatically to ensure they can login
            $user->syncRoles(['formateur']);

            return $this->model->create([
                'user_id' => $user->id,
                'codeInterne' => $data['codeInterne'],
                'specialite' => $data['specialite'],
            ]);
        });
    }

    /**
     * Update user and formateur details.
     * 
     * @param int $id
     * @param array $data
     * @return Formateur
     */
    public function updateFormateur(int $id, array $data): Formateur
    {
        $formateur = $this->findOrFail($id);

        return DB::transaction(function () use ($formateur, $data) {
            $userData = [
                'nom' => $data['nom'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $formateur->user->update($userData);

            $formateur->update([
                'codeInterne' => $data['codeInterne'],
                'specialite' => $data['specialite'],
            ]);

            return $formateur;
        });
    }

    /**
     * Delete formateur and associated user.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteFormateur(int $id): bool
    {
        $formateur = $this->findOrFail($id);

        return DB::transaction(function () use ($formateur) {
            $user = $formateur->user;
            $formateur->delete();
            if ($user) {
                $user->delete();
            }
            return true;
        });
    }
}
