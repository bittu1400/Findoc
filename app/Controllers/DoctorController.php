<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Doctor;
use App\Models\Appointment;

class DoctorController extends Controller
{
    private $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new Doctor();
    }

    // Public: List all doctors
    public function index()
    {
        $filters = [
            'specialty' => $_GET['specialty'] ?? '',
            'search' => $_GET['search'] ?? '',
            'min_experience' => $_GET['min_experience'] ?? '',
            'max_fee' => $_GET['max_fee'] ?? '',
            'sort' => $_GET['sort'] ?? 'name'
        ];

        $doctors = $this->doctorModel->getAll($filters);
        $specialties = $this->doctorModel->getSpecialties();

        $this->view('doctors.index', [
            'doctors' => $doctors,
            'specialties' => $specialties,
            'filters' => $filters
        ]);
    }

    // Public: Search doctors (AJAX)
    public function search()
    {
        $filters = [
            'specialty' => $_GET['specialty'] ?? '',
            'search' => $_GET['search'] ?? '',
            'min_experience' => $_GET['min_experience'] ?? '',
            'max_fee' => $_GET['max_fee'] ?? '',
            'sort' => $_GET['sort'] ?? 'name'
        ];

        $doctors = $this->doctorModel->getAll($filters);

        $this->json([
            'success' => true,
            'doctors' => $doctors
        ]);
    }

    // Public: View single doctor profile
    public function show($id)
    {
        $doctor = $this->doctorModel->getById($id);

        if (!$doctor) {
            $this->setFlash('error', 'Doctor not found.');
            $this->redirect('doctors');
        }

        // Get available dates for next 30 days
        $availableDates = [];
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        
        $slots = $this->doctorModel->getTimeSlots($id, $startDate, $endDate);
        
        foreach ($slots as $slot) {
            if (!$slot['is_booked']) {
                $availableDates[$slot['slot_date']][] = $slot;
            }
        }

        // Get reviews
        $reviews = $this->doctorModel->getReviews($id, 10);

        $this->view('doctors.show', [
            'doctor' => $doctor,
            'availableDates' => $availableDates,
            'reviews' => $reviews
        ]);
    }

    // Doctor only: Dashboard
    public function dashboard()
    {
        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        if (!$doctor) {
            $this->setFlash('error', 'Please complete your profile first.');
            $this->redirect('doctor/profile');
        }

        // Get today's appointments
        $appointmentModel = new Appointment();
        $todayAppointments = $appointmentModel->getDoctorAppointmentsToday($doctor['doctor_id']);
        $upcomingAppointments = $appointmentModel->getDoctorUpcomingAppointments($doctor['doctor_id'], 7);
        $stats = $appointmentModel->getDoctorStats($doctor['doctor_id']);

        $this->view('doctors.dashboard', [
            'doctor' => $doctor,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'stats' => $stats
        ]);
    }

    // Doctor only: View/Edit profile
    public function profile()
    {
        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);
        $specialties = $this->doctorModel->getSpecialties();

        $this->view('doctors.profile', [
            'doctor' => $doctor,
            'specialties' => $specialties,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    // Doctor only: Update profile
    public function updateProfile()
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request.');
            $this->back();
        }

        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        $data = $this->sanitize($_POST);

        $validation = $this->validate($data, [
            'specialty' => 'required|min:2|max:100',
            'experience_years' => 'required|numeric',
            'qualifications' => 'required|min:5',
            'clinic_name' => 'required|min:2|max:100',
            'consultation_fee' => 'required|numeric',
            'description' => 'max:1000'
        ]);

        if ($validation !== true) {
            $this->setOldInput($data);
            $this->setFlash('errors', $validation);
            $this->back();
        }

        try {
            if ($doctor) {
                // Update existing profile
                $this->doctorModel->update($doctor['doctor_id'], $data);
                $this->setFlash('success', 'Profile updated successfully!');
            } else {
                // Create new profile
                $data['user_id'] = $userId;
                $this->doctorModel->create($data);
                $this->setFlash('success', 'Profile created successfully!');
            }

            $this->clearOldInput();
            $this->redirect('doctor/dashboard');

        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to update profile. Please try again.');
            $this->back();
        }
    }

    // Doctor only: Manage availability
    public function availability()
    {
        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        if (!$doctor) {
            $this->setFlash('error', 'Please complete your profile first.');
            $this->redirect('doctor/profile');
        }

        // Get slots for next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        $slots = $this->doctorModel->getTimeSlots($doctor['doctor_id'], $startDate, $endDate);

        // Get unavailable dates
        $unavailableDates = $this->doctorModel->getUnavailableDates($doctor['doctor_id']);

        $this->view('doctors.availability', [
            'doctor' => $doctor,
            'slots' => $slots,
            'unavailableDates' => $unavailableDates,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    // Doctor only: Create time slots
    public function createSlots()
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        if (!$doctor) {
            $this->json(['success' => false, 'message' => 'Doctor profile not found'], 404);
        }

        $data = $this->sanitize($_POST);

        $validation = $this->validate($data, [
            'slot_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        if ($validation !== true) {
            $this->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validation], 400);
        }

        $slotData = [
            'doctor_id' => $doctor['doctor_id'],
            'slot_date' => $data['slot_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time']
        ];

        if ($this->doctorModel->createTimeSlot($slotData)) {
            $this->json(['success' => true, 'message' => 'Time slot created successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to create slot. It may already exist.'], 400);
        }
    }

    // Doctor only: Set unavailable dates
    public function setUnavailability()
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        if (!$doctor) {
            $this->json(['success' => false, 'message' => 'Doctor profile not found'], 404);
        }

        $data = $this->sanitize($_POST);

        if (empty($data['unavailable_date'])) {
            $this->json(['success' => false, 'message' => 'Date is required'], 400);
        }

        if ($this->doctorModel->setUnavailability($doctor['doctor_id'], $data['unavailable_date'], $data['reason'] ?? null)) {
            $this->json(['success' => true, 'message' => 'Unavailability set successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to set unavailability'], 400);
        }
    }

    // Doctor only: View appointments
    public function appointments()
    {
        $userId = Auth::id();
        $doctor = $this->doctorModel->getByUserId($userId);

        if (!$doctor) {
            $this->setFlash('error', 'Please complete your profile first.');
            $this->redirect('doctor/profile');
        }

        $appointmentModel = new Appointment();
        $appointments = $appointmentModel->getByDoctor($doctor['doctor_id']);

        $this->view('doctors.appointments', [
            'doctor' => $doctor,
            'appointments' => $appointments
        ]);
    }
}