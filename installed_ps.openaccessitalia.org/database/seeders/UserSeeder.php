<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salt = hash('sha512', uniqid(random_int(1, mt_getrandmax()), true));
        User::factory()->create([
            'name' => 'admin',
            'friendly_name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make(hash('sha512', hash('sha512', 'openaccessitalia').$salt)),
            'salt' => $salt,
            'admin' => true,
            'piracy' => true,
            'cncpo' => true,
            'adm' => true,
            'manual' => true,
            'enabled' => true,
            'avatar' => null,
        ]);
    }
}
