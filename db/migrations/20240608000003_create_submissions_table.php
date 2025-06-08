<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSubmissionsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('submissions', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('user_id', 'uuid')
              ->addColumn('challenge_id', 'uuid')
              ->addColumn('title', 'string', ['limit' => 100])
              ->addColumn('description', 'text')
              ->addColumn('content', 'text')
              ->addColumn('file_path', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('status', 'enum', ['values' => ['draft', 'submitted', 'approved', 'rejected'], 'default' => 'submitted'])
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addForeignKey('challenge_id', 'challenges', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addIndex(['user_id', 'challenge_id'], ['unique' => true])
              ->create();
    }
} 