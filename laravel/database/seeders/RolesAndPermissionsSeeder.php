<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $viewAssets = Permission::firstOrCreate(['name' => 'view_assets']);
        $editAssets = Permission::firstOrCreate(['name' => 'edit_assets']);

        // Create role
        $manager = Role::firstOrCreate(['name' => 'Manager','level' => 1]);
        $manager->permissions()->sync([$viewAssets->id, $editAssets->id]);

        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Master Admin',
                'password' => Hash::make('password')
            ]
        );
        // Assign role to user
        $user->roles()->sync([$manager->id]);
    }
}
