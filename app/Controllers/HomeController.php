<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Doctor;
use App\Models\Review;

class HomeController extends Controller
{
    private $doctorModel;
    private $reviewModel;

    public function __construct()
    {
        $this->doctorModel = new Doctor();
        $this->reviewModel = new Review();
    }

    public function index()
    {
        // Get featured doctors (top rated, limit 6)
        $featuredDoctors = $this->doctorModel->getAll(['sort' => 'rating']);
        $featuredDoctors = array_slice($featuredDoctors, 0, 6);

        // Get all specialties for search
        $specialties = $this->doctorModel->getSpecialties();

        // Get recent reviews
        $recentReviews = $this->reviewModel->getRecentReviews(3);

        // Get statistics
        $stats = [
            'total_doctors' => $this->doctorModel->getTotalCount(),
            'total_specialties' => count($specialties),
            'total_reviews' => $this->reviewModel->getTotalCount()
        ];

        $this->view('home.index', [
            'featuredDoctors' => $featuredDoctors,
            'specialties' => $specialties,
            'recentReviews' => $recentReviews,
            'stats' => $stats
        ]);
    }
}