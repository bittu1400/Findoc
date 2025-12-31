<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .page-header { background: #FFC629; padding: 60px 0; }
        .status-pending { background-color: #FFF3CD; color: #856404; }
        .status-confirmed { background-color: #D1ECF1; color: #0C5460; }
        .status-completed { background-color: #D4EDDA; color: #155724; }
        .status-cancelled { background-color: #F8D7DA; color: #721C24; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">FINDOC</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/doctors">Find Doctors</a></li>
                    <li class="nav-item"><a class="nav-link" href="/appointments">My Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Appointment Details</h1>
            <p class="lead">View your appointment information</p>
        </div>
    </div>

    <div class="container my-5">
        <?php if (isset($_SESSION['flash']['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash']['info'])): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['flash']['info']); unset($_SESSION['flash']['info']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Appointment Info Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Appointment Information</h5>
                        <span class="badge status-<?= $appointment['status'] ?> px-3 py-2">
                            <?= ucfirst($appointment['status']) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Appointment ID</div>
                            <div class="col-sm-8 fw-bold">#<?= $appointment['appointment_id'] ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Date</div>
                            <div class="col-sm-8 fw-bold">
                                <?= date('l, F d, Y', strtotime($appointment['slot_date'])) ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Time</div>
                            <div class="col-sm-8 fw-bold">
                                <?= date('h:i A', strtotime($appointment['start_time'])) ?> - 
                                <?= date('h:i A', strtotime($appointment['end_time'])) ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Booked On</div>
                            <div class="col-sm-8">
                                <?= date('M d, Y h:i A', strtotime($appointment['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Info Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Doctor Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 70px; height: 70px; font-size: 28px; font-weight: 700; color: #FFC629;">
                                <?= strtoupper(substr($appointment['doctor_name'], 0, 1)) ?>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1"><?= htmlspecialchars($appointment['doctor_name']) ?></h4>
                                <p class="text-muted mb-0"><?= htmlspecialchars($appointment['specialty']) ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-4 text-muted">Clinic</div>
                            <div class="col-sm-8"><?= htmlspecialchars($appointment['clinic_name']) ?></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-4 text-muted">Phone</div>
                            <div class="col-sm-8"><?= htmlspecialchars($appointment['doctor_phone']) ?></div>
                        </div>
                    </div>
                </div>

                <?php if ($_SESSION['user_role'] === 'doctor'): ?>
                    <!-- Patient Info Card (for doctors) -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Patient Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Name</div>
                                <div class="col-sm-8 fw-bold"><?= htmlspecialchars($appointment['patient_name']) ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-4 text-muted">Phone</div>
                                <div class="col-sm-8"><?= htmlspecialchars($appointment['patient_phone']) ?></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 text-muted">Email</div>
                                <div class="col-sm-8"><?= htmlspecialchars($appointment['patient_email']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Payment Info -->
                <?php if ($payment): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Payment Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status</span>
                                <span class="badge bg-<?= $payment['status'] === 'success' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($payment['status']) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Amount</span>
                                <span class="fw-bold"><?= $payment['currency'] ?> <?= number_format($payment['amount'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Gateway</span>
                                <span><?= ucfirst($payment['gateway']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Transaction ID</span>
                                <small><?= htmlspecialchars($payment['transaction_reference']) ?></small>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm mb-4 border-warning">
                        <div class="card-body text-center">
                            <h5 class="text-warning">⚠️ Payment Pending</h5>
                            <p class="text-muted">Please complete payment to confirm appointment</p>
                            <a href="/appointments/<?= $appointment['appointment_id'] ?>/payment" 
                               class="btn btn-warning">
                                Complete Payment
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Actions Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <?php if ($_SESSION['user_role'] === 'patient'): ?>
                            <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                                <?php 
                                $appointmentDateTime = strtotime($appointment['slot_date'] . ' ' . $appointment['start_time']);
                                if ($appointmentDateTime > time()): 
                                ?>
                                    <button class="btn btn-danger" onclick="cancelAppointment()">
                                        Cancel Appointment
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($appointment['status'] === 'completed'): ?>
                                <a href="/appointments/<?= $appointment['appointment_id'] ?>/review" 
                                   class="btn btn-primary">
                                    Write Review
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($_SESSION['user_role'] === 'doctor'): ?>
                            <?php if ($appointment['status'] === 'pending'): ?>
                                <button class="btn btn-success" onclick="confirmAppointment()">
                                    Confirm Appointment
                                </button>
                            <?php endif; ?>

                            <?php if ($appointment['status'] === 'confirmed'): ?>
                                <button class="btn btn-primary" onclick="completeAppointment()">
                                    Mark as Completed
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <a href="/appointments" class="btn btn-outline-secondary">
                            Back to Appointments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '<?= \App\Core\Auth::generateCsrfToken() ?>';
        const appointmentId = <?= $appointment['appointment_id'] ?>;

        function cancelAppointment() {
            if (!confirm('Are you sure you want to cancel this appointment?')) return;

            fetch(`/appointments/${appointmentId}/cancel`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `csrf_token=${csrfToken}`
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) location.reload();
            });
        }

        function confirmAppointment() {
            fetch(`/appointments/${appointmentId}/confirm`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `csrf_token=${csrfToken}`
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) location.reload();
            });
        }

        function completeAppointment() {
            fetch(`/appointments/${appointmentId}/complete`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `csrf_token=${csrfToken}`
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) location.reload();
            });
        }
    </script>
</body>
</html>