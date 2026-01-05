<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Availability - FINDOC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <style>
        .navbar-brand { color: #FFC629 !important; font-weight: 700; }
        .sidebar { min-height: 100vh; background: #f8f9fa; }
        .sidebar .nav-link { color: #333; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #FFC629; color: #000; }
        .slot-badge { font-size: 12px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar border-end" style="width: 250px;">
            <div class="p-3 border-bottom">
                <h5 class="mb-0" style="color: #FFC629;">FINDOC</h5>
                <small class="text-muted">Doctor Portal</small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link" href="/doctor/dashboard">📊 Dashboard</a>
                <a class="nav-link" href="/doctor/profile">👤 Profile</a>
                <a class="nav-link active" href="/doctor/availability">📅 Availability</a>
                <a class="nav-link" href="/doctor/appointments">📋 Appointments</a>
                <a class="nav-link" href="/">🏠 Home</a>
                <a class="nav-link" href="/logout">🚪 Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <h5 class="mb-0">Manage Availability</h5>
                </div>
            </nav>

            <!-- Content -->
            <div class="p-4">
                <div class="row">
                    <!-- Add Time Slot Form -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Add Time Slot</h5>
                            </div>
                            <div class="card-body">
                                <form id="addSlotForm">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Date *</label>
                                        <input type="date" 
                                               class="form-control" 
                                               name="slot_date" 
                                               min="<?= date('Y-m-d') ?>"
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Start Time *</label>
                                        <input type="time" 
                                               class="form-control" 
                                               name="start_time" 
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">End Time *</label>
                                        <input type="time" 
                                               class="form-control" 
                                               name="end_time" 
                                               required>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        Add Slot
                                    </button>
                                </form>

                                <hr>

                                <h6 class="mb-3">Set Unavailable Date</h6>
                                <form id="unavailableForm">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Date *</label>
                                        <input type="date" 
                                               class="form-control" 
                                               name="unavailable_date" 
                                               min="<?= date('Y-m-d') ?>"
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Reason (Optional)</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="reason"
                                               placeholder="e.g., Holiday, Conference">
                                    </div>

                                    <button type="submit" class="btn btn-warning w-100">
                                        Mark Unavailable
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Quick Add Templates -->
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Quick Add</h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted">Add common time slots:</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="quickAdd('09:00', '17:00')">
                                        9 AM - 5 PM (Full Day)
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="quickAdd('09:00', '12:00')">
                                        9 AM - 12 PM (Morning)
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="quickAdd('14:00', '17:00')">
                                        2 PM - 5 PM (Afternoon)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slots List -->
                    <div class="col-lg-8">
                        <!-- Unavailable Dates -->
                        <?php if (!empty($unavailableDates)): ?>
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Unavailable Dates</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($unavailableDates as $unavailable): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="alert alert-warning mb-0">
                                                    <strong><?= date('M d, Y', strtotime($unavailable['unavailable_date'])) ?></strong>
                                                    <?php if ($unavailable['reason']): ?>
                                                        <br><small><?= htmlspecialchars($unavailable['reason']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Available Slots -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Your Time Slots (Next 30 Days)</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($slots)): ?>
                                    <p class="text-muted text-center py-4">
                                        No time slots available. Add your first slot using the form.
                                    </p>
                                <?php else: ?>
                                    <div id="slotsContainer">
                                        <?php 
                                        $groupedSlots = [];
                                        foreach ($slots as $slot) {
                                            $groupedSlots[$slot['slot_date']][] = $slot;
                                        }
                                        ?>

                                        <?php foreach ($groupedSlots as $date => $dateSlots): ?>
                                            <div class="mb-4">
                                                <h6 class="border-bottom pb-2">
                                                    <?= date('l, F d, Y', strtotime($date)) ?>
                                                </h6>
                                                <div class="row g-2">
                                                    <?php foreach ($dateSlots as $slot): ?>
                                                        <div class="col-md-3">
                                                            <div class="card h-100 <?= $slot['is_booked'] ? 'border-success' : '' ?>">
                                                                <div class="card-body p-2 text-center">
                                                                    <div class="fw-bold">
                                                                        <?= date('h:i A', strtotime($slot['start_time'])) ?>
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        <?= date('h:i A', strtotime($slot['end_time'])) ?>
                                                                    </small>
                                                                    <div class="mt-1">
                                                                        <?php if ($slot['is_booked']): ?>
                                                                            <span class="badge bg-success slot-badge">Booked</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-secondary slot-badge">Available</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add time slot
        document.getElementById('addSlotForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/doctor/availability', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                
                const data = await response.json();
                alert(data.message);
                
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });

        // Set unavailable
        document.getElementById('unavailableForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/doctor/unavailability', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                
                const data = await response.json();
                alert(data.message);
                
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });

        // Quick add function
        function quickAdd(startTime, endTime) {
            document.querySelector('input[name="start_time"]').value = startTime;
            document.querySelector('input[name="end_time"]').value = endTime;
        }
    </script>
</body>
</html>