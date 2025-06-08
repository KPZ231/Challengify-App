<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBadgesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('badges', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('name', 'string', ['limit' => 50])
              ->addColumn('description', 'text')
              ->addColumn('image', 'string', ['limit' => 255])
              ->addColumn('criteria', 'text')
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addIndex(['name'], ['unique' => true])
              ->create();
    }
} 