<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        Role::findOrCreate('staff', 'web');

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@smartlinesa.net'],
            [
                'name' => 'Smart Line Admin',
                'password' => Hash::make('SmartLine@2026'),
                'phone' => '+966592458908',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
    }
}
