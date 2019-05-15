<?php


use Phinx\Seed\AbstractSeed;

class ExternalLinkSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [
            ['name' => $faker->word, 'url' => $faker->url],
            ['name' => $faker->word, 'url' => $faker->url],
            ['name' => $faker->word, 'url' => $faker->url]
        ];

        $externalLink = $this->table('external_links');

        $externalLink->truncate();
        $externalLink->insert($data)
            ->save();
    }
}
