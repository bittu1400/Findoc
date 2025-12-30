<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Review;

class AdminController extends Controller
{
    private $userModel;
    private $doctorModel;
    private $appointmentModel;
    private $reviewModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->doctorModel = new Doctor();
        $this->appointmentModel = new Appointment();
        $this->reviewModel = new Review();
    }

    public function dashboard()
    {
        // Get overall statistics
        $stats = [
            'total_users' => $this->userModel->getTotalCount(),
            'total_patients' => $this->userModel->getCountByRole('patient'),
            'total_doctors' => $this->userModel->getCountByRole('doctor'),
            'total_appointments' => $this->appointmentModel->getTotalCount(),
            'today_appointments' => $this->appointmentModel->getTodayCount(),
            'total_reviews' => $this->reviewModel->getTotalCount()
        ];

        // Get recent appointments
        $recentAppointments = $this->appointmentModel->getAll();
        $recentAppointments = array_slice($recentAppointments, 0, 10);

        // Get recent reviews
        $recentReviews = $this->reviewModel->getRecentReviews(5);

        // Get payment statistics
        $db = \App\Core\Database::getInstance();
        $paymentStats = $db->fetch(
            "SELECT 
                COUNT(*) as total_payments,
                SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as total_revenue,
                COUNT(CASE WHEN status = 'success' THEN 1 END) as successful_payments,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_payments
            FROM payment"
        );

        $this->view('admin.dashboard', [
            'stats' => $stats,
            'paymentStats' => $paymentStats,
            'recentAppointments' => $recentAppointments,
            'recentReviews' => $recentReviews
        ]);
    }

    public function users()
    {
        $role = $_GET['role'] ?? null;
        $users = $this->userModel->getAll($role);

        $this->view('admin.users', [
            'users' => $users,
            'selectedRole' => $role,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    public function doctors()
    {
        $doctors = $this->doctorModel->getAll();

        $this->view('admin.doctors', [
            'doctors' => $doctors,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    public function appointments()
    {
        $appointments = $this->appointmentModel->getAll();

        // Group by status
        $grouped = [
            'pending' => [],
            'confirmed' => [],
            'completed' => [],
            'cancelled' => []
        ];

        foreach ($appointments as $appointment) {
            $status = $appointment['status'];
            if (isset($grouped[$status])) {
                $grouped[$status][] = $appointment;
            }
        }

        $this->view('admin.appointments', [
            'appointments' => $appointments,
            'grouped' => $grouped,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    public function payments()
    {
        $db = \App\Core\Database::getInstance();
        
        $payments = $db->fetchAll(
            "SELECT p.*, a.appointment_id,
                u.name as patient_name,
                d.name as doctor_name,
                dp.specialty
            FROM payment p
            INNER JOIN appointment a ON p.appointment_id = a.appointment_id
            INNER JOIN user_entity u ON a.patient_id = u.user_id
            INNER JOIN doctor_profile dp ON a.doctor_id = dp.doctor_id
            INNER JOIN user_entity d ON dp.user_id = d.user_id
            ORDER BY p.created_at DESC"
        );

        // Calculate statistics
        $stats = [
            'total_revenue' => 0,
            'successful_count' => 0,
            'failed_count' => 0,
            'pending_count' => 0
        ];

        foreach ($payments as $payment) {
            if ($payment['status'] === 'success') {
                $stats['total_revenue'] += $payment['amount'];
                $stats['successful_count']++;
            } elseif ($payment['status'] === 'failed') {
                $stats['failed_count']++;
            } elseif ($payment['status'] === 'initiated') {
                $stats['pending_count']++;
            }
        }

        $this->view('admin.payments', [
            'payments' => $payments,
            'stats' => $stats
        ]);
    }

    public function reviews()
    {
        $reviews = $this->reviewModel->getAll();

        $this->view('admin.reviews', [
            'reviews' => $reviews,
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    public function deleteUser()
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $userId = $_POST['user_id'] ?? null;

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User ID required'], 400);
        }

        // Don't allow deleting yourself
        if ($userId == Auth::id()) {
            $this->json(['success' => false, 'message' => 'Cannot delete your own account'], 400);
        }

        if ($this->userModel->delete($userId)) {
            $this->json(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete user'], 500);
        }
    }

    public function deleteReview()
    {
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $reviewId = $_POST['review_id'] ?? null;

        if (!$reviewId) {
            $this->json(['success' => false, 'message' => 'Review ID required'], 400);
        }

        if ($this->reviewModel->delete($reviewId)) {
            $this->json(['success' => true, 'message' => 'Review deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete review'], 500);
        }
    }

    public function reports()
    {
        $db = \App\Core\Database::getInstance();

        // Monthly appointment trends (last 12 months)
        $appointmentTrends = $db->fetchAll(
            "SELECT 
                DATE_FORMAT(ts.slot_date, '%Y-%m') as month,
                COUNT(*) as count,
                COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN a.status = 'cancelled' THEN 1 END) as cancelled
            FROM appointment a
            INNER JOIN time_slot ts ON a.slot_id = ts.slot_id
            WHERE ts.slot_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month ASC"
        );

        // Revenue by month
        $revenueTrends = $db->fetchAll(
            "SELECT 
                DATE_FORMAT(p.created_at, '%Y-%m') as month,
                SUM(p.amount) as revenue,
                COUNT(*) as transaction_count
            FROM payment p
            WHERE p.status = 'success'
            AND p.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month ASC"
        );

        // Top doctors by appointments
        $topDoctors = $db->fetchAll(
            "SELECT 
                d.name as doctor_name,
                dp.specialty,
                COUNT(a.appointment_id) as total_appointments,
                COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as completed_appointments,
                COALESCE(AVG(r.rating), 0) as average_rating
            FROM doctor_profile dp
            INNER JOIN user_entity d ON dp.user_id = d.user_id
            LEFT JOIN appointment a ON dp.doctor_id = a.doctor_id
            LEFT JOIN review r ON a.appointment_id = r.appointment_id AND a.status = 'completed'
            GROUP BY dp.doctor_id, d.name, dp.specialty
            ORDER BY total_appointments DESC
            LIMIT 10"
        );

        // Specialty distribution
        $specialtyStats = $db->fetchAll(
            "SELECT 
                dp.specialty,
                COUNT(DISTINCT dp.doctor_id) as doctor_count,
                COUNT(a.appointment_id) as appointment_count
            FROM doctor_profile dp
            LEFT JOIN appointment a ON dp.doctor_id = a.doctor_id
            WHERE dp.specialty IS NOT NULL
            GROUP BY dp.specialty
            ORDER BY appointment_count DESC"
        );

        $this->view('admin.reports', [
            'appointmentTrends' => $appointmentTrends,
            'revenueTrends' => $revenueTrends,
            'topDoctors' => $topDoctors,
            'specialtyStats' => $specialtyStats
        ]);
    }
}