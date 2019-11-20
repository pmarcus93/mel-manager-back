<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

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
                'telefone' => '123456789',
                'created_at' => Carbon::now()
            ],
            [
                'name' => "Marcus Pereira",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789',
                'created_at' => Carbon::now()
            ],
            [
                'name' => "Eduardo César Silva Melo",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789',
                'created_at' => Carbon::now()
            ],
            [
                'name' => "Daniel Capanema",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789',
                'created_at' => Carbon::now()
            ],
            [
                'name' => "Luciana Mara Freitas",
                'email' => Str::random(10) . '@gmail.com',
                'password' => bcrypt('password'),
                'telefone' => '123456789',
                'created_at' => Carbon::now()
            ]
        ];

        DB::table('users')->insert($userSeedData);

        $categoriaData = [
            ['nome' => 'Serviços', 'created_at' => Carbon::now()],
            ['nome' => 'Materiais de escritório', 'created_at' => Carbon::now()],
            ['nome' => 'Alimentação', 'created_at' => Carbon::now()],
            ['nome' => 'Materiais de limpeza', 'created_at' => Carbon::now()],
            ['nome' => 'Locação de equipamentos', 'created_at' => Carbon::now()],
            ['nome' => 'Patrocínio', 'created_at' => Carbon::now()]
        ];

        DB::table('categoria')->insert($categoriaData);

        $eventoData = [
            ['nome' => 'Encontro Gamer De Pará De Minas', 'created_at' => Carbon::now()],
            ['nome' => 'FINECOM', 'created_at' => Carbon::now()],
            ['nome' => 'Startup Weekend', 'created_at' => Carbon::now()],
            ['nome' => 'Semana de Estudos Jurídicos', 'created_at' => Carbon::now()],
        ];

        DB::table('evento')->insert($eventoData);

        $empresaData = [
            ['nome' => 'Empresa Teste 01', 'evento_id' =>  1, 'telefone' => rand(10000000000, 99999999999), 'created_at' => Carbon::now()],
            ['nome' => 'Empresa Teste 02', 'evento_id' =>  1, 'telefone' => rand(10000000000, 99999999999), 'created_at' => Carbon::now()],
            ['nome' => 'Empresa Teste 03', 'evento_id' =>  1, 'telefone' => rand(10000000000, 99999999999), 'created_at' => Carbon::now()],
            ['nome' => 'Empresa Teste 04', 'evento_id' =>  1, 'telefone' => rand(10000000000, 99999999999), 'created_at' => Carbon::now()],
            ['nome' => 'Empresa Teste 05', 'evento_id' =>  1, 'telefone' => rand(10000000000, 99999999999), 'created_at' => Carbon::now()]
        ];

        DB::table('empresa')->insert($empresaData);

        $eventoEdicaoData = [
            ['evento_id' => 1, 'nome' => '3º Encontro Gamer de Pará de Minas', 'created_at' => Carbon::now()],
            ['evento_id' => 2, 'nome' => 'FINECOM 2019', 'created_at' => Carbon::now()],
            ['evento_id' => 2, 'nome' => 'FINECOM 2020', 'created_at' => Carbon::now()],
            ['evento_id' => 2, 'nome' => 'FINECOM 2021', 'created_at' => Carbon::now()],
            ['evento_id' => 3, 'nome' => 'Startup Weekend 2020', 'created_at' => Carbon::now()],
            ['evento_id' => 3, 'nome' => 'Startup Weekend 2021', 'created_at' => Carbon::now()],
            ['evento_id' => 4, 'nome' => '61ª Semana de Estudos Jurídicos', 'created_at' => Carbon::now()],
            ['evento_id' => 4, 'nome' => '62ª Semana de Estudos Jurídicos', 'created_at' => Carbon::now()],
            ['evento_id' => 4, 'nome' => '63ª Semana de Estudos Jurídicos', 'created_at' => Carbon::now()],
            ['evento_id' => 1, 'nome' => '4º Encontro Gamer De Pará de Minas', 'created_at' => Carbon::now()],

        ];

        DB::table('evento_edicao')->insert($eventoEdicaoData);

        $eventoAdministradorData = [
            ['evento_id' => 1, 'user_id' => 1, 'created_at' => Carbon::now()],
            ['evento_id' => 1, 'user_id' => 2, 'created_at' => Carbon::now()],
            ['evento_id' => 1, 'user_id' => 3, 'created_at' => Carbon::now()],
            ['evento_id' => 1, 'user_id' => 4, 'created_at' => Carbon::now()],
            ['evento_id' => 1, 'user_id' => 5, 'created_at' => Carbon::now()],
            ['evento_id' => 2, 'user_id' => 4, 'created_at' => Carbon::now()],
            ['evento_id' => 2, 'user_id' => 5, 'created_at' => Carbon::now()],
            ['evento_id' => 3, 'user_id' => 4, 'created_at' => Carbon::now()]
        ];

        DB::table('evento_administrador')->insert($eventoAdministradorData);

    }
}
