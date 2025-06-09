<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateContactsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('contacts', ['id' => false, 'primary_key' => 'id']);
        $table->addColumn('id', 'uuid')
              ->addColumn('name', 'string', ['limit' => 100])
              ->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('subject', 'string', ['limit' => 200])
              ->addColumn('message', 'text')
              ->addColumn('ip', 'string', ['limit' => 45])  // IPv6 addresses can be up to 45 chars
              ->addColumn('is_read', 'boolean', ['default' => false])
              ->addColumn('created_at', 'datetime')
              ->addIndex(['created_at'])
              ->create();
    }
} 