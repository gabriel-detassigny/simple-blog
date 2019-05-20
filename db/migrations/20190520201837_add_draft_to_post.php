<?php


use Phinx\Migration\AbstractMigration;

class AddDraftToPost extends AbstractMigration
{
    public function change()
    {
        $this->table('posts')
            ->addColumn('state', 'string', ['default' => 'published', 'null' => false])
            ->update();
    }
}
