<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AuthorTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('authors')) {
            $this->table('authors')
                ->addColumn('name', 'string', ['limit' => 50])
                ->create();
        }
    }

    public function down()
    {
        $this->table('authors')->drop()->save();
    }
}
