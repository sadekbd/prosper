<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Avoid duplicate seeding
        if (DB::table('admin_users')->where('username', 'superadmin')->exists()) {
            $this->command->info('Super admin already exists. Skipping.');
            return;
        }

        DB::table('admin_users')->insert([
            'username'   => 'superadmin',
            'full_name'  => 'Prosper Media Admin',
            'email'      => 'admin@prospermedia.com',
            'mobile'     => '+8801700000000',
            'password'   => Hash::make('Admin@1234'),   // CHANGE THIS after first login
            'role'       => 'super_admin',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Super admin created. Email: admin@prospermedia.com | Password: Admin@1234');
    }
}