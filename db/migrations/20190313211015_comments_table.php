<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CommentsTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('comments')) {
            $this->table('comments')
                ->addColumn('name', 'string', ['limit' => 50])
                ->addColumn('text', 'text', ['limit' => 500])
                ->addColumn('isAdmin', 'boolean')
                ->addColumn('created_at', 'datetime')
                ->addColumn('post_id', 'integer', ['null' => true])
                ->addForeignKey('post_id', 'posts')
                ->create();
        }
    }

    public function down()
    {
        $this->table('comments')->drop()->save();
    }
}
