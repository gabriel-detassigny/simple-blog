<?php

use Phinx\Migration\AbstractMigration;

class AuthorExternalLinksTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('author_external_links')) {
            $this->table('author_external_links')
                ->addColumn('author_id', 'integer')
                ->addForeignKey('author_id', 'authors')
                ->addColumn('external_link_id', 'integer')
                ->addForeignKey('external_link_id', 'external_links')
                ->addIndex(['external_link_id'], ['unique' => true])
                ->create();
        }
    }

    public function down()
    {
        $this->table('author_external_links')->drop()->save();
    }
}
