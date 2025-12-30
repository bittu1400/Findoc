<?php

namespace App\Models;

use App\Core\Database;

class Review
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO review (appointment_id, rating, comment) 
                VALUES (:appointment_id, :rating, :comment)";
        
        $params = [
            'appointment_id' => $data['appointment_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null
        ];

        try {
            $this->db->execute($sql, $params);
            return $this->db->lastInsertId();
        } catch (\Exception $e) {
            // Duplicate review or invalid appointment
            throw $e;
        }
    }

    public function getByAppointment($appointmentId)
    {
        $sql = "SELECT r.*, u.name as patient_name
                FROM review r
                INNER JOIN appointment a ON r.appointment_id = a.appointment_id
                INNER JOIN user_entity u ON a.patient_id = u.user_id
                WHERE r.appointment_id = :appointment_id";
        
        return $this->db->fetch($sql, ['appointment_id' => $appointmentId]);
    }

    public function getByDoctor($doctorId, $limit = null)
    {
        $sql = "SELECT r.*, u.name as patient_name, a.appointment_id,
                ts.slot_date
                FROM review r
                INNER JOIN appointment a ON r.appointment_id = a.appointment_id
                INNER JOIN user_entity u ON a.patient_id = u.user_id
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                WHERE a.doctor_id = :doctor_id
                ORDER BY r.review_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        return $this->db->fetchAll($sql, ['doctor_id' => $doctorId]);
    }

    public function getDoctorAverageRating($doctorId)
    {
        $sql = "SELECT 
                COALESCE(AVG(r.rating), 0) as average_rating,
                COUNT(r.review_id) as review_count,
                COUNT(CASE WHEN r.rating = 5 THEN 1 END) as five_star,
                COUNT(CASE WHEN r.rating = 4 THEN 1 END) as four_star,
                COUNT(CASE WHEN r.rating = 3 THEN 1 END) as three_star,
                COUNT(CASE WHEN r.rating = 2 THEN 1 END) as two_star,
                COUNT(CASE WHEN r.rating = 1 THEN 1 END) as one_star
                FROM review r
                INNER JOIN appointment a ON r.appointment_id = a.appointment_id
                WHERE a.doctor_id = :doctor_id";
        
        return $this->db->fetch($sql, ['doctor_id' => $doctorId]);
    }

    public function update($reviewId, $data)
    {
        $sql = "UPDATE review SET rating = :rating, comment = :comment 
                WHERE review_id = :review_id";
        
        return $this->db->execute($sql, [
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'review_id' => $reviewId
        ]) > 0;
    }

    public function delete($reviewId)
    {
        $sql = "DELETE FROM review WHERE review_id = :review_id";
        return $this->db->execute($sql, ['review_id' => $reviewId]) > 0;
    }

    public function getAll($limit = null)
    {
        $sql = "SELECT r.*, 
                u.name as patient_name,
                d.name as doctor_name,
                dp.specialty,
                a.appointment_id
                FROM review r
                INNER JOIN appointment a ON r.appointment_id = a.appointment_id
                INNER JOIN user_entity u ON a.patient_id = u.user_id
                INNER JOIN doctor_profile dp ON a.doctor_id = dp.doctor_id
                INNER JOIN user_entity d ON dp.user_id = d.user_id
                ORDER BY r.review_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        return $this->db->fetchAll($sql);
    }

    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM review";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }

    public function getRecentReviews($limit = 5)
    {
        return $this->getAll($limit);
    }

    public function canUserReview($appointmentId, $userId)
    {
        $sql = "SELECT a.*, r.review_id
                FROM appointment a
                LEFT JOIN review r ON a.appointment_id = r.appointment_id
                WHERE a.appointment_id = :appointment_id 
                AND a.patient_id = :user_id";
        
        $result = $this->db->fetch($sql, [
            'appointment_id' => $appointmentId,
            'user_id' => $userId
        ]);

        if (!$result) {
            return false;
        }

        // Must be completed and not already reviewed
        return $result['status'] === 'completed' && !$result['review_id'];
    }

    public function getRatingDistribution($doctorId)
    {
        $stats = $this->getDoctorAverageRating($doctorId);
        
        $total = $stats['review_count'];
        
        if ($total == 0) {
            return [
                'average' => 0,
                'total' => 0,
                'distribution' => [
                    5 => ['count' => 0, 'percentage' => 0],
                    4 => ['count' => 0, 'percentage' => 0],
                    3 => ['count' => 0, 'percentage' => 0],
                    2 => ['count' => 0, 'percentage' => 0],
                    1 => ['count' => 0, 'percentage' => 0]
                ]
            ];
        }

        return [
            'average' => round($stats['average_rating'], 1),
            'total' => $total,
            'distribution' => [
                5 => [
                    'count' => $stats['five_star'],
                    'percentage' => round(($stats['five_star'] / $total) * 100)
                ],
                4 => [
                    'count' => $stats['four_star'],
                    'percentage' => round(($stats['four_star'] / $total) * 100)
                ],
                3 => [
                    'count' => $stats['three_star'],
                    'percentage' => round(($stats['three_star'] / $total) * 100)
                ],
                2 => [
                    'count' => $stats['two_star'],
                    'percentage' => round(($stats['two_star'] / $total) * 100)
                ],
                1 => [
                    'count' => $stats['one_star'],
                    'percentage' => round(($stats['one_star'] / $total) * 100)
                ]
            ]
        ];
    }
}