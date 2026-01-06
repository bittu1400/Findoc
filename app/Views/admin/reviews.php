<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .sidebar { min-height: 100vh; background: #1a1a1a; }
        .sidebar .nav-link { color: #fff; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #FFC629; color: #000; }
        .review-card { transition: all 0.2s; }
        .review-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
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
                <a class="nav-link" href="/admin/payments">💰 Payments</a>
                <a class="nav-link active" href="/admin/reviews">⭐ Reviews</a>
                <a class="nav-link" href="/">🏠 Home</a>
                <a class="nav-link" href="/logout">🚪 Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 bg-light">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <h4 class="mb-0">Review Management</h4>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <!-- Review Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="mb-1"><?= count($reviews) ?></h3>
                                <small class="text-muted">Total Reviews</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card shadow-sm text-center border-warning">
                            <div class="card-body">
                                <h3 class="text-warning mb-1">
                                    <?= count(array_filter($reviews, fn($r) => $r['rating'] == 5)) ?>
                                </h3>
                                <small class="text-muted">5 Stars</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="mb-1">
                                    <?= count(array_filter($reviews, fn($r) => $r['rating'] == 4)) ?>
                                </h3>
                                <small class="text-muted">4 Stars</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="mb-1">
                                    <?= count(array_filter($reviews, fn($r) => $r['rating'] == 3)) ?>
                                </h3>
                                <small class="text-muted">3 Stars</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="mb-1">
                                    <?= count(array_filter($reviews, fn($r) => $r['rating'] == 2)) ?>
                                </h3>
                                <small class="text-muted">2 Stars</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card shadow-sm text-center border-danger">
                            <div class="card-body">
                                <h3 class="text-danger mb-1">
                                    <?= count(array_filter($reviews, fn($r) => $r['rating'] == 1)) ?>
                                </h3>
                                <small class="text-muted">1 Star</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" id="searchInput" class="form-control" 
                                       placeholder="Search by patient, doctor, or comment...">
                            </div>
                            <div class="col-md-3">
                                <select id="ratingFilter" class="form-select">
                                    <option value="">All Ratings</option>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sortOrder" class="form-select">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="highest">Highest Rating</option>
                                    <option value="lowest">Lowest Rating</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                    Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div id="reviewsContainer" class="row g-3">
                    <?php foreach ($reviews as $review): ?>
                        <div class="col-12 review-item" 
                             data-rating="<?= $review['rating'] ?>"
                             data-date="<?= strtotime($review['review_date']) ?>">
                            <div class="card shadow-sm review-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <?= htmlspecialchars($review['patient_name']) ?>
                                                        <span class="text-warning ms-2">
                                                            <?= str_repeat('★', $review['rating']) ?><?= str_repeat('☆', 5 - $review['rating']) ?>
                                                        </span>
                                                    </h6>
                                                    <small class="text-muted">
                                                        For Dr. <?= htmlspecialchars($review['doctor_name']) ?> 
                                                        (<?= htmlspecialchars($review['specialty']) ?>)
                                                    </small>
                                                </div>
                                                <small class="text-muted">
                                                    <?= date('M d, Y', strtotime($review['review_date'])) ?>
                                                </small>
                                            </div>
                                            
                                            <?php if (!empty($review['comment'])): ?>
                                                <p class="mb-2 text-secondary">
                                                    "<?= htmlspecialchars($review['comment']) ?>"
                                                </p>
                                            <?php else: ?>
                                                <p class="mb-2 text-muted fst-italic">
                                                    No comment provided
                                                </p>
                                            <?php endif; ?>

                                            <div class="d-flex gap-2 mt-3">
                                                <a href="/appointments/<?= $review['appointment_id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   target="_blank">
                                                    View Appointment
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteReview(<?= $review['review_id'] ?>, '<?= htmlspecialchars($review['patient_name']) ?>')">
                                                    Delete Review
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="border rounded p-3 bg-light">
                                                <h6 class="mb-2">Review Stats</h6>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Review ID:</small>
                                                    <small><strong>#<?= $review['review_id'] ?></strong></small>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Appointment:</small>
                                                    <small><strong>#<?= $review['appointment_id'] ?></strong></small>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Rating:</small>
                                                    <small><strong><?= $review['rating'] ?>/5</strong></small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Has Comment:</small>
                                                    <small><strong><?= !empty($review['comment']) ? 'Yes' : 'No' ?></strong></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($reviews)): ?>
                    <div class="alert alert-info text-center">
                        <h5>No Reviews Yet</h5>
                        <p class="mb-0">Reviews will appear here once patients start rating their appointments.</p>
                    </div>
                <?php endif; ?>

                <!-- Average Rating Card -->
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Overall Platform Rating</h5>
                            </div>
                            <div class="card-body text-center">
                                <h1 class="display-3 text-warning mb-3">
                                    <?php 
                                    $avgRating = count($reviews) > 0 ? array_sum(array_column($reviews, 'rating')) / count($reviews) : 0;
                                    echo number_format($avgRating, 1);
                                    ?>
                                </h1>
                                <div class="text-warning fs-4 mb-2">
                                    <?= str_repeat('★', round($avgRating)) ?><?= str_repeat('☆', 5 - round($avgRating)) ?>
                                </div>
                                <p class="text-muted">Based on <?= count($reviews) ?> reviews</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Rating Distribution</h5>
                            </div>
                            <div class="card-body">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <?php 
                                    $count = count(array_filter($reviews, fn($r) => $r['rating'] == $i));
                                    $percentage = count($reviews) > 0 ? ($count / count($reviews)) * 100 : 0;
                                    ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="me-2" style="width: 60px;"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></span>
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar bg-warning" 
                                                 style="width: <?= $percentage ?>%">
                                                <?= $count ?>
                                            </div>
                                        </div>
                                        <span class="ms-2" style="width: 50px;"><?= round($percentage) ?>%</span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const reviewItems = document.querySelectorAll('.review-item');

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', filterReviews);
        document.getElementById('ratingFilter').addEventListener('change', filterReviews);
        document.getElementById('sortOrder').addEventListener('change', sortReviews);

        function filterReviews() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const ratingFilter = document.getElementById('ratingFilter').value;

            reviewItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                const rating = item.dataset.rating;
                
                const matchesSearch = text.includes(searchTerm);
                const matchesRating = !ratingFilter || rating === ratingFilter;
                
                item.style.display = (matchesSearch && matchesRating) ? '' : 'none';
            });
        }

        function sortReviews() {
            const container = document.getElementById('reviewsContainer');
            const items = Array.from(reviewItems);
            const sortOrder = document.getElementById('sortOrder').value;

            items.sort((a, b) => {
                switch(sortOrder) {
                    case 'newest':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    case 'oldest':
                        return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                    case 'highest':
                        return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
                    case 'lowest':
                        return parseInt(a.dataset.rating) - parseInt(b.dataset.rating);
                }
            });

            items.forEach(item => container.appendChild(item));
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('ratingFilter').value = '';
            document.getElementById('sortOrder').value = 'newest';
            filterReviews();
            sortReviews();
        }

        function deleteReview(reviewId, patientName) {
            if (!confirm(`Are you sure you want to delete the review by "${patientName}"? This action cannot be undone.`)) {
                return;
            }

            const csrfToken = '<?= $csrf_token ?>';

            fetch('/admin/reviews/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `csrf_token=${csrfToken}&review_id=${reviewId}`
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