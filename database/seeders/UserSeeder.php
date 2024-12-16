<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('app/Portal.csv');

        if (!file_exists($filePath)) {
            echo "File not found: {$filePath}\n";
            return;
        }

        $faker = Faker::create();
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);

        DB::table('users')->insert([
            'name' => 'Mohamed Youssef',
            'email' => 'mydev@my-dev.pro',
            'mobile' => '201022366794',
            'role' => 'admin',
            'password' => Hash::make('823w&%9^N^*fD45I*5pc'),
        ]);

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);

            DB::table('users')->insert([
                'name' => $data['Physician'] ?? $faker->name,
                'email' => $data['email'] ?? $faker->unique()->safeEmail,
                'mobile' => $data['Phone Number'] ?? $faker->phoneNumber,
                'role' => $data['role'] ?? 'doctor',
                'password' => Hash::make($data['password'] ?? $faker->password),
            ]);
        }

        fclose($handle);
    }
}
