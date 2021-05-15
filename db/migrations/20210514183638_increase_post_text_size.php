<?php

use Phinx\Migration\AbstractMigration;

class IncreasePostTextSize extends AbstractMigration
{
    public function up()
    {
        $this->table('posts')
            ->changeColumn('text', 'text', ['limit' => 65535])
            ->save();
    }

    public function down()
    {
        $this->table('posts')
            ->changeColumn('text', 'text', ['limit' => 2000])
            ->save();
    }
}
