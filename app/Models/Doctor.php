<?php

namespace App\Models;

use App\Core\Database;

class Doctor
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO doctor_profile 
                (user_id, specialty, experience_years, qualifications, clinic_name, consultation_fee, description) 
                VALUES (:user_id, :specialty, :experience_years, :qualifications, :clinic_name, :consultation_fee, :description)";
        
        $params = [
            'user_id' => $data['user_id'],
            'specialty' => $data['specialty'] ?? null,
            'experience_years' => $data['experience_years'] ?? 0,
            'qualifications' => $data['qualifications'] ?? null,
            'clinic_name' => $data['clinic_name'] ?? null,
            'consultation_fee' => $data['consultation_fee'],
            'description' => $data['description'] ?? null
        ];

        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function getAll($filters = [])
    {
        $sql = "SELECT dp.*, u.name, u.email, u.phone,
                COALESCE(AVG(r.rating), 0) as average_rating,
                COUNT(DISTINCT r.review_id) as review_count
                FROM doctor_profile dp
                INNER JOIN user_entity u ON dp.user_id = u.user_id
                LEFT JOIN appointment a ON dp.doctor_id = a.doctor_id AND a.status = 'completed'
                LEFT JOIN review r ON a.appointment_id = r.appointment_id
                WHERE 1=1";
        
        $params = [];

        // Filter by specialty
        if (!empty($filters['specialty'])) {
            $sql .= " AND dp.specialty LIKE :specialty";
            $params['specialty'] = '%' . $filters['specialty'] . '%';
        }

        // Filter by minimum experience
        if (!empty($filters['min_experience'])) {
            $sql .= " AND dp.experience_years >= :min_experience";
            $params['min_experience'] = $filters['min_experience'];
        }

        // Filter by max consultation fee
        if (!empty($filters['max_fee'])) {
            $sql .= " AND dp.consultation_fee <= :max_fee";
            $params['max_fee'] = $filters['max_fee'];
        }

        // Search by name or clinic
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE :search OR dp.clinic_name LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " GROUP BY dp.doctor_id, u.user_id";

        // Sorting
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'rating':
                    $sql .= " ORDER BY average_rating DESC";
                    break;
                case 'experience':
                    $sql .= " ORDER BY dp.experience_years DESC";
                    break;
                case 'fee_low':
                    $sql .= " ORDER BY dp.consultation_fee ASC";
                    break;
                case 'fee_high':
                    $sql .= " ORDER BY dp.consultation_fee DESC";
                    break;
                default:
                    $sql .= " ORDER BY u.name ASC";
            }
        } else {
            $sql .= " ORDER BY u.name ASC";
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id)
    {
        $sql = "SELECT dp.*, u.name, u.email, u.phone,
                COALESCE(AVG(r.rating), 0) as average_rating,
                COUNT(DISTINCT r.review_id) as review_count
                FROM doctor_profile dp
                INNER JOIN user_entity u ON dp.user_id = u.user_id
                LEFT JOIN appointment a ON dp.doctor_id = a.doctor_id AND a.status = 'completed'
                LEFT JOIN review r ON a.appointment_id = r.appointment_id
                WHERE dp.doctor_id = :id
                GROUP BY dp.doctor_id, u.user_id";
        
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM doctor_profile WHERE user_id = :user_id LIMIT 1";
        return $this->db->fetch($sql, ['user_id' => $userId]);
    }

    public function update($doctorId, $data)
    {
        $fields = [];
        $params = ['doctor_id' => $doctorId];

        $allowedFields = ['specialty', 'experience_years', 'qualifications', 'clinic_name', 'consultation_fee', 'description'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE doctor_profile SET " . implode(', ', $fields) . " WHERE doctor_id = :doctor_id";
        return $this->db->execute($sql, $params) > 0;
    }

    public function getAvailableSlots($doctorId, $date)
    {
        $sql = "SELECT * FROM time_slot 
                WHERE doctor_id = :doctor_id 
                AND slot_date = :date 
                AND is_booked = 0
                ORDER BY start_time ASC";
        
        return $this->db->fetchAll($sql, [
            'doctor_id' => $doctorId,
            'date' => $date
        ]);
    }

    public function createTimeSlot($data)
    {
        $sql = "INSERT INTO time_slot (doctor_id, slot_date, start_time, end_time, is_booked) 
                VALUES (:doctor_id, :slot_date, :start_time, :end_time, 0)";
        
        try {
            $this->db->execute($sql, $data);
            return true;
        } catch (\Exception $e) {
            // Duplicate slot
            return false;
        }
    }

    public function getTimeSlots($doctorId, $startDate, $endDate)
    {
        $sql = "SELECT * FROM time_slot 
                WHERE doctor_id = :doctor_id 
                AND slot_date BETWEEN :start_date AND :end_date
                ORDER BY slot_date ASC, start_time ASC";
        
        return $this->db->fetchAll($sql, [
            'doctor_id' => $doctorId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    public function deleteTimeSlot($slotId)
    {
        $sql = "DELETE FROM time_slot WHERE slot_id = :slot_id AND is_booked = 0";
        return $this->db->execute($sql, ['slot_id' => $slotId]) > 0;
    }

    public function setUnavailability($doctorId, $date, $reason = null)
    {
        $sql = "INSERT INTO doctor_unavailability (doctor_id, unavailable_date, reason) 
                VALUES (:doctor_id, :unavailable_date, :reason)";
        
        try {
            $this->db->execute($sql, [
                'doctor_id' => $doctorId,
                'unavailable_date' => $date,
                'reason' => $reason
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUnavailableDates($doctorId)
    {
        $sql = "SELECT * FROM doctor_unavailability 
                WHERE doctor_id = :doctor_id 
                AND unavailable_date >= CURDATE()
                ORDER BY unavailable_date ASC";
        
        return $this->db->fetchAll($sql, ['doctor_id' => $doctorId]);
    }

    public function getReviews($doctorId, $limit = null)
    {
        $sql = "SELECT r.*, u.name as patient_name, a.appointment_id
                FROM review r
                INNER JOIN appointment a ON r.appointment_id = a.appointment_id
                INNER JOIN user_entity u ON a.patient_id = u.user_id
                WHERE a.doctor_id = :doctor_id
                ORDER BY r.review_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            return $this->db->fetchAll($sql, ['doctor_id' => $doctorId, 'limit' => $limit]);
        }
        
        return $this->db->fetchAll($sql, ['doctor_id' => $doctorId]);
    }

    public function getSpecialties()
    {
        $sql = "SELECT DISTINCT specialty FROM doctor_profile WHERE specialty IS NOT NULL ORDER BY specialty ASC";
        return $this->db->fetchAll($sql);
    }

    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM doctor_profile";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }
}