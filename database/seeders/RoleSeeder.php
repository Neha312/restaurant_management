<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = new Role();
        $role_admin->name = 'Admin';
        $role_admin->save();

        $role_chef = new Role();
        $role_chef->name = 'Chef';
        $role_chef->save();

        $role_owner = new Role();
        $role_owner->name = 'Owner';
        $role_owner->save();

        $role_waiter = new Role();
        $role_waiter->name = 'Waiter';
        $role_waiter->save();

        $role_user = new Role();
        $role_user->name = 'User';
        $role_user->save();

        $role_cashier = new Role();
        $role_cashier->name = 'Cashier';
        $role_cashier->save();
    }
}
