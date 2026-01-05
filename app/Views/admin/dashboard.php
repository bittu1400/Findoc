<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .sidebar { min-height: 100vh; background: #1a1a1a; }
        .sidebar .nav-link { color: #fff; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #FFC629; color: #000; }
        .stat-card { border-left: 4px solid; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" style="width: 250px;">
            <div class="p-3 border-bottom">
                <h5 class="mb-0 text-white" style="color: #FFC629;">FINDOC</h5>
                <small class="text-white-50">Admin Panel</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link active" href="/admin/dashboard">📊 Dashboard</a>
                <a class="nav-link" href="/admin/users">👥 Users</a>
                <a class="nav-link" href="/admin/doctors">👨‍⚕️ Doctors</a>
                <a class="nav-link" href="/admin/appointments">📋 Appointments</a>
                <a class="nav-link" href="/admin/payments">💰 Payments</a>
                <a class="nav-link" href="/admin/reviews">⭐ Reviews</a>
                <a class="nav-link" href="/">🏠 Home</a>
                <a class="nav-link" href="/logout">🚪 Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 bg-light">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <h4 class="mb-0">Admin Dashboard</h4>
                    <span class="navbar-text">
                        Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                    </span>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">System Overview</h5>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm h-100" style="border-left-color: #0d6efd;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Users</h6>
                                        <h2 class="mb-0"><?= $stats['total_users'] ?></h2>
                                        <small class="text-muted">All registered</small>
                                    </div>
                                    <div class="fs-1 text-primary">👥</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm h-100" style="border-left-color: #198754;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Doctors</h6>
                                        <h2 class="mb-0"><?= $stats['total_doctors'] ?></h2>
                                        <small class="text-muted">Active providers</small>
                                    </div>
                                    <div class="fs-1 text-success">👨‍⚕️</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm h-100" style="border-left-color: #ffc107;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Appointments</h6>
                                        <h2 class="mb-0"><?= $stats['total_appointments'] ?></h2>
                                        <small class="text-warning"><?= $stats['today_appointments'] ?> today</small>
                                    </div>
                                    <div class="fs-1 text-warning">📅</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm h-100" style="border-left-color: #dc3545;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Revenue</h6>
                                        <h2 class="mb-0">${= number_format($paymentStats['total_revenue'] ?? 0, 2) ?></h2>
                                        <small class="text-muted"><?= $paymentStats['successful_payments'] ?? 0 ?> transactions</small>
                                    </div>
                                    <div class="fs-1 text-danger">💰</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Recent Appointments</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Patient</th>
                                                <th>Doctor</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($recentAppointments, 0, 5) as $apt): ?>
                                                <tr>
                                                    <td>#<?= $apt['appointment_id'] ?></td>
                                                    <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                    <td><?= htmlspecialchars($apt['doctor_name']) ?></td>
                                                    <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $apt['status'] === 'pending' ? 'warning' : ($apt['status'] === 'confirmed' ? 'info' : ($apt['status'] === 'completed' ? 'success' : 'danger')) ?>">
                                                            <?= ucfirst($apt['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="/admin/appointments" class="btn btn-outline-primary btn-sm">View All</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Quick Stats</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Patients</span>
                                    <strong><?= $stats['total_patients'] ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Total Reviews</span>
                                    <strong><?= $stats['total_reviews'] ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Failed Payments</span>
                                    <strong class="text-danger"><?= $paymentStats['failed_payments'] ?? 0 ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Total Payments</span>
                                    <strong><?= $paymentStats['total_payments'] ?? 0 ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Recent Reviews</h6>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recentReviews as $review): ?>
                                    <div class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between mb-1">
                                            <strong class="small"><?= htmlspecialchars($review['patient_name']) ?></strong>
                                            <span class="text-warning small">
                                                <?= str_repeat('★', $review['rating']) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            For Dr. <?= htmlspecialchars($review['doctor_name']) ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                                <div class="text-center">
                                    <a href="/admin/reviews" class="btn btn-outline-primary btn-sm">View All</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>