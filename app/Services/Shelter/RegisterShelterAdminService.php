<?php

declare(strict_types=1);

namespace App\Services\Shelter;

use App\Enums\Account\Role;
use App\Enums\Shelter\ShelterStatus;
use App\Models\Shelter\Shelter;
use App\Models\Account\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterShelterAdminService
{
    /**
     * Barınak yöneticisi kaydı: user (role=admin) + shelter (status=pending).
     *
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => Role::Admin->value,
            ]);

            Shelter::create([
                'admin_user_id' => $user->id,
                'name' => $data['shelter_name'],
                'license_no' => $data['license_no'],
                'city' => $data['city'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'status' => ShelterStatus::Pending->value,
            ]);

            return $user;
        });
    }
}
