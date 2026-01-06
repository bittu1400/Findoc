<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Review - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .page-header { background: #FFC629; padding: 60px 0; }
        .star-rating { font-size: 40px; cursor: pointer; }
        .star-rating .star { color: #ddd; transition: color 0.2s; }
        .star-rating .star.selected,
        .star-rating .star:hover,
        .star-rating .star:hover ~ .star { color: #FFC629; }
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
            <h1 class="display-5 fw-bold">Write a Review</h1>
            <p class="lead">Share your experience with Dr. <?= htmlspecialchars($appointment['doctor_name']) ?></p>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (isset($_SESSION['flash']['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['flash']['errors'] as $field => $errors): ?>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash']['errors']); ?>
                <?php endif; ?>

                <!-- Appointment Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; font-size: 24px; font-weight: 700; color: #FFC629;">
                                <?= strtoupper(substr($appointment['doctor_name'], 0, 1)) ?>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-1"><?= htmlspecialchars($appointment['doctor_name']) ?></h5>
                                <p class="text-muted mb-0">
                                    <?= htmlspecialchars($appointment['specialty']) ?> • 
                                    <?= date('M d, Y', strtotime($appointment['slot_date'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Form -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Your Review</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/appointments/<?= $appointment['appointment_id'] ?>/review">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="rating" id="ratingInput" value="">

                            <!-- Star Rating -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Rate Your Experience</label>
                                <div class="star-rating" id="starRating">
                                    <span class="star" data-rating="5">★</span>
                                    <span class="star" data-rating="4">★</span>
                                    <span class="star" data-rating="3">★</span>
                                    <span class="star" data-rating="2">★</span>
                                    <span class="star" data-rating="1">★</span>
                                </div>
                                <small class="text-muted">Click a star to rate (1 = Poor, 5 = Excellent)</small>
                                <div id="ratingError" class="text-danger small mt-1" style="display: none;">
                                    Please select a rating
                                </div>
                            </div>

                            <!-- Review Comment -->
                            <div class="mb-4">
                                <label for="comment" class="form-label fw-bold">Your Review (Optional)</label>
                                <textarea 
                                    class="form-control" 
                                    id="comment" 
                                    name="comment" 
                                    rows="6"
                                    placeholder="Share your experience with this doctor. How was the consultation? Would you recommend them?"
                                    maxlength="1000"><?= htmlspecialchars($_SESSION['old']['comment'] ?? '') ?></textarea>
                                <small class="text-muted">Maximum 1000 characters</small>
                            </div>

                            <!-- Tips -->
                            <div class="alert alert-info">
                                <h6 class="alert-heading">💡 Tips for writing a helpful review:</h6>
                                <ul class="mb-0 small">
                                    <li>Be honest and specific about your experience</li>
                                    <li>Mention what you liked or didn't like</li>
                                    <li>Keep it respectful and constructive</li>
                                    <li>Focus on the doctor's professionalism and care quality</li>
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    Submit Review
                                </button>
                                <a href="/appointments/<?= $appointment['appointment_id'] ?>" 
                                   class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Review Guidelines -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Review Guidelines</h6>
                        <p class="small text-muted mb-2">
                            Reviews help other patients make informed decisions. Please ensure your review:
                        </p>
                        <ul class="small text-muted mb-0">
                            <li>Is based on your personal experience</li>
                            <li>Does not contain offensive or inappropriate language</li>
                            <li>Does not share personal medical information</li>
                            <li>Complies with healthcare privacy regulations</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('ratingInput');
        const ratingError = document.getElementById('ratingError');
        let selectedRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                ratingInput.value = selectedRating;
                ratingError.style.display = 'none';
                updateStars();
            });

            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                stars.forEach(s => {
                    s.classList.toggle('selected', parseInt(s.dataset.rating) <= rating);
                });
            });
        });

        document.getElementById('starRating').addEventListener('mouseleave', updateStars);

        function updateStars() {
            stars.forEach(star => {
                star.classList.toggle('selected', parseInt(star.dataset.rating) <= selectedRating);
            });
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!ratingInput.value) {
                e.preventDefault();
                ratingError.style.display = 'block';
                document.getElementById('starRating').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Character counter
        const textarea = document.getElementById('comment');
        const maxLength = 1000;
        
        textarea.addEventListener('input', function() {
            const remaining = maxLength - this.value.length;
            const counter = this.nextElementSibling;
            counter.textContent = `${remaining} characters remaining`;
            counter.classList.toggle('text-danger', remaining < 100);
        });
    </script>

    <?php unset($_SESSION['old']); ?>
</body>
</html> 