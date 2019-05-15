<?php


use Phinx\Seed\AbstractSeed;

class CommentSeeder extends AbstractSeed
{
    public const COMMENTS_COUNT = 50;

    public function getDependencies()
    {
        return ['PostSeeder'];
    }

    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [];

        for ($i = 0; $i < self::COMMENTS_COUNT; $i++) {
            $data[] = [
                'name' => $faker->userName,
                'text' => $faker->sentence,
                'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                'isAdmin' => rand(1, 10) == 1 ? 1 : 0,
                'post_id' => rand(1, PostSeeder::POSTS_COUNT)
            ];
        }

        $this->table('comments')
            ->insert($data)
            ->save();
    }
}
