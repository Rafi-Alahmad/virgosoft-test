<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "name" => "admin",
                "email" => env('ADMIN_EMAIL', 'admin@admin.com'),
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make(env('ADMIN_AUTH', '12345678')),
            ]
        ];

        foreach ($users as $user) {
            $user = User::firstOrCreate(["email" => $user['email'],], [
                "name" => $user['name'],
                "email_verified_at" => $user['email_verified_at'],
                "password" => $user['password'],
            ]);
        }
    }
}
