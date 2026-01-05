<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .sidebar { min-height: 100vh; background: #f8f9fa; }
        .sidebar .nav-link { color: #333; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #FFC629; color: #000; }
        .stat-card { border-left: 4px solid #FFC629; }
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
                <a class="nav-link active" href="/doctor/dashboard">📊 Dashboard</a>
                <a class="nav-link" href="/doctor/profile">👤 Profile</a>
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
                    <span class="navbar-text">
                        Welcome back, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                    </span>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <h2 class="mb-4">Dashboard Overview</h2>

                <?php if (isset($_SESSION['flash']['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Total Appointments</h6>
                                <h2 class="mb-0"><?= $stats['total_count'] ?? 0 ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Pending</h6>
                                <h2 class="mb-0 text-warning"><?= $stats['pending_count'] ?? 0 ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Confirmed</h6>
                                <h2 class="mb-0 text-info"><?= $stats['confirmed_count'] ?? 0 ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Completed</h6>
                                <h2 class="mb-0 text-success"><?= $stats['completed_count'] ?? 0 ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Today's Appointments</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($todayAppointments)): ?>
                            <p class="text-muted text-center py-3">No appointments scheduled for today</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Patient</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($todayAppointments as $apt): ?>
                                            <tr>
                                                <td class="fw-bold"><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                <td><?= htmlspecialchars($apt['patient_phone']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $apt['status'] === 'confirmed' ? 'info' : 'warning' ?>">
                                                        <?= ucfirst($apt['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Upcoming Appointments (Next 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($upcomingAppointments)): ?>
                            <p class="text-muted text-center py-3">No upcoming appointments</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Patient</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcomingAppointments as $apt): ?>
                                            <tr>
                                                <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                <td class="fw-bold"><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                <td><?= htmlspecialchars($apt['patient_phone']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $apt['status'] === 'confirmed' ? 'info' : 'warning' ?>">
                                                        <?= ucfirst($apt['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>