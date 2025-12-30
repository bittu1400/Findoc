<?php

namespace App\Models;

use App\Core\Database;

class Appointment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        // Begin transaction
        $this->db->beginTransaction();

        try {
            // Check if slot is still available
            $slotCheck = "SELECT is_booked FROM time_slot WHERE slot_id = :slot_id FOR UPDATE";
            $slot = $this->db->fetch($slotCheck, ['slot_id' => $data['slot_id']]);

            if (!$slot || $slot['is_booked'] == 1) {
                $this->db->rollback();
                return false;
            }

            // Create appointment
            $sql = "INSERT INTO appointment (patient_id, doctor_id, slot_id, status) 
                    VALUES (:patient_id, :doctor_id, :slot_id, 'pending')";
            
            $this->db->execute($sql, [
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'slot_id' => $data['slot_id']
            ]);

            $appointmentId = $this->db->lastInsertId();

            // Mark slot as booked
            $updateSlot = "UPDATE time_slot SET is_booked = 1 WHERE slot_id = :slot_id";
            $this->db->execute($updateSlot, ['slot_id' => $data['slot_id']]);

            $this->db->commit();
            return $appointmentId;

        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getById($id)
    {
        $sql = "SELECT a.*, 
                ts.slot_date, ts.start_time, ts.end_time,
                dp.specialty, dp.clinic_name, dp.consultation_fee,
                d.name as doctor_name, d.phone as doctor_phone,
                p.name as patient_name, p.phone as patient_phone, p.email as patient_email
                FROM appointment a
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                INNER JOIN doctor_profile dp ON a.doctor_id = dp.doctor_id
                INNER JOIN user_entity d ON dp.user_id = d.user_id
                INNER JOIN user_entity p ON a.patient_id = p.user_id
                WHERE a.appointment_id = :id";
        
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getByPatient($patientId)
    {
        $sql = "SELECT a.*, 
                ts.slot_date, ts.start_time, ts.end_time,
                dp.specialty, dp.clinic_name, dp.consultation_fee,
                d.name as doctor_name, d.phone as doctor_phone,
                COALESCE(r.review_id, 0) as has_review
                FROM appointment a
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                INNER JOIN doctor_profile dp ON a.doctor_id = dp.doctor_id
                INNER JOIN user_entity d ON dp.user_id = d.user_id
                LEFT JOIN review r ON a.appointment_id = r.appointment_id
                WHERE a.patient_id = :patient_id
                ORDER BY ts.slot_date DESC, ts.start_time DESC";
        
        return $this->db->fetchAll($sql, ['patient_id' => $patientId]);
    }

    public function getByDoctor($doctorId)
    {
        $sql = "SELECT a.*, 
                ts.slot_date, ts.start_time, ts.end_time,
                p.name as patient_name, p.phone as patient_phone, p.email as patient_email
                FROM appointment a
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                INNER JOIN user_entity p ON a.patient_id = p.user_id
                WHERE a.doctor_id = :doctor_id
                ORDER BY ts.slot_date DESC, ts.start_time DESC";
        
        return $this->db->fetchAll($sql, ['doctor_id' => $doctorId]);
    }

    public function getDoctorAppointmentsToday($doctorId)
    {
        $sql = "SELECT a.*, 
                ts.slot_date, ts.start_time, ts.end_time,
                p.name as patient_name, p.phone as patient_phone
                FROM appointment a
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                INNER JOIN user_entity p ON a.patient_id = p.user_id
                WHERE a.doctor_id = :doctor_id 
                AND ts.slot_date = CURDATE()
                AND a.status IN ('pending', 'confirmed')
                ORDER BY ts.start_time ASC";
        
        return $this->db->fetchAll($sql, ['doctor_id' => $doctorId]);
    }

    public function getDoctorUpcomingAppointments($doctorId, $days = 7)
    {
        $sql = "SELECT a.*, 
                ts.slot_date, ts.start_time, ts.end_time,
                p.name as patient_name, p.phone as patient_phone
                FROM appointment a
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                INNER JOIN user_entity p ON a.patient_id = p.user_id
                WHERE a.doctor_id = :doctor_id 
                AND ts.slot_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                AND a.status IN ('pending', 'confirmed')
                ORDER BY ts.slot_date ASC, ts.start_time ASC";
        
        return $this->db->fetchAll($sql, [
            'doctor_id' => $doctorId,
            'days' => $days
        ]);
    }

    public function updateStatus($appointmentId, $status)
    {
        $sql = "UPDATE appointment SET status = :status WHERE appointment_id = :id";
        return $this->db->execute($sql, [
            'status' => $status,
            'id' => $appointmentId
        ]) > 0;
    }

    public function cancel($appointmentId)
    {
        $this->db->beginTransaction();

        try {
            // Get slot ID
            $appointment = $this->getById($appointmentId);
            
            if (!$appointment) {
                $this->db->rollback();
                return false;
            }

            // Update appointment status
            $this->updateStatus($appointmentId, 'cancelled');

            // Free up the slot
            $updateSlot = "UPDATE time_slot SET is_booked = 0 WHERE slot_id = :slot_id";
            $this->db->execute($updateSlot, ['slot_id' => $appointment['slot_id']]);

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function canCancel($appointmentId, $userId)
    {
        $appointment = $this->getById($appointmentId);
        
        if (!$appointment) {
            return false;
        }

        // Check if user owns this appointment
        if ($appointment['patient_id'] != $userId) {
            return false;
        }

        // Check if appointment is in the future
        $appointmentDateTime = $appointment['slot_date'] . ' ' . $appointment['start_time'];
        if (strtotime($appointmentDateTime) < time()) {
            return false;
        }

        // Can only cancel pending or confirmed appointments
        if (!in_array($appointment['status'], ['pending', 'confirmed'])) {
            return false;
        }

        return true;
    }

    public function canReview($appointmentId, $userId)
    {
        $sql = "SELECT a.*, r.review_id
                FROM appointment a
                LEFT JOIN review r ON a.appointment_id = r.appointment_id
                WHERE a.appointment_id = :id AND a.patient_id = :user_id";
        
        $appointment = $this->db->fetch($sql, [
            'id' => $appointmentId,
            'user_id' => $userId
        ]);

        if (!$appointment) {
            return false;
        }

        // Must be completed
        if ($appointment['status'] !== 'completed') {
            return false;
        }

        // Cannot review twice
        if ($appointment['review_id']) {
            return false;
        }

        return true;
    }

    public function getDoctorStats($doctorId)
    {
        $sql = "SELECT 
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_count,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count,
                COUNT(*) as total_count
                FROM appointment
                WHERE doctor_id = :doctor_id";
        
        return $this->db->fetch($sql, ['doctor_id' => $doctorId]);
    }

    public function getAll()
    {
        $sql = "SELECT a.*, 
                ts.slot_date, ts.start_time, ts.end_time,
                dp.specialty, dp.clinic_name,
                d.name as doctor_name,
                p.name as patient_name
                FROM appointment a
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                INNER JOIN doctor_profile dp ON a.doctor_id = dp.doctor_id
                INNER JOIN user_entity d ON dp.user_id = d.user_id
                INNER JOIN user_entity p ON a.patient_id = p.user_id
                ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM appointment";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }

    public function getTodayCount()
    {
        $sql = "SELECT COUNT(*) as count FROM appointment a
                INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
                WHERE ts.slot_date = CURDATE()";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }
}