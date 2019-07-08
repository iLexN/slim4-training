<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        for ($j = 0; $j < 1000; $j++) {
            $data = [];
            for ($i = 0; $i < 100; $i++) {
                $data[] = [
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'count' => 0,
                ];
            }
            $this->insert('users', $data);
        }

    }
}
