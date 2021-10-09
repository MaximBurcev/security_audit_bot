<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $model)
    {
        $tableName = $model->getTable();
        DB::table( $tableName )->delete();
        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");

        $password = Hash::make('toptal');

        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.demo',
            'email_verified_at' => now(),
            'password' => $password,
            'role_id' => random_int(1,5),
            'level' => $model::LEVEL_ADMIN
        ]);

        $model::factory(10)->create();
    }
}
