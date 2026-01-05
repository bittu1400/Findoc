<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .sidebar { min-height: 100vh; background: #f8f9fa; }
        .sidebar .nav-link { color: #333; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #FFC629; color: #000; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar border-end" style="width: 250px;">
            <div class="p-3 border-bottom">
                <h5 class="mb-0" style="color: #FFC629;">FINDOC</h5>
                <small class="text-muted">Doctor Portal</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link" href="/doctor/dashboard">📊 Dashboard</a>
                <a class="nav-link active" href="/doctor/profile">👤 Profile</a>
                <a class="nav-link" href="/doctor/availability">📅 Availability</a>
                <a class="nav-link" href="/doctor/appointments">📋 Appointments</a>
                <a class="nav-link" href="/">🏠 Home</a>
                <a class="nav-link" href="/logout">🚪 Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <?php if (isset($_SESSION['flash']['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['flash']['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['flash']['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['flash']['errors'] as $field => $errors): ?>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash']['errors']); ?>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Professional Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="/doctor/profile">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Specialty *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="specialty" 
                                                   value="<?= htmlspecialchars($doctor['specialty'] ?? $_SESSION['old']['specialty'] ?? '') ?>"
                                                   required>
                                            <small class="text-muted">e.g., Cardiologist, Dermatologist</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Years of Experience *</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="experience_years" 
                                                   value="<?= htmlspecialchars($doctor['experience_years'] ?? $_SESSION['old']['experience_years'] ?? '') ?>"
                                                   min="0"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Qualifications *</label>
                                        <textarea class="form-control" 
                                                  name="qualifications" 
                                                  rows="3"
                                                  required><?= htmlspecialchars($doctor['qualifications'] ?? $_SESSION['old']['qualifications'] ?? '') ?></textarea>
                                        <small class="text-muted">List your degrees, certifications (e.g., MBBS, MD)</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Clinic Name *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="clinic_name" 
                                                   value="<?= htmlspecialchars($doctor['clinic_name'] ?? $_SESSION['old']['clinic_name'] ?? '') ?>"
                                                   required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Consultation Fee (USD) *</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="consultation_fee" 
                                                   value="<?= htmlspecialchars($doctor['consultation_fee'] ?? $_SESSION['old']['consultation_fee'] ?? '') ?>"
                                                   min="0"
                                                   step="0.01"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">About / Description</label>
                                        <textarea class="form-control" 
                                                  name="description" 
                                                  rows="5"
                                                  maxlength="1000"
                                                  placeholder="Tell patients about yourself, your experience, and approach to care..."><?= htmlspecialchars($doctor['description'] ?? $_SESSION['old']['description'] ?? '') ?></textarea>
                                        <small class="text-muted">Maximum 1000 characters</small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary px-4">
                                            Save Profile
                                        </button>
                                        <a href="/doctor/dashboard" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Profile Preview -->
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Profile Preview</h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 100px; height: 100px; font-size: 40px; font-weight: 700; color: #FFC629;">
                                    <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                                </div>
                                <h5><?= htmlspecialchars($_SESSION['user_name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($doctor['specialty'] ?? 'Your Specialty') ?></p>
                            </div>
                        </div>

                        <!-- Quick Tips -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">💡 Profile Tips</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small mb-0">
                                    <li>Complete all fields for better visibility</li>
                                    <li>Use a professional description</li>
                                    <li>Keep your qualifications up to date</li>
                                    <li>Set competitive consultation fees</li>
                                    <li>Update your availability regularly</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php unset($_SESSION['old']); ?>
</body>
</html>