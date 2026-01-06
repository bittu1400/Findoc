<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - FINDOC</title>
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
                <a class="nav-link" href="/admin/appointments">📋 Appointments</a>
                <a class="nav-link active" href="/admin/payments">💰 Payments</a>
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
                    <h4 class="mb-0">Payment Management</h4>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <!-- Payment Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-success">
                            <div class="card-body">
                                <h4 class="text-success mb-1">$<?= number_format($stats['total_revenue'], 2) ?></h4>
                                <p class="text-muted mb-0 small">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-primary">
                            <div class="card-body">
                                <h4 class="text-primary mb-1"><?= $stats['successful_count'] ?></h4>
                                <p class="text-muted mb-0 small">Successful Payments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-danger">
                            <div class="card-body">
                                <h4 class="text-danger mb-1"><?= $stats['failed_count'] ?></h4>
                                <p class="text-muted mb-0 small">Failed Payments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center border-warning">
                            <div class="card-body">
                                <h4 class="text-warning mb-1"><?= $stats['pending_count'] ?></h4>
                                <p class="text-muted mb-0 small">Pending Payments</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Payments</h5>
                        <div class="d-flex gap-2">
                            <input type="text" id="searchInput" class="form-control" 
                                   placeholder="Search payments..." style="width: 250px;">
                            <select id="statusFilter" class="form-select" style="width: 150px;">
                                <option value="">All Status</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                                <option value="initiated">Initiated</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="paymentsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Appointment</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Amount</th>
                                        <th>Gateway</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td>#<?= $payment['payment_id'] ?></td>
                                            <td>
                                                <a href="/appointments/<?= $payment['appointment_id'] ?>" target="_blank">
                                                    #<?= $payment['appointment_id'] ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($payment['patient_name']) ?></td>
                                            <td>
                                                <?= htmlspecialchars($payment['doctor_name']) ?>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($payment['specialty']) ?></small>
                                            </td>
                                            <td>
                                                <strong><?= $payment['currency'] ?> <?= number_format($payment['amount'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= ucfirst($payment['gateway']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted font-monospace">
                                                    <?= htmlspecialchars($payment['transaction_reference']) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $payment['status'] === 'success' ? 'success' : ($payment['status'] === 'failed' ? 'danger' : ($payment['status'] === 'refunded' ? 'warning' : 'secondary')) ?>">
                                                    <?= ucfirst($payment['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('M d, Y', strtotime($payment['created_at'])) ?>
                                                <br>
                                                <small class="text-muted"><?= date('h:i A', strtotime($payment['created_at'])) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart (Placeholder) -->
                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Revenue Overview</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <strong>💡 Note:</strong> Revenue analytics and charts can be implemented using Chart.js or similar libraries.
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h5 class="text-success">$<?= number_format($stats['total_revenue'] / max(1, date('d')), 2) ?></h5>
                                            <small class="text-muted">Daily Average</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h5 class="text-primary">$<?= number_format($stats['total_revenue'] / max(1, $stats['successful_count']), 2) ?></h5>
                                            <small class="text-muted">Average Transaction</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h5 class="text-warning"><?= $stats['successful_count'] > 0 ? round(($stats['successful_count'] / ($stats['successful_count'] + $stats['failed_count'])) * 100, 1) : 0 ?>%</h5>
                                            <small class="text-muted">Success Rate</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Payment Methods</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $gateways = [];
                                foreach ($payments as $p) {
                                    if ($p['status'] === 'success') {
                                        $gateways[$p['gateway']] = ($gateways[$p['gateway']] ?? 0) + 1;
                                    }
                                }
                                arsort($gateways);
                                ?>
                                <?php foreach ($gateways as $gateway => $count): ?>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span><?= ucfirst($gateway) ?></span>
                                        <strong><?= $count ?> transactions</strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search and filter functionality
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#paymentsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !status || text.includes(status);
                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }
    </script>
</body>
</html>