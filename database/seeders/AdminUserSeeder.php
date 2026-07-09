<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = config('cinema.admin.name');
        $email = config('cinema.admin.email');
        $password = config('cinema.admin.password');

        if (empty($email) || empty($password)) {
            throw new RuntimeException('Укажите ADMIN_EMAIL и ADMIN_PASSWORD в файле .env.');
        }

        /*
         * updateOrCreate:
         *
         * 1. Ищет пользователя по email.
         * 2. Если пользователь найден — обновляет его.
         * 3. Если не найден — создаёт нового.
         *
         * Это позволяет запускать сидер повторно,
         * не создавая одинаковых пользователей.
         */
        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password)
            ]
        );
    }
}
