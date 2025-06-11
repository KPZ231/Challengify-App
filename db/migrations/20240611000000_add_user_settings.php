<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserSettings extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        
        // Notification settings
        $table->addColumn('notification_email', 'boolean', ['default' => true])
              ->addColumn('notification_push', 'boolean', ['default' => false])
              ->addColumn('notification_sms', 'boolean', ['default' => false])
              ->addColumn('notification_time', 'string', ['default' => '18:00', 'limit' => 5])
              ->addColumn('weekly_summary', 'boolean', ['default' => true])
              ->addColumn('monthly_summary', 'boolean', ['default' => false])
              
              // Privacy settings
              ->addColumn('profile_visibility', 'enum', ['values' => ['public', 'followers', 'private'], 'default' => 'public'])
              ->addColumn('messaging_permission', 'enum', ['values' => ['all', 'followers', 'none'], 'default' => 'all'])
              
              // Language and timezone settings
              ->addColumn('language', 'string', ['default' => 'en', 'limit' => 5])
              ->addColumn('timezone', 'string', ['default' => 'UTC', 'limit' => 64])
              ->addColumn('auto_timezone', 'boolean', ['default' => true])
              
              ->save();
    }
} 