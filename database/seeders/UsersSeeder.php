<?php

namespace Database\Seeders;

use App\Models\Asset;
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
                "balance" => 10000,
                'assets' => [
                    [
                        'symbol' => 'BTC',
                        'amount' => 5,
                    ],
                    [
                        'symbol' => 'ETH',
                        'amount' => 10,
                    ],
                ],
            ],
            [
                "name" => "user",
                "email" => 'user@user.com',
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make('12345678'),
                "balance" => 20000,
                'assets' => [
                    [
                        'symbol' => 'BTC',
                        'amount' => 10,
                    ],
                    [
                        'symbol' => 'ETH',
                        'amount' => 20,
                    ],
                ],
            ],
        ];

        foreach ($users as $user) {
            $userModel = User::firstOrCreate(["email" => $user['email'],], [
                "name" => $user['name'],
                "email_verified_at" => $user['email_verified_at'],
                "password" => $user['password'],
                "balance" => $user['balance'],
            ]);

            foreach ($user['assets'] as $asset) {
                Asset::firstOrCreate(['user_id' => $userModel->id, 'symbol' => $asset['symbol']], [
                    'amount' => $asset['amount'],
                    'locked_amount' => 0,
                ]);
            }
        }
    }
}
