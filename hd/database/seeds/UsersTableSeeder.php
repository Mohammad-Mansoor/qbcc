<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'QBCC',
            'last_name' => 'QBCC',
            'role' => 'SP',
            'email' => 'qbc1@live.com',
            'password' => bcrypt('Qbcc22000@1500af'),
        ]);
    }
}
