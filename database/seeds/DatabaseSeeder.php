<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $userSeedData = [
            [
                'name' => "Lucas Junior Dias de Paula",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password')
            ],
            [
                'name' => "Marcus Pereira",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password')
            ],
            [
                'name' => "Eduardo César Silva Melo",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password')
            ],
            [
                'name' => "Daniel Capanema",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password')
            ],
            [
                'name' => "Luciana Mara Freitas",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password')
            ]
        ];

        DB::table('users')->insert($userSeedData);

        $eventoData = [
            ['nome' => 'Encontro Gamer De Pará De Minas'],
            ['nome' => 'FINECOM'],
            ['nome' => 'Startup Weekend'],
            ['nome' => 'Semana de Estudos Jurídicos'],
        ];

        DB::table('evento')->insert($eventoData);

    }
}
