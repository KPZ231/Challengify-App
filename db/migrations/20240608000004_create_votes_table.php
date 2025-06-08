<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateVotesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('votes', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('user_id', 'uuid')
              ->addColumn('submission_id', 'uuid')
              ->addColumn('vote_type', 'enum', ['values' => ['upvote', 'downvote']])
              ->addColumn('comment', 'text', ['null' => true])
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addForeignKey('submission_id', 'submissions', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addIndex(['user_id', 'submission_id'], ['unique' => true])
              ->create();
    }
} 