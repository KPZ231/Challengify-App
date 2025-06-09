<?php

use Phinx\Migration\AbstractMigration;

class AddUserProfileFields extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        
        // Add username_changes column with default value of 0
        if (!$table->hasColumn('username_changes')) {
            $table->addColumn('username_changes', 'integer', [
                'default' => 0,
                'limit' => 11,
                'null' => false,
                'comment' => 'Number of times user has changed their username'
            ]);
        }
        
        $table->save();
    }
} 