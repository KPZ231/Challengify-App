<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Medoo\Medoo;

class RateLimiterService
{
    private Medoo $db;
    private int $maxAttempts;
    private int $decayMinutes;

    public function __construct(Medoo $db, int $maxAttempts = 5, int $decayMinutes = 1)
    {
        $this->db = $db;
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    /**
     * Check if the user has exceeded the rate limit
     */
    public function tooManyAttempts(string $key, string $ip): bool
    {
        // Check if the rate limit record exists
        $record = $this->db->get('rate_limits', '*', [
            'key' => $key,
            'ip' => $ip
        ]);

        if (!$record) {
            // Create new record if it doesn't exist
            $this->db->insert('rate_limits', [
                'key' => $key,
                'ip' => $ip,
                'attempts' => 1,
                'last_attempt' => date('Y-m-d H:i:s')
            ]);
            return false;
        }

        // Check if the decay time has passed
        $lastAttempt = new \DateTime($record['last_attempt']);
        $now = new \DateTime();
        $diff = $now->diff($lastAttempt);
        $minutesPassed = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

        if ($minutesPassed >= $this->decayMinutes) {
            // Reset attempts if decay time has passed
            $this->db->update('rate_limits', [
                'attempts' => 1,
                'last_attempt' => date('Y-m-d H:i:s')
            ], [
                'key' => $key,
                'ip' => $ip
            ]);
            return false;
        }

        // Increment attempts
        $this->db->update('rate_limits', [
            'attempts' => $record['attempts'] + 1,
            'last_attempt' => date('Y-m-d H:i:s')
        ], [
            'key' => $key,
            'ip' => $ip
        ]);

        // Check if attempts exceed max attempts
        return ($record['attempts'] + 1) > $this->maxAttempts;
    }

    /**
     * Get the number of attempts left
     */
    public function retriesLeft(string $key, string $ip): int
    {
        $record = $this->db->get('rate_limits', '*', [
            'key' => $key,
            'ip' => $ip
        ]);

        if (!$record) {
            return $this->maxAttempts;
        }

        // Check if the decay time has passed
        $lastAttempt = new \DateTime($record['last_attempt']);
        $now = new \DateTime();
        $diff = $now->diff($lastAttempt);
        $minutesPassed = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

        if ($minutesPassed >= $this->decayMinutes) {
            return $this->maxAttempts;
        }

        return max(0, $this->maxAttempts - $record['attempts']);
    }

    /**
     * Reset the rate limit for a key
     */
    public function resetAttempts(string $key, string $ip): void
    {
        $this->db->update('rate_limits', [
            'attempts' => 0,
            'last_attempt' => date('Y-m-d H:i:s')
        ], [
            'key' => $key,
            'ip' => $ip
        ]);
    }
} 