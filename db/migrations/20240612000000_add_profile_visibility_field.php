<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProfileVisibilityField extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        
        // Check if the column already exists to avoid errors
        if (!$table->hasColumn('profile_visibility')) {
            $table->addColumn('profile_visibility', 'enum', [
                'values' => ['public', 'followers', 'private'], 
                'default' => 'public'
            ])
            ->save();
        }
    }
} 