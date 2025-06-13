<?php

namespace Kpzsproductions\Challengify\Services;

use Medoo\Medoo;

class ReportService
{
    private $db;

    public function __construct(Medoo $db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            // Get the database instance from the global container
            global $container;
            $this->db = $container->get(Medoo::class);
        }
    }

    public function getReportData($userId, $period = 'today', $customDate = null)
    {
        $data = [
            'completed_challenges' => 0,
            'likes_received' => 0,
            'new_followers' => 0
        ];
        
        // Build date range based on period
        $dateRange = $this->getDateRange($period, $customDate);
        
        // Get completed challenges count
        $data['completed_challenges'] = $this->getCompletedChallengesCount($userId, $dateRange);
        
        // Get likes received count
        $data['likes_received'] = $this->getLikesReceivedCount($userId, $dateRange);
        
        // Get new followers count
        $data['new_followers'] = $this->getNewFollowersCount($userId, $dateRange);
        
        return $data;
    }
    
    private function getDateRange($period, $customDate = null)
    {
        $today = date('Y-m-d');
        $startDate = $today;
        $endDate = $today;
        
        switch ($period) {
            case 'year':
                $startDate = date('Y-01-01');
                break;
            case 'month':
                $startDate = date('Y-m-01');
                break;
            case 'week':
                $startDate = date('Y-m-d', strtotime('monday this week'));
                break;
            case 'custom':
                if ($customDate) {
                    $startDate = date('Y-m-d', strtotime($customDate));
                    $endDate = date('Y-m-d', strtotime($customDate));
                }
                break;
            // Default is 'today', which is already set
        }
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
    
    private function getCompletedChallengesCount($userId, $dateRange)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM challenge_submissions 
                WHERE user_id = ? 
                AND status = 'completed' 
                AND completed_at BETWEEN ? AND ? 23:59:59";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $dateRange['start_date'], $dateRange['end_date']]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    private function getLikesReceivedCount($userId, $dateRange)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM likes 
                WHERE content_user_id = ? 
                AND created_at BETWEEN ? AND ? 23:59:59";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $dateRange['start_date'], $dateRange['end_date']]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    private function getNewFollowersCount($userId, $dateRange)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM followers 
                WHERE followed_user_id = ? 
                AND created_at BETWEEN ? AND ? 23:59:59";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $dateRange['start_date'], $dateRange['end_date']]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
} 