<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoriesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('categories', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('name', 'string', ['limit' => 50])
              ->addColumn('description', 'text', ['null' => true])
              ->addColumn('slug', 'string', ['limit' => 100])
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addIndex(['name'], ['unique' => true])
              ->addIndex(['slug'], ['unique' => true])
              ->create();
    }
} 