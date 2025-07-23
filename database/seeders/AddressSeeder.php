<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user
        $user = User::first();
        
        if ($user) {
            Address::create([
                'user_id' => $user->id,
                'name' => 'John Doe',
                'phone' => '081234567890',
                'address' => 'Jl. Contoh No. 123, RT 001/RW 002',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12345',
                'is_primary' => true
            ]);

            Address::create([
                'user_id' => $user->id,
                'name' => 'Jane Doe',
                'phone' => '081234567891',
                'address' => 'Jl. Sample No. 456, RT 003/RW 004',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '54321',
                'is_primary' => false
            ]);
        }
    }
}
