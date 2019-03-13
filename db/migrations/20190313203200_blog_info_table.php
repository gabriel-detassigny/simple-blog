<?php


use Phinx\Migration\AbstractMigration;

class BlogInfoTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('blog_infos')) {
            $blogInfos = $this->table('blog_infos');
            $blogInfos->addColumn('info_key', 'string', ['limit' => 20])
                ->addColumn('info_value', 'string', ['limit' => 200])
                ->addIndex(['info_key'], ['name' => 'info_key'])
                ->create();
        }
    }

    public function down()
    {
        $this->table('blog_infos')->drop()->save();
    }
}
