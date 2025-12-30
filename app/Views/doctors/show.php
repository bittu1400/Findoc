<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($doctor['name']) ?> - FINDOC</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .page-header {
            background: var(--primary-color);
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .profile-header {
            background: var(--white);
            border-radius: 12px;
            padding: 32px;
            box-shadow: var(--shadow);
            margin-top: -60px;
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }
        .doctor-avatar-large {
            width: 150px;
            height: 150px;
            border-radius: 12px;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            font-weight: 700;
            color: var(--primary-color);
            flex-shrink: 0;
        }
        .doctor-main-info {
            flex: 1;
        }
        .doctor-name-large {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .doctor-specialty-large {
            font-size: 18px;
            color: var(--text-light);
            margin-bottom: 16px;
        }
        .rating-large {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .rating-stars {
            color: var(--primary-color);
            font-size: 24px;
        }
        .rating-text {
            font-size: 18px;
            font-weight: 600;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        .info-item {
            padding: 16px;
            background: var(--bg-light);
            border-radius: 8px;
        }
        .info-label {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 8px;
        }
        .info-value {
            font-size: 20px;
            font-weight: 600;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
            margin-top: 32px;
        }
        .content-section {
            background: var(--white);
            border-radius: 12px;
            padding: 32px;
            box-shadow: var(--shadow);
        }
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .review-item {
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .review-item:last-child {
            border-bottom: none;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .reviewer-name {
            font-weight: 600;
        }
        .review-stars {
            color: var(--primary-color);
        }
        .review-date {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 8px;
        }
        .review-comment {
            color: var(--text-light);
            line-height: 1.6;
        }
        .availability-calendar {
            display: grid;
            gap: 12px;
        }
        .date-slot {
            padding: 16px;
            background: var(--bg-light);
            border-radius: 8px;
        }
        .date-label {
            font-weight: 600;
            margin-bottom: 12px;
        }
        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .time-slot {
            padding: 8px 16px;
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
        }
        .empty-message {
            color: var(--text-light);
            text-align: center;
            padding: 40px 20px;
        }
        @media (max-width: 968px) {
            .profile-header {
                flex-direction: column;
            }
            .content-grid {
                grid-template-columns: 1fr;
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] === 'doctor'): ?>
                        <a href="/doctor/dashboard">Dashboard</a>
                    <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin/dashboard">Admin</a>
                    <?php else: ?>
                        <a href="/appointments">My Appointments</a>
                    <?php endif; ?>
                    <a href="/logout">Logout</a>
                <?php else: ?>
                    <a href="/login">Login</a>
                    <a href="/register" class="btn btn-primary" style="padding: 8px 16px;">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="page-header"></div>

    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="doctor-avatar-large">
                <?= strtoupper(substr($doctor['name'], 0, 1)) ?>
            </div>
            <div class="doctor-main-info">
                <h1 class="doctor-name-large"><?= htmlspecialchars($doctor['name']) ?></h1>
                <div class="doctor-specialty-large"><?= htmlspecialchars($doctor['specialty'] ?? 'General Practice') ?></div>
                
                <div class="rating-large">
                    <span class="rating-stars">★★★★★</span>
                    <span class="rating-text">
                        <?= number_format($doctor['average_rating'], 1) ?> 
                        (<?= $doctor['review_count'] ?> reviews)
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Experience</div>
                        <div class="info-value"><?= $doctor['experience_years'] ?> years</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Consultation Fee</div>
                        <div class="info-value">$<?= number_format($doctor['consultation_fee'], 2) ?></div>
                    </div>
                    <?php if (!empty($doctor['clinic_name'])): ?>
                        <div class="info-item">
                            <div class="info-label">Clinic</div>
                            <div class="info-value"><?= htmlspecialchars($doctor['clinic_name']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <a href="/appointments/create/<?= $doctor['doctor_id'] ?>" class="btn btn-primary" style="padding: 14px 32px;">
                    Book Appointment
                </a>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Left Column -->
            <div>
                <!-- About Section -->
                <div class="content-section" style="margin-bottom: 32px;">
                    <h2 class="section-title">About</h2>
                    <?php if (!empty($doctor['description'])): ?>
                        <p style="line-height: 1.8; color: var(--text-light);">
                            <?= nl2br(htmlspecialchars($doctor['description'])) ?>
                        </p>
                    <?php else: ?>
                        <p class="empty-message">No description available.</p>
                    <?php endif; ?>
                </div>

                <!-- Qualifications -->
                <?php if (!empty($doctor['qualifications'])): ?>
                    <div class="content-section" style="margin-bottom: 32px;">
                        <h2 class="section-title">Qualifications</h2>
                        <p style="line-height: 1.8; color: var(--text-light);">
                            <?= nl2br(htmlspecialchars($doctor['qualifications'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Reviews Section -->
                <div class="content-section">
                    <h2 class="section-title">Patient Reviews (<?= count($reviews) ?>)</h2>
                    <?php if (empty($reviews)): ?>
                        <p class="empty-message">No reviews yet. Be the first to review!</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <span class="reviewer-name"><?= htmlspecialchars($review['patient_name']) ?></span>
                                    <span class="review-stars">
                                        <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                                    </span>
                                </div>
                                <div class="review-date">
                                    <?= date('M d, Y', strtotime($review['review_date'])) ?>
                                </div>
                                <?php if (!empty($review['comment'])): ?>
                                    <div class="review-comment">
                                        <?= htmlspecialchars($review['comment']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Availability -->
            <div>
                <div class="content-section">
                    <h2 class="section-title">Available Dates</h2>
                    <?php if (empty($availableDates)): ?>
                        <p class="empty-message">No available slots at the moment.</p>
                    <?php else: ?>
                        <div class="availability-calendar">
                            <?php 
                            $count = 0;
                            foreach ($availableDates as $date => $slots): 
                                if ($count >= 5) break; // Show only next 5 days
                                $count++;
                            ?>
                                <div class="date-slot">
                                    <div class="date-label">
                                        <?= date('l, M d', strtotime($date)) ?>
                                    </div>
                                    <div class="time-slots">
                                        <?php foreach (array_slice($slots, 0, 4) as $slot): ?>
                                            <span class="time-slot">
                                                <?= date('h:i A', strtotime($slot['start_time'])) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($slots) > 4): ?>
                                            <span class="time-slot" style="color: var(--primary-color);">
                                                +<?= count($slots) - 4 ?> more
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/appointments/create/<?= $doctor['doctor_id'] ?>" 
                           class="btn btn-primary btn-full" 
                           style="margin-top: 20px;">
                            View All Slots
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Contact Info -->
                <div class="content-section" style="margin-top: 32px;">
                    <h2 class="section-title">Contact</h2>
                    <div style="line-height: 2;">
                        <?php if (!empty($doctor['phone'])): ?>
                            <div>
                                <strong>Phone:</strong> 
                                <span style="color: var(--text-light);"><?= htmlspecialchars($doctor['phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($doctor['email'])): ?>
                            <div>
                                <strong>Email:</strong> 
                                <span style="color: var(--text-light);"><?= htmlspecialchars($doctor['email']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>