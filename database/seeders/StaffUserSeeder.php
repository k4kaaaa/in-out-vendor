<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StaffUserSeeder extends Seeder
{
    /**
     * Seed the staff (petugas gudang) user.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'budi.gudang@inoutvendor.local',
        ], [
            'name'     => 'Budi Gudang',
            'password' => bcrypt('gudang123'),
            'role'     => 'staff',
        ]);
    }
}
