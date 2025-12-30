<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;

class AppointmentController extends Controller
{
    private $appointmentModel;
    private $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
    }

    // Patient: List all appointments
    public function index()
    {
        $userId = Auth::id();
        $appointments = $this->appointmentModel->getByPatient($userId);

        $this->view('appointments.index', [
            'appointments' => $appointments
        ]);
    }

    // Patient: Show booking form
    public function create($doctorId)
    {
        $doctor = $this->doctorModel->getById($doctorId);

        if (!$doctor) {
            $this->setFlash('error', 'Doctor not found.');
            $this->redirect('doctors');
        }

        // Get available slots for next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        $slots = $this->doctorModel->getTimeSlots($doctorId, $startDate, $endDate);

        // Group by date
        $availableSlots = [];
        foreach ($slots as $slot) {
            if (!$slot['is_booked']) {
                $availableSlots[$slot['slot_date']][] = $slot;
            }
        }

        $this->view('appointments.create', [
            'doctor' => $doctor,
            'availableSlots' => $availableSlots,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    // Patient: Store appointment
    public function store()
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request.');
            $this->back();
        }

        $data = $this->sanitize($_POST);

        $validation = $this->validate($data, [
            'doctor_id' => 'required|numeric',
            'slot_id' => 'required|numeric'
        ]);

        if ($validation !== true) {
            $this->setFlash('errors', $validation);
            $this->back();
        }

        try {
            $appointmentId = $this->appointmentModel->create([
                'patient_id' => Auth::id(),
                'doctor_id' => $data['doctor_id'],
                'slot_id' => $data['slot_id']
            ]);

            if ($appointmentId) {
                $this->setFlash('success', 'Appointment booked successfully!');
                $this->redirect('appointments/' . $appointmentId . '/payment');
            } else {
                $this->setFlash('error', 'This slot is no longer available. Please choose another.');
                $this->back();
            }

        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to book appointment. Please try again.');
            $this->back();
        }
    }

    // Patient/Doctor: View appointment details
    public function show($id)
    {
        $appointment = $this->appointmentModel->getById($id);

        if (!$appointment) {
            $this->setFlash('error', 'Appointment not found.');
            $this->redirect('appointments');
        }

        $userId = Auth::id();
        $userRole = Auth::role();

        // Check access permission
        if ($userRole === 'patient' && $appointment['patient_id'] != $userId) {
            $this->setFlash('error', 'Unauthorized access.');
            $this->redirect('appointments');
        }

        if ($userRole === 'doctor') {
            $doctor = $this->doctorModel->getByUserId($userId);
            if (!$doctor || $doctor['doctor_id'] != $appointment['doctor_id']) {
                $this->setFlash('error', 'Unauthorized access.');
                $this->redirect('doctor/appointments');
            }
        }

        // Check if payment exists
        $paymentSql = "SELECT * FROM payment WHERE appointment_id = :id";
        $db = \App\Core\Database::getInstance();
        $payment = $db->fetch($paymentSql, ['id' => $id]);

        $this->view('appointments.show', [
            'appointment' => $appointment,
            'payment' => $payment
        ]);
    }

    // Patient: Cancel appointment
    public function cancel($id)
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $userId = Auth::id();

        if (!$this->appointmentModel->canCancel($id, $userId)) {
            $this->json(['success' => false, 'message' => 'Cannot cancel this appointment'], 400);
        }

        if ($this->appointmentModel->cancel($id)) {
            $this->json(['success' => true, 'message' => 'Appointment cancelled successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to cancel appointment'], 500);
        }
    }

    // Doctor: Confirm appointment
    public function confirm($id)
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $appointment = $this->appointmentModel->getById($id);

        if (!$appointment) {
            $this->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }

        // Verify doctor owns this appointment
        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        if (!$doctor || $doctor['doctor_id'] != $appointment['doctor_id']) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($this->appointmentModel->updateStatus($id, 'confirmed')) {
            $this->json(['success' => true, 'message' => 'Appointment confirmed']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to confirm'], 500);
        }
    }

    // Doctor: Mark appointment as completed
    public function complete($id)
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $appointment = $this->appointmentModel->getById($id);

        if (!$appointment) {
            $this->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }

        // Verify doctor owns this appointment
        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        if (!$doctor || $doctor['doctor_id'] != $appointment['doctor_id']) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($this->appointmentModel->updateStatus($id, 'completed')) {
            $this->json(['success' => true, 'message' => 'Appointment marked as completed']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    // Patient: Payment page
    public function payment($id)
    {
        $appointment = $this->appointmentModel->getById($id);

        if (!$appointment) {
            $this->setFlash('error', 'Appointment not found.');
            $this->redirect('appointments');
        }

        $userId = Auth::id();

        if ($appointment['patient_id'] != $userId) {
            $this->setFlash('error', 'Unauthorized access.');
            $this->redirect('appointments');
        }

        // Check if already paid
        $db = \App\Core\Database::getInstance();
        $payment = $db->fetch(
            "SELECT * FROM payment WHERE appointment_id = :id AND status = 'success'",
            ['id' => $id]
        );

        if ($payment) {
            $this->setFlash('info', 'This appointment has already been paid.');
            $this->redirect('appointments/' . $id);
        }

        $this->view('appointments.payment', [
            'appointment' => $appointment,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    // Patient: Process payment
    public function processPayment($id)
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request.');
            $this->back();
        }

        $appointment = $this->appointmentModel->getById($id);

        if (!$appointment || $appointment['patient_id'] != Auth::id()) {
            $this->setFlash('error', 'Invalid appointment.');
            $this->redirect('appointments');
        }

        // Create payment record
        $db = \App\Core\Database::getInstance();
        
        try {
            $sql = "INSERT INTO payment (appointment_id, amount, currency, gateway, transaction_reference, status)
                    VALUES (:appointment_id, :amount, 'USD', 'manual', :reference, 'success')";
            
            $db->execute($sql, [
                'appointment_id' => $id,
                'amount' => $appointment['consultation_fee'],
                'reference' => 'TXN_' . uniqid()
            ]);

            // Update appointment status to confirmed
            $this->appointmentModel->updateStatus($id, 'confirmed');

            $this->setFlash('success', 'Payment successful! Your appointment is confirmed.');
            $this->redirect('appointments/' . $id);

        } catch (\Exception $e) {
            $this->setFlash('error', 'Payment failed. Please try again.');
            $this->back();
        }
    }

    // Patient: Review form
    public function reviewForm($id)
    {
        if (!$this->appointmentModel->canReview($id, Auth::id())) {
            $this->setFlash('error', 'You cannot review this appointment.');
            $this->redirect('appointments');
        }

        $appointment = $this->appointmentModel->getById($id);

        $this->view('appointments.review', [
            'appointment' => $appointment,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    // Patient: Submit review
    public function submitReview($id)
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request.');
            $this->back();
        }

        if (!$this->appointmentModel->canReview($id, Auth::id())) {
            $this->setFlash('error', 'You cannot review this appointment.');
            $this->redirect('appointments');
        }

        $data = $this->sanitize($_POST);

        $validation = $this->validate($data, [
            'rating' => 'required|numeric',
            'comment' => 'max:1000'
        ]);

        if ($validation !== true) {
            $this->setFlash('errors', $validation);
            $this->back();
        }

        if ($data['rating'] < 1 || $data['rating'] > 5) {
            $this->setFlash('error', 'Rating must be between 1 and 5.');
            $this->back();
        }

        try {
            $reviewModel = new Review();
            $reviewModel->create([
                'appointment_id' => $id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null
            ]);

            $this->setFlash('success', 'Thank you for your review!');
            $this->redirect('appointments/' . $id);

        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to submit review. Please try again.');
            $this->back();
        }
    }
}