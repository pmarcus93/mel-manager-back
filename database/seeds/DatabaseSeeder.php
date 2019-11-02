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

        $userSeedData = [
            [
                'name' => "Lucas Junior Dias de Paula",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789'
            ],
            [
                'name' => "Marcus Pereira",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789'
            ],
            [
                'name' => "Eduardo César Silva Melo",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789'
            ],
            [
                'name' => "Daniel Capanema",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789'
            ],
            [
                'name' => "Luciana Mara Freitas",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789'
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

        $eventoEdicaoData = [
            ['evento_id' => 1, 'nome' => '3º Encontro Gamer de Pará de Minas'],
            ['evento_id' => 2, 'nome' => 'FINECOM 2019'],
            ['evento_id' => 2, 'nome' => 'FINECOM 2020'],
            ['evento_id' => 2, 'nome' => 'FINECOM 2021'],
            ['evento_id' => 3, 'nome' => 'Startup Weekend 2020'],
            ['evento_id' => 3, 'nome' => 'Startup Weekend 2021'],
            ['evento_id' => 4, 'nome' => '61ª Semana de Estudos Jurídicos'],
            ['evento_id' => 4, 'nome' => '62ª Semana de Estudos Jurídicos'],
            ['evento_id' => 4, 'nome' => '63ª Semana de Estudos Jurídicos'],
            ['evento_id' => 1, 'nome' => '4º Encontro Gamer De Pará de Minas'],

        ];

        DB::table('evento_edicao')->insert($eventoEdicaoData);

        $eventoAdministradorData = [
            ['evento_id' => 1, 'user_id' => 1],
            ['evento_id' => 1, 'user_id' => 2],
            ['evento_id' => 1, 'user_id' => 3],
            ['evento_id' => 1, 'user_id' => 4],
            ['evento_id' => 1, 'user_id' => 5],
            ['evento_id' => 2, 'user_id' => 4],
            ['evento_id' => 2, 'user_id' => 5],
            ['evento_id' => 3, 'user_id' => 4]
        ];

        DB::table('evento_administrador')->insert($eventoAdministradorData);
    }
}
