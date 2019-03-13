<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class PostTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('posts')) {
            $this->table('posts')
                ->addColumn('text', 'text', ['limit' => 2000])
                ->addColumn('title', 'string', ['limit' => 50])
                ->addColumn('subtitle', 'string', ['limit' => 150])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('author_id', 'integer', ['null' => true])
                ->addForeignKey('author_id', 'authors')
                ->addIndex(['title'], ['name' => 'title'])
                ->create();
        }
    }

    public function down()
    {
        $this->table('posts')->drop()->save();
    }
}
