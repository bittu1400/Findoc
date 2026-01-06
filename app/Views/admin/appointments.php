<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .sidebar { min-height: 100vh; background: #1a1a1a; }
        .sidebar .nav-link { color: #fff; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #FFC629; color: #000; }
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
                <a class="nav-link" href="/admin/dashboard">📊 Dashboard</a>
                <a class="nav-link" href="/admin/users">👥 Users</a>
                <a class="nav-link" href="/admin/doctors">👨‍⚕️ Doctors</a>
                <a class="nav-link active" href="/admin/appointments">📋 Appointments</a>
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
                    <h4 class="mb-0">Appointment Management</h4>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <!-- Status Summary -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-warning">
                            <div class="card-body">
                                <h3 class="text-warning"><?= count($grouped['pending']) ?></h3>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-info">
                            <div class="card-body">
                                <h3 class="text-info"><?= count($grouped['confirmed']) ?></h3>
                                <p class="text-muted mb-0">Confirmed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-success">
                            <div class="card-body">
                                <h3 class="text-success"><?= count($grouped['completed']) ?></h3>
                                <p class="text-muted mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-danger">
                            <div class="card-body">
                                <h3 class="text-danger"><?= count($grouped['cancelled']) ?></h3>
                                <p class="text-muted mb-0">Cancelled</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#all">
                                    All (<?= count($appointments) ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#pending">
                                    Pending (<?= count($grouped['pending']) ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#confirmed">
                                    Confirmed (<?= count($grouped['confirmed']) ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#completed">
                                    Completed (<?= count($grouped['completed']) ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#cancelled">
                                    Cancelled (<?= count($grouped['cancelled']) ?>)
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="searchInput" class="form-control" 
                                   placeholder="Search by patient name, doctor name, or appointment ID...">
                        </div>

                        <div class="tab-content">
                            <!-- All Tab -->
                            <div class="tab-pane fade show active" id="all">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="appointmentsTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Patient</th>
                                                <th>Doctor</th>
                                                <th>Specialty</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($appointments as $apt): ?>
                                                <tr>
                                                    <td>#<?= $apt['appointment_id'] ?></td>
                                                    <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                    <td><?= htmlspecialchars($apt['doctor_name']) ?></td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            <?= htmlspecialchars($apt['specialty']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                    <td><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $apt['status'] === 'pending' ? 'warning' : ($apt['status'] === 'confirmed' ? 'info' : ($apt['status'] === 'completed' ? 'success' : 'danger')) ?>">
                                                            <?= ucfirst($apt['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           target="_blank">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Pending Tab -->
                            <div class="tab-pane fade" id="pending">
                                <?php if (empty($grouped['pending'])): ?>
                                    <div class="alert alert-info">No pending appointments</div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Patient</th>
                                                    <th>Doctor</th>
                                                    <th>Date & Time</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($grouped['pending'] as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td><?= htmlspecialchars($apt['doctor_name']) ?></td>
                                                        <td>
                                                            <?= date('M d, Y', strtotime($apt['slot_date'])) ?> 
                                                            at <?= date('h:i A', strtotime($apt['start_time'])) ?>
                                                        </td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary"
                                                               target="_blank">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Confirmed Tab -->
                            <div class="tab-pane fade" id="confirmed">
                                <?php if (empty($grouped['confirmed'])): ?>
                                    <div class="alert alert-info">No confirmed appointments</div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Patient</th>
                                                    <th>Doctor</th>
                                                    <th>Date & Time</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($grouped['confirmed'] as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td><?= htmlspecialchars($apt['doctor_name']) ?></td>
                                                        <td>
                                                            <?= date('M d, Y', strtotime($apt['slot_date'])) ?> 
                                                            at <?= date('h:i A', strtotime($apt['start_time'])) ?>
                                                        </td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary"
                                                               target="_blank">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Completed Tab -->
                            <div class="tab-pane fade" id="completed">
                                <?php if (empty($grouped['completed'])): ?>
                                    <div class="alert alert-info">No completed appointments</div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Patient</th>
                                                    <th>Doctor</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($grouped['completed'] as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td><?= htmlspecialchars($apt['doctor_name']) ?></td>
                                                        <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary"
                                                               target="_blank">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Cancelled Tab -->
                            <div class="tab-pane fade" id="cancelled">
                                <?php if (empty($grouped['cancelled'])): ?>
                                    <div class="alert alert-info">No cancelled appointments</div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Patient</th>
                                                    <th>Doctor</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($grouped['cancelled'] as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td><?= htmlspecialchars($apt['doctor_name']) ?></td>
                                                        <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary"
                                                               target="_blank">View</a>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#appointmentsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>