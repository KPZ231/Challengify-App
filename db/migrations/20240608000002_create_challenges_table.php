<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateChallengesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('challenges', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('user_id', 'uuid')
              ->addColumn('category_id', 'uuid')
              ->addColumn('title', 'string', ['limit' => 100])
              ->addColumn('description', 'text')
              ->addColumn('difficulty', 'enum', ['values' => ['easy', 'medium', 'hard'], 'default' => 'medium'])
              ->addColumn('rules', 'text', ['null' => true])
              ->addColumn('submission_guidelines', 'text', ['null' => true])
              ->addColumn('start_date', 'datetime')
              ->addColumn('end_date', 'datetime')
              ->addColumn('status', 'enum', ['values' => ['draft', 'active', 'completed', 'cancelled'], 'default' => 'draft'])
              ->addColumn('image', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addForeignKey('category_id', 'categories', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addIndex(['title'], ['unique' => true])
              ->create();
    }
} 