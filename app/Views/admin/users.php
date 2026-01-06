<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - FINDOC</title>
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
                <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                <a class="nav-link active" href="/admin/users">Users</a>
                <a class="nav-link" href="/admin/doctors">Doctors</a>
                <a class="nav-link" href="/admin/appointments">Appointments</a>
                <a class="nav-link" href="/admin/payments">Payments</a>
                <a class="nav-link" href="/admin/reviews">Reviews</a>
                <a class="nav-link" href="/">Home</a>
                <a class="nav-link" href="/logout">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 bg-light">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <h4 class="mb-0">User Management</h4>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <!-- Filter Buttons -->
                <div class="btn-group mb-4" role="group">
                    <a href="/admin/users" class="btn btn-<?= empty($selectedRole) ? 'primary' : 'outline-primary' ?>">
                        All Users
                    </a>
                    <a href="/admin/users?role=patient" class="btn btn-<?= $selectedRole === 'patient' ? 'primary' : 'outline-primary' ?>">
                        Patients
                    </a>
                    <a href="/admin/users?role=doctor" class="btn btn-<?= $selectedRole === 'doctor' ? 'primary' : 'outline-primary' ?>">
                        Doctors
                    </a>
                    <a href="/admin/users?role=admin" class="btn btn-<?= $selectedRole === 'admin' ? 'primary' : 'outline-primary' ?>">
                        Admins
                    </a>
                </div>

                <!-- Users Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <?= $selectedRole ? ucfirst($selectedRole) . 's' : 'All Users' ?> 
                            (<?= count($users) ?>)
                        </h5>
                        <input type="text" id="searchInput" class="form-control w-25" placeholder="Search users...">
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= $user['user_id'] ?></td>
                                            <td><?= htmlspecialchars($user['name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'doctor' ? 'success' : 'primary') ?>">
                                                    <?= ucfirst($user['role']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($user['registered_date'])) ?></td>
                                            <td>
                                                <?php if ($user['role'] === 'doctor'): ?>
                                                    <a href="/doctors/<?= $user['user_id'] ?>" 
                                                       class="btn btn-sm btn-outline-info"
                                                       target="_blank">
                                                        View Profile
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteUser(<?= $user['user_id'] ?>, '<?= htmlspecialchars($user['name']) ?>')">
                                                        Delete
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-primary"><?= count(array_filter($users, fn($u) => $u['role'] === 'patient')) ?></h3>
                                <p class="text-muted mb-0">Total Patients</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-success"><?= count(array_filter($users, fn($u) => $u['role'] === 'doctor')) ?></h3>
                                <p class="text-muted mb-0">Total Doctors</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-danger"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></h3>
                                <p class="text-muted mb-0">Total Admins</p>
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
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Delete user
        function deleteUser(userId, userName) {
            if (!confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                return;
            }

            const csrfToken = '<?= $csrf_token ?>';

            fetch('/admin/users/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `csrf_token=${csrfToken}&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>
</html>