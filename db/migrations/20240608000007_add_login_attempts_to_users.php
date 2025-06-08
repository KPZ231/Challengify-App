<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddLoginAttemptsToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('login_attempts', 'integer', ['default' => 0])
              ->addColumn('last_attempt_time', 'datetime', ['null' => true])
              ->update();
    }
} 