<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'  => 'Super Admin',
            'mobile' => 1234567890,
            'email' => 'admin@admin.com',
            'password' => bcrypt('secret')
        ]);

        
    }
}
