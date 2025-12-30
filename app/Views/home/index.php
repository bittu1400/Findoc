<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINDOC - Find Your Perfect Doctor</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            padding: 80px 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--text-dark);
        }
        .hero p {
            font-size: 20px;
            color: var(--text-light);
            margin-bottom: 40px;
        }
        .search-box {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 12px;
            padding: 12px;
            box-shadow: var(--shadow-lg);
            display: flex;
            gap: 12px;
        }
        .search-box select,
        .search-box input {
            flex: 1;
            padding: 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
        }
        .search-box button {
            padding: 14px 32px;
            background: var(--secondary-color);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 60px;
            flex-wrap: wrap;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--secondary-color);
        }
        .stat-label {
            color: var(--text-light);
            margin-top: 8px;
        }
        .section {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 20px;
        }
        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }
        .section-header h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .section-header p {
            color: var(--text-light);
            font-size: 18px;
        }
        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }
        .doctor-card {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        .doctor-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .doctor-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        .doctor-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .doctor-info h3 {
            font-size: 20px;
            margin-bottom: 4px;
        }
        .specialty {
            color: var(--text-light);
            font-size: 14px;
        }
        .doctor-details {
            margin-bottom: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .detail-label {
            color: var(--text-light);
            font-size: 14px;
        }
        .detail-value {
            font-weight: 600;
        }
        .rating {
            color: var(--primary-color);
            font-weight: 600;
        }
        .btn-view {
            display: block;
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: var(--text-dark);
            text-align: center;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-view:hover {
            background: var(--primary-dark);
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .search-box { flex-direction: column; }
            .doctor-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav style="background: var(--white); padding: 16px 20px; box-shadow: var(--shadow); position: sticky; top: 0; z-index: 100;">
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
                    <a href="/logout" class="btn btn-secondary" style="padding: 8px 16px;">Logout</a>
                <?php else: ?>
                    <a href="/login">Login</a>
                    <a href="/register" class="btn btn-primary" style="padding: 8px 16px;">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Find & Book Your Perfect Doctor</h1>
        <p>Connect with verified healthcare professionals in your area</p>
        
        <form action="/doctors" method="GET" class="search-box">
            <select name="specialty">
                <option value="">All Specialties</option>
                <?php foreach ($specialties as $spec): ?>
                    <option value="<?= htmlspecialchars($spec['specialty']) ?>">
                        <?= htmlspecialchars($spec['specialty']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="search" placeholder="Search by doctor name or clinic...">
            <button type="submit">Search</button>
        </form>

        <!-- Stats -->
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number"><?= $stats['total_doctors'] ?>+</div>
                <div class="stat-label">Verified Doctors</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $stats['total_specialties'] ?>+</div>
                <div class="stat-label">Specialties</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $stats['total_reviews'] ?>+</div>
                <div class="stat-label">Patient Reviews</div>
            </div>
        </div>
    </section>

    <!-- Featured Doctors -->
    <section class="section">
        <div class="section-header">
            <h2>Top Rated Doctors</h2>
            <p>Meet our highly rated healthcare professionals</p>
        </div>

        <div class="doctor-grid">
            <?php foreach ($featuredDoctors as $doctor): ?>
                <div class="doctor-card">
                    <div class="doctor-header">
                        <div class="doctor-avatar">
                            <?= strtoupper(substr($doctor['name'], 0, 1)) ?>
                        </div>
                        <div class="doctor-info">
                            <h3><?= htmlspecialchars($doctor['name']) ?></h3>
                            <div class="specialty"><?= htmlspecialchars($doctor['specialty'] ?? 'General') ?></div>
                        </div>
                    </div>

                    <div class="doctor-details">
                        <div class="detail-row">
                            <span class="detail-label">Experience</span>
                            <span class="detail-value"><?= $doctor['experience_years'] ?> years</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Consultation Fee</span>
                            <span class="detail-value">$<?= number_format($doctor['consultation_fee'], 2) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Rating</span>
                            <span class="rating">
                                ★ <?= number_format($doctor['average_rating'], 1) ?> 
                                (<?= $doctor['review_count'] ?> reviews)
                            </span>
                        </div>
                    </div>

                    <a href="/doctors/<?= $doctor['doctor_id'] ?>" class="btn-view">View Profile</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="/doctors" class="btn btn-primary" style="padding: 14px 32px;">View All Doctors</a>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: var(--secondary-color); color: var(--white); padding: 40px 20px; text-align: center;">
        <p>&copy; 2024 FINDOC. All rights reserved.</p>
        <p style="margin-top: 8px; color: #999;">Your trusted healthcare marketplace</p>
    </footer>
</body>
</html>