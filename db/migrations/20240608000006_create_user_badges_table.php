<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserBadgesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user_badges', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('user_id', 'uuid')
              ->addColumn('badge_id', 'uuid')
              ->addColumn('awarded_at', 'datetime')
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addForeignKey('badge_id', 'badges', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addIndex(['user_id', 'badge_id'], ['unique' => true])
              ->create();
    }
} 