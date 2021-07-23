<?php

namespace App\Database\Seeds;

use CodeIgniter\I18n\Time;
use CodeIgniter\Database\Seeder;

class OrangSeeder extends Seeder
{
    public function run()
    {
        // $data = [
        //     [
        //         'nama' => 'Rafki Desramadhan',
        //         'alamat'    => 'Jl. Waru Jakarta Timur',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ],
        //     [
        //         'nama' => 'Muhammad Rasyid AA',
        //         'alamat'    => 'Jl. Soreang Bandung',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ],
        //     [
        //         'nama' => 'Makmur Rozak',
        //         'alamat'    => 'Jl. Cilodong Depok',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ]
        // ];

        //MENGGUNAKAN FAKER
        $faker = \Faker\Factory::create('id_ID');
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'nama' => $faker->name,
                'alamat'    => $faker->address,
                'created_at' => Time::createFromTimestamp($faker->unixTime()), //random time
                'updated_at' => Time::now()
            ];
            $this->db->table('orang')->insert($data);
        }

        // Simple Queries
        // $this->db->query("INSERT INTO orang (nama, alamat, created_at, updated_at) VALUES(:nama:, :alamat:,
        // :created_at:, :updated_at:)", $data);

        // Using Query Builder (cara kedua)
        // $this->db->table('orang')->insert($data); //cara insert hanya 1 data
        // $this->db->table('orang')->insertBatch($data);
    }
}
