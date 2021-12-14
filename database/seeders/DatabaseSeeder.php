<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "lucas";
        $user->puesto = "directivo";
        $contrasena = "1234";
        $user->password = Hash::make($contrasena);
        $user->email = "lucas@gmail.com";
        $user->salario = 2;
        $user->biografia = "sisisis";
        $user->save();
    }
}
