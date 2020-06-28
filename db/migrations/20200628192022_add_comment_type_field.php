<?php

use Phinx\Migration\AbstractMigration;

class AddCommentTypeField extends AbstractMigration
{
    public function change()
    {
        $this->table('posts')
            ->addColumn('comment_type', 'string', ['default' => 'none', 'null' => false])
            ->update();
    }
}
