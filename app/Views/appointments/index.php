<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - FINDOC</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .page-header {
            background: var(--primary-color);
            padding: 40px 20px;
            text-align: center;
        }
        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 32px;
            border-bottom: 2px solid var(--border-color);
        }
        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            font-weight: 600;
            color: var(--text-light);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: var(--transition);
        }
        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        .appointments-list {
            display: grid;
            gap: 20px;
        }
        .appointment-card {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 24px;
            align-items: center;
        }
        .appointment-date {
            text-align: center;
            padding: 16px;
            background: var(--accent-color);
            border-radius: 8px;
            min-width: 100px;
        }
        .date-month {
            font-size: 14px;
            color: var(--text-light);
            text-transform: uppercase;
        }
        .date-day {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .date-time {
            font-size: 14px;
            font-weight: 600;
            margin-top: 8px;
        }
        .appointment-info {
            flex: 1;
        }
        .doctor-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .appointment-details {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 12px;
        }
        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background: #FFF3CD;
            color: #856404;
        }
        .status-confirmed {
            background: #D1ECF1;
            color: #0C5460;
        }
        .status-completed {
            background: #D4EDDA;
            color: #155724;
        }
        .status-cancelled {
            background: #F8D7DA;
            color: #721C24;
        }
        .appointment-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            text-align: center;
            white-space: nowrap;
        }
        .btn-cancel {
            background: var(--white);
            border: 1px solid var(--error);
            color: var(--error);
        }
        .btn-cancel:hover {
            background: var(--error);
            color: var(--white);
        }
        .btn-review {
            background: var(--primary-color);
            color: var(--text-dark);
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 12px;
        }
        @media (max-width: 768px) {
            .appointment-card {
                grid-template-columns: 1fr;
            }
            .appointment-actions {
                flex-direction: row;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav style="background: var(--white); padding: 16px 20px; box-shadow: var(--shadow);">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <a href="/" style="font-size: 24px; font-weight: 700; color: var(--primary-color);">FINDOC</a>
            <div style="display: flex; gap: 24px; align-items: center;">
                <a href="/doctors">Find Doctors</a>
                <a href="/appointments" style="color: var(--primary-color);">My Appointments</a>
                <a href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <h1>My Appointments</h1>
        <p>Manage your healthcare appointments</p>
    </div>

    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div class="container">
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['flash']['success']); unset($_SESSION['flash']['success']); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="container">
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="filterAppointments('all')">All</button>
            <button class="tab" onclick="filterAppointments('upcoming')">Upcoming</button>
            <button class="tab" onclick="filterAppointments('completed')">Completed</button>
            <button class="tab" onclick="filterAppointments('cancelled')">Cancelled</button>
        </div>

        <!-- Appointments List -->
        <?php if (empty($appointments)): ?>
            <div class="empty-state">
                <h3>No Appointments Yet</h3>
                <p>You haven't booked any appointments. Find a doctor to get started.</p>
                <a href="/doctors" class="btn btn-primary" style="margin-top: 20px; padding: 12px 32px;">
                    Find Doctors
                </a>
            </div>
        <?php else: ?>
            <div class="appointments-list" id="appointmentsList">
                <?php foreach ($appointments as $appointment): ?>
                    <div class="appointment-card" data-status="<?= $appointment['status'] ?>">
                        <!-- Date Column -->
                        <div class="appointment-date">
                            <div class="date-month"><?= date('M', strtotime($appointment['slot_date'])) ?></div>
                            <div class="date-day"><?= date('d', strtotime($appointment['slot_date'])) ?></div>
                            <div class="date-time"><?= date('h:i A', strtotime($appointment['start_time'])) ?></div>
                        </div>

                        <!-- Info Column -->
                        <div class="appointment-info">
                            <h3 class="doctor-name"><?= htmlspecialchars($appointment['doctor_name']) ?></h3>
                            <div class="appointment-details">
                                <div class="detail-item">
                                    <strong>Specialty:</strong> <?= htmlspecialchars($appointment['specialty'] ?? 'General') ?>
                                </div>
                                <div class="detail-item">
                                    <strong>Clinic:</strong> <?= htmlspecialchars($appointment['clinic_name'] ?? 'N/A') ?>
                                </div>
                                <div class="detail-item">
                                    <strong>Fee:</strong> $<?= number_format($appointment['consultation_fee'], 2) ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?= $appointment['status'] ?>">
                                <?= ucfirst($appointment['status']) ?>
                            </span>
                        </div>

                        <!-- Actions Column -->
                        <div class="appointment-actions">
                            <a href="/appointments/<?= $appointment['appointment_id'] ?>" 
                               class="btn btn-primary btn-small">
                                View Details
                            </a>

                            <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                                <?php 
                                $appointmentDateTime = strtotime($appointment['slot_date'] . ' ' . $appointment['start_time']);
                                $canCancel = $appointmentDateTime > time();
                                ?>
                                <?php if ($canCancel): ?>
                                    <button onclick="cancelAppointment(<?= $appointment['appointment_id'] ?>)" 
                                            class="btn btn-cancel btn-small">
                                        Cancel
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($appointment['status'] === 'completed' && !$appointment['has_review']): ?>
                                <a href="/appointments/<?= $appointment['appointment_id'] ?>/review" 
                                   class="btn btn-review btn-small">
                                    Write Review
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function filterAppointments(filter) {
            // Update tab active state
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');

            // Filter appointments
            const appointments = document.querySelectorAll('.appointment-card');
            appointments.forEach(card => {
                const status = card.dataset.status;
                
                if (filter === 'all') {
                    card.style.display = 'grid';
                } else if (filter === 'upcoming') {
                    card.style.display = (status === 'pending' || status === 'confirmed') ? 'grid' : 'none';
                } else {
                    card.style.display = status === filter ? 'grid' : 'none';
                }
            });
        }

        function cancelAppointment(appointmentId) {
            if (!confirm('Are you sure you want to cancel this appointment?')) {
                return;
            }

            const csrfToken = '<?= \App\Core\Auth::generateCsrfToken() ?>';

            fetch(`/appointments/${appointmentId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `csrf_token=${csrfToken}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>
</html>