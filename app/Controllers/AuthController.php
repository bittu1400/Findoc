<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showRegister()
    {
        // If already logged in, redirect to home
        if (Auth::check()) {
            $this->redirect('');
        }

        $this->view('auth.register', [
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    public function register()
    {
        // Verify CSRF token
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request. Please try again.');
            $this->back();
        }

        // Sanitize input
        $data = $this->sanitize($_POST);

        // Validation rules
        $validation = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirm' => 'required|match:password',
            'role' => 'required',
            'phone' => 'min:10|max:15'
        ]);

        if ($validation !== true) {
            $this->setOldInput($data);
            $this->setFlash('errors', $validation);
            $this->back();
        }

        // Check if email already exists
        if ($this->userModel->emailExists($data['email'])) {
            $this->setOldInput($data);
            $this->setFlash('error', 'Email already registered. Please login.');
            $this->back();
        }

        // Validate role
        if (!in_array($data['role'], ['patient', 'doctor'])) {
            $this->setFlash('error', 'Invalid role selected.');
            $this->back();
        }

        // Create user
        try {
            $userId = $this->userModel->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'] ?? null,
                'role' => $data['role']
            ]);

            // Auto login after registration
            $user = $this->userModel->findById($userId);
            Auth::login($user);

            $this->clearOldInput();
            
            // Redirect based on role
            if ($data['role'] === 'doctor') {
                $this->setFlash('success', 'Registration successful! Please complete your profile.');
                $this->redirect('doctor/profile');
            } else {
                $this->setFlash('success', 'Registration successful! Welcome to FINDOC.');
                $this->redirect('doctors');
            }

        } catch (\Exception $e) {
            $this->setFlash('error', 'Registration failed. Please try again.');
            $this->back();
        }
    }

    public function showLogin()
    {
        // If already logged in, redirect to home
        if (Auth::check()) {
            $this->redirect('');
        }

        $this->view('auth.login', [
            'csrf_token' => Auth::generateCsrfToken()
        ]);
    }

    public function login()
    {
        // Verify CSRF token
        if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Invalid request. Please try again.');
            $this->back();
        }

        // Sanitize input
        $data = $this->sanitize($_POST);

        // Validation
        $validation = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validation !== true) {
            $this->setOldInput($data);
            $this->setFlash('errors', $validation);
            $this->back();
        }

        // Verify credentials
        $user = $this->userModel->verifyCredentials($data['email'], $data['password']);

        if (!$user) {
            $this->setOldInput(['email' => $data['email']]);
            $this->setFlash('error', 'Invalid email or password.');
            $this->back();
        }

        // Login user
        Auth::login($user);
        $this->clearOldInput();

        // Redirect to intended page or role-based default
        $intendedUrl = Auth::intended();
        
        if ($intendedUrl !== '/') {
            $this->redirect(ltrim($intendedUrl, '/'));
        }

        // Default redirects based on role
        switch ($user['role']) {
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            case 'doctor':
                $this->redirect('doctor/dashboard');
                break;
            default:
                $this->redirect('doctors');
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->setFlash('success', 'You have been logged out successfully.');
        $this->redirect('login');
    }
}

?>