<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Doctors - FINDOC</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .page-header {
            background: var(--primary-color);
            padding: 40px 20px;
            text-align: center;
        }
        .page-header h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .filters {
            background: var(--white);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 32px;
        }
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .results-count {
            font-size: 18px;
            font-weight: 600;
        }
        .sort-select {
            padding: 10px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }
        .doctor-list {
            display: grid;
            gap: 24px;
        }
        .doctor-item {
            background: var(--white);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            display: flex;
            gap: 24px;
            transition: var(--transition);
        }
        .doctor-item:hover {
            box-shadow: var(--shadow-lg);
        }
        .doctor-avatar {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-color);
            flex-shrink: 0;
        }
        .doctor-content {
            flex: 1;
        }
        .doctor-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .doctor-specialty {
            color: var(--text-light);
            margin-bottom: 16px;
        }
        .doctor-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            margin-bottom: 16px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .meta-label {
            color: var(--text-light);
        }
        .meta-value {
            font-weight: 600;
        }
        .doctor-description {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .doctor-actions {
            display: flex;
            gap: 12px;
        }
        .btn-book {
            padding: 10px 24px;
            background: var(--primary-color);
            color: var(--text-dark);
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-book:hover {
            background: var(--primary-dark);
        }
        .btn-profile {
            padding: 10px 24px;
            background: var(--white);
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 12px;
        }
        @media (max-width: 768px) {
            .doctor-item {
                flex-direction: column;
            }
            .doctor-meta {
                flex-direction: column;
                gap: 12px;
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

    <!-- Page Header -->
    <div class="page-header">
        <h1>Find Your Doctor</h1>
        <p>Browse through our network of verified healthcare professionals</p>
    </div>

    <div class="container">
        <!-- Filters -->
        <form method="GET" action="/doctors" class="filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" name="search" placeholder="Doctor name or clinic..." 
                           value="<?= htmlspecialchars($filters['search']) ?>">
                </div>
                <div class="filter-group">
                    <label>Specialty</label>
                    <select name="specialty">
                        <option value="">All Specialties</option>
                        <?php foreach ($specialties as $spec): ?>
                            <option value="<?= htmlspecialchars($spec['specialty']) ?>"
                                <?= $filters['specialty'] === $spec['specialty'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($spec['specialty']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Min Experience (years)</label>
                    <input type="number" name="min_experience" min="0" 
                           value="<?= htmlspecialchars($filters['min_experience']) ?>">
                </div>
                <div class="filter-group">
                    <label>Max Fee ($)</label>
                    <input type="number" name="max_fee" min="0" 
                           value="<?= htmlspecialchars($filters['max_fee']) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>

        <!-- Results Header -->
        <div class="results-header">
            <div class="results-count">
                <?= count($doctors) ?> doctors found
            </div>
            <select name="sort" class="sort-select" onchange="window.location.href='?sort=' + this.value + '<?= !empty($filters['specialty']) ? '&specialty=' . urlencode($filters['specialty']) : '' ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>'">
                <option value="name" <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>Name (A-Z)</option>
                <option value="rating" <?= $filters['sort'] === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                <option value="experience" <?= $filters['sort'] === 'experience' ? 'selected' : '' ?>>Most Experienced</option>
                <option value="fee_low" <?= $filters['sort'] === 'fee_low' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="fee_high" <?= $filters['sort'] === 'fee_high' ? 'selected' : '' ?>>Price: High to Low</option>
            </select>
        </div>

        <!-- Doctor List -->
        <?php if (empty($doctors)): ?>
            <div class="empty-state">
                <h3>No doctors found</h3>
                <p>Try adjusting your filters or search criteria</p>
            </div>
        <?php else: ?>
            <div class="doctor-list">
                <?php foreach ($doctors as $doctor): ?>
                    <div class="doctor-item">
                        <div class="doctor-avatar">
                            <?= strtoupper(substr($doctor['name'], 0, 1)) ?>
                        </div>
                        <div class="doctor-content">
                            <h2 class="doctor-name"><?= htmlspecialchars($doctor['name']) ?></h2>
                            <div class="doctor-specialty"><?= htmlspecialchars($doctor['specialty'] ?? 'General Practice') ?></div>
                            
                            <div class="doctor-meta">
                                <div class="meta-item">
                                    <span class="meta-label">Experience:</span>
                                    <span class="meta-value"><?= $doctor['experience_years'] ?> years</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Fee:</span>
                                    <span class="meta-value">$<?= number_format($doctor['consultation_fee'], 2) ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Rating:</span>
                                    <span class="meta-value" style="color: var(--primary-color);">
                                        ★ <?= number_format($doctor['average_rating'], 1) ?> 
                                        (<?= $doctor['review_count'] ?>)
                                    </span>
                                </div>
                                <?php if (!empty($doctor['clinic_name'])): ?>
                                    <div class="meta-item">
                                        <span class="meta-label">Clinic:</span>
                                        <span class="meta-value"><?= htmlspecialchars($doctor['clinic_name']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($doctor['description'])): ?>
                                <div class="doctor-description">
                                    <?= htmlspecialchars(substr($doctor['description'], 0, 150)) ?>
                                    <?= strlen($doctor['description']) > 150 ? '...' : '' ?>
                                </div>
                            <?php endif; ?>

                            <div class="doctor-actions">
                                <a href="/appointments/create/<?= $doctor['doctor_id'] ?>" class="btn-book">Book Appointment</a>
                                <a href="/doctors/<?= $doctor['doctor_id'] ?>" class="btn-profile">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>