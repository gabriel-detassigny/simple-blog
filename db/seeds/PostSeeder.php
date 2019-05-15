<?php


use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    public const POSTS_COUNT = 30;

    public function getDependencies()
    {
        return ['AuthorSeeder'];
    }

    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [];

        for ($i = 0; $i < self::POSTS_COUNT; $i++) {
            $data[] = [
                'text' => $faker->text,
                'title' => $faker->word,
                'subtitle' => $faker->sentence,
                'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                'author_id' => rand(1, AuthorSeeder::AUTHORS_COUNT)
            ];
        }

        $this->table('posts')
            ->insert($data)
            ->save();
    }
}
