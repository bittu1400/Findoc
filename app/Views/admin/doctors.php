<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - FINDOC</title>
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
                <a class="nav-link active" href="/admin/doctors">👨‍⚕️ Doctors</a>
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
                    <h4 class="mb-0">Doctor Management</h4>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <!-- Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="text-success"><?= count($doctors) ?></h3>
                                <p class="text-muted mb-0">Total Doctors</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="text-info"><?= count(array_unique(array_column($doctors, 'specialty'))) ?></h3>
                                <p class="text-muted mb-0">Specialties</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="text-warning">
                                    <?= number_format(array_sum(array_column($doctors, 'average_rating')) / max(count($doctors), 1), 1) ?>
                                </h3>
                                <p class="text-muted mb-0">Avg Rating</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="text-primary">
                                    <?= array_sum(array_column($doctors, 'review_count')) ?>
                                </h3>
                                <p class="text-muted mb-0">Total Reviews</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctors Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Doctors</h5>
                        <div class="d-flex gap-2">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search doctors..." style="width: 250px;">
                            <select id="specialtyFilter" class="form-select" style="width: 200px;">
                                <option value="">All Specialties</option>
                                <?php 
                                $specialties = array_unique(array_column($doctors, 'specialty'));
                                sort($specialties);
                                foreach ($specialties as $spec): 
                                ?>
                                    <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="doctorsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Specialty</th>
                                        <th>Experience</th>
                                        <th>Fee</th>
                                        <th>Rating</th>
                                        <th>Reviews</th>
                                        <th>Clinic</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <tr>
                                            <td><?= $doctor['doctor_id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($doctor['name']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($doctor['email']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($doctor['specialty'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td><?= $doctor['experience_years'] ?> years</td>
                                            <td>$<?= number_format($doctor['consultation_fee'], 2) ?></td>
                                            <td>
                                                <span class="text-warning">★</span>
                                                <?= number_format($doctor['average_rating'], 1) ?>
                                            </td>
                                            <td><?= $doctor['review_count'] ?></td>
                                            <td><?= htmlspecialchars($doctor['clinic_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/doctors/<?= $doctor['doctor_id'] ?>" 
                                                       class="btn btn-outline-primary"
                                                       target="_blank">
                                                        View
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Doctors -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3">Top Rated Doctors</h5>
                    </div>
                    <?php 
                    $topDoctors = $doctors;
                    usort($topDoctors, fn($a, $b) => $b['average_rating'] <=> $a['average_rating']);
                    $topDoctors = array_slice($topDoctors, 0, 3);
                    foreach ($topDoctors as $doctor): 
                    ?>
                        <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 60px; height: 60px; font-size: 24px; font-weight: 700; color: #FFC629;">
                                            <?= strtoupper(substr($doctor['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= htmlspecialchars($doctor['name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($doctor['specialty']) ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="text-warning fw-bold">
                                                ★ <?= number_format($doctor['average_rating'], 1) ?>
                                            </div>
                                            <small class="text-muted"><?= $doctor['review_count'] ?> reviews</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold"><?= $doctor['experience_years'] ?> yrs</div>
                                            <small class="text-muted">Experience</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('specialtyFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const specialty = document.getElementById('specialtyFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#doctorsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesSearch = text.includes(searchTerm);
                const matchesSpecialty = !specialty || text.includes(specialty);
                row.style.display = (matchesSearch && matchesSpecialty) ? '' : 'none';
            });
        }
    </script>
</body>
</html>