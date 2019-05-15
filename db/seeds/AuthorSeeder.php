<?php

use Phinx\Seed\AbstractSeed;

class AuthorSeeder extends AbstractSeed
{
    public const AUTHORS_COUNT = 3;

    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [];
        for ($i = 0; $i < self::AUTHORS_COUNT; $i++) {
            $data[] = ['name' => $faker->name];
        }

        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        $this->table('comments')->truncate();
        $this->table('posts')->truncate();
        $this->table('authors')->truncate();

        $this->table('authors')->insert($data)
            ->save();
    }
}
