<?php

use Phinx\Seed\AbstractSeed;

class BlogInfoSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [
            ['info_key' => 'blog_title', 'info_value' => $faker->word],
            ['info_key' => 'blog_description', 'info_value' => $faker->sentence],
            ['info_key' => 'about_text', 'info_value' => $faker->text],
        ];

        $blogInfo = $this->table('blog_infos');

        $blogInfo->truncate();
        $blogInfo->insert($data)
            ->save();
    }
}
