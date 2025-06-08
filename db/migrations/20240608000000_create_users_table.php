<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('username', 'string', ['limit' => 50])
              ->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('password', 'string', ['limit' => 255])
              ->addColumn('first_name', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('last_name', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('bio', 'text', ['null' => true])
              ->addColumn('role', 'enum', ['values' => ['user', 'admin'], 'default' => 'user'])
              ->addColumn('email_verified_at', 'datetime', ['null' => true])
              ->addColumn('remember_token', 'string', ['limit' => 100, 'null' => true])
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addIndex(['email'], ['unique' => true])
              ->addIndex(['username'], ['unique' => true])
              ->create();
    }
} 