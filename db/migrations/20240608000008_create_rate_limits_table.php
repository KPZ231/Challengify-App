<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRateLimitsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('rate_limits');
        $table->addColumn('key', 'string', ['limit' => 100])
              ->addColumn('ip', 'string', ['limit' => 45])
              ->addColumn('attempts', 'integer', ['default' => 0])
              ->addColumn('last_attempt', 'datetime')
              ->addIndex(['key', 'ip'], ['unique' => true])
              ->create();
    }
} 