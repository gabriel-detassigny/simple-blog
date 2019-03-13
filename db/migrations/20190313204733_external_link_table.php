<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class ExternalLinkTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('external_links')) {
            $this->table('external_links')
                ->addColumn('name', 'string', ['limit' => 50])
                ->addColumn('url', 'string', ['limit' => 200])
                ->create();
        }
    }

    public function down()
    {
        $this->table('external_links')->drop()->save();
    }
}
