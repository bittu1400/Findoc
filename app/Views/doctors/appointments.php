<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - FINDOC</title>
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
                <a class="nav-link" href="/doctor/profile">👤 Profile</a>
                <a class="nav-link" href="/doctor/availability">📅 Availability</a>
                <a class="nav-link active" href="/doctor/appointments">📋 Appointments</a>
                <a class="nav-link" href="/">🏠 Home</a>
                <a class="nav-link" href="/logout">🚪 Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <h5 class="mb-0">All Appointments</h5>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <!-- Filter Tabs -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#pending">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#confirmed">Confirmed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#completed">Completed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#cancelled">Cancelled</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="all">
                        <?php if (empty($appointments)): ?>
                            <div class="alert alert-info">
                                No appointments found.
                            </div>
                        <?php else: ?>
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Patient</th>
                                                    <th>Contact</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($appointments as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                        <td><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td>
                                                            <small><?= htmlspecialchars($apt['patient_phone']) ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $apt['status'] === 'pending' ? 'warning' : ($apt['status'] === 'confirmed' ? 'info' : ($apt['status'] === 'completed' ? 'success' : 'danger')) ?>">
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
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="pending">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Contact</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $pending = array_filter($appointments, fn($a) => $a['status'] === 'pending');
                                            if (empty($pending)): ?>
                                                <tr><td colspan="6" class="text-center text-muted">No pending appointments</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($pending as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                        <td><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td><small><?= htmlspecialchars($apt['patient_phone']) ?></small></td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="confirmed">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Contact</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $confirmed = array_filter($appointments, fn($a) => $a['status'] === 'confirmed');
                                            if (empty($confirmed)): ?>
                                                <tr><td colspan="6" class="text-center text-muted">No confirmed appointments</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($confirmed as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                        <td><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td><small><?= htmlspecialchars($apt['patient_phone']) ?></small></td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="completed">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $completed = array_filter($appointments, fn($a) => $a['status'] === 'completed');
                                            if (empty($completed)): ?>
                                                <tr><td colspan="5" class="text-center text-muted">No completed appointments</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($completed as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                        <td><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="cancelled">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $cancelled = array_filter($appointments, fn($a) => $a['status'] === 'cancelled');
                                            if (empty($cancelled)): ?>
                                                <tr><td colspan="5" class="text-center text-muted">No cancelled appointments</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($cancelled as $apt): ?>
                                                    <tr>
                                                        <td>#<?= $apt['appointment_id'] ?></td>
                                                        <td><?= date('M d, Y', strtotime($apt['slot_date'])) ?></td>
                                                        <td><?= date('h:i A', strtotime($apt['start_time'])) ?></td>
                                                        <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                                                        <td>
                                                            <a href="/appointments/<?= $apt['appointment_id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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