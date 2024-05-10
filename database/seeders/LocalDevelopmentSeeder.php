<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use AidingApp\Authorization\Models\Role;

class LocalDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isLocal()) {
            $this->internalAdminUsers();
        }
    }

    private function internalAdminUsers(): void
    {
        $superAdminRole = Role::where('name', 'authorization.super_admin')->first();

        if (! $superAdminRole) {
            return;
        }

        collect(config('local_development.internal_users.emails'))->each(function ($email) use ($superAdminRole) {
            $user = User::where('email', $email)->first();

            if (is_null($user)) {
                $user = User::factory()->create([
                    'name' => Str::title(Str::replace('.', ' ', Str::before($email, '@'))),
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'is_external' => true,
                ]);
            }

            $user->roles()->sync($superAdminRole);
        });
    }
}
