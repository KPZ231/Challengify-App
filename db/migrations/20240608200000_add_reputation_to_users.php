<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddReputationToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('reputation', 'integer', ['default' => 0])
              ->update();
    }
} 
 