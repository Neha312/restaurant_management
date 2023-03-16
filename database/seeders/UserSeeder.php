<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        User::create([
            'role_id'     => 1,
            'first_name'  => 'Admin',
            'last_name'   => 'Admin',
            'email'       => 'admin@gmail.com',
            'password'    => bcrypt('12345678'),
            'address1'    => 'Surat',
            'address2'    => 'Bharuch',
            'zip_code'    => 341273,
            'phone'       => 8975452756,
            'total_leave' => 15,
            'used_leave'  => 0,
            'joining_date' => '2023-01-02',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now()
        ]);
    }
}
