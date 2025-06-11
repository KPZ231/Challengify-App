<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserFollowersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user_followers', ['id' => false, 'primary_key' => ['follower_id', 'following_id']]);
        $table->addColumn('follower_id', 'uuid')
              ->addColumn('following_id', 'uuid')
              ->addColumn('created_at', 'datetime')
              ->addForeignKey('follower_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addForeignKey('following_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->create();
    }
} 