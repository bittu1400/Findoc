<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - FINDOC</title>
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
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .booking-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 32px;
        }
        .booking-section {
            background: var(--white);
            border-radius: 12px;
            padding: 32px;
            box-shadow: var(--shadow);
        }
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .doctor-info {
            display: flex;
            gap: 16px;
            padding: 20px;
            background: var(--bg-light);
            border-radius: 8px;
            margin-bottom: 32px;
        }
        .doctor-avatar-small {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .doctor-details h3 {
            font-size: 18px;
            margin-bottom: 4px;
        }
        .doctor-specialty-small {
            color: var(--text-light);
            font-size: 14px;
        }
        .calendar-grid {
            display: grid;
            gap: 16px;
        }
        .date-group {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        .date-header {
            background: var(--bg-light);
            padding: 16px;
            font-weight: 600;
        }
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 8px;
            padding: 16px;
        }
        .slot-button {
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--white);
            cursor: pointer;
            text-align: center;
            font-weight: 600;
            transition: var(--transition);
        }
        .slot-button:hover {
            border-color: var(--primary-color);
            background: var(--accent-color);
        }
        .slot-button.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: var(--text-dark);
        }
        .summary-section {
            position: sticky;
            top: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-label {
            color: var(--text-light);
        }
        .summary-value {
            font-weight: 600;
        }
        .total-section {
            background: var(--bg-light);
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
        }
        .total-label {
            font-size: 18px;
            font-weight: 600;
        }
        .total-amount {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .empty-slots {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-light);
        }
        @media (max-width: 968px) {
            .booking-grid {
                grid-template-columns: 1fr;
            }
            .summary-section {
                position: static;
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
                <a href="/appointments">My Appointments</a>
                <a href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <h1>Book Appointment</h1>
        <p>Select a convenient date and time</p>
    </div>

    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="container">
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['flash']['error']); unset($_SESSION['flash']['error']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="booking-grid">
            <!-- Left Column - Calendar -->
            <div class="booking-section">
                <!-- Doctor Info -->
                <div class="doctor-info">
                    <div class="doctor-avatar-small">
                        <?= strtoupper(substr($doctor['name'], 0, 1)) ?>
                    </div>
                    <div class="doctor-details">
                        <h3><?= htmlspecialchars($doctor['name']) ?></h3>
                        <div class="doctor-specialty-small"><?= htmlspecialchars($doctor['specialty'] ?? 'General Practice') ?></div>
                    </div>
                </div>

                <h2 class="section-title">Select Date & Time</h2>

                <?php if (empty($availableSlots)): ?>
                    <div class="empty-slots">
                        <h3>No Available Slots</h3>
                        <p>This doctor has no available appointments at the moment.</p>
                        <a href="/doctors/<?= $doctor['doctor_id'] ?>" class="btn btn-primary" style="margin-top: 20px;">
                            Back to Profile
                        </a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="/appointments" id="bookingForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="doctor_id" value="<?= $doctor['doctor_id'] ?>">
                        <input type="hidden" name="slot_id" id="selectedSlotId">

                        <div class="calendar-grid">
                            <?php foreach ($availableSlots as $date => $slots): ?>
                                <div class="date-group">
                                    <div class="date-header">
                                        <?= date('l, F d, Y', strtotime($date)) ?>
                                    </div>
                                    <div class="slots-grid">
                                        <?php foreach ($slots as $slot): ?>
                                            <button type="button" 
                                                    class="slot-button" 
                                                    data-slot-id="<?= $slot['slot_id'] ?>"
                                                    data-date="<?= $date ?>"
                                                    data-time="<?= date('h:i A', strtotime($slot['start_time'])) ?>"
                                                    onclick="selectSlot(this)">
                                                <?= date('h:i A', strtotime($slot['start_time'])) ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Right Column - Summary -->
            <div class="booking-section summary-section">
                <h2 class="section-title">Booking Summary</h2>

                <div class="summary-item">
                    <span class="summary-label">Doctor</span>
                    <span class="summary-value"><?= htmlspecialchars($doctor['name']) ?></span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Specialty</span>
                    <span class="summary-value"><?= htmlspecialchars($doctor['specialty'] ?? 'General') ?></span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Date</span>
                    <span class="summary-value" id="selectedDate">Not selected</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Time</span>
                    <span class="summary-value" id="selectedTime">Not selected</span>
                </div>

                <div class="total-section">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="total-label">Total Amount</span>
                        <span class="total-amount">$<?= number_format($doctor['consultation_fee'], 2) ?></span>
                    </div>
                </div>

                <button type="submit" 
                        form="bookingForm" 
                        class="btn btn-primary btn-full" 
                        style="margin-top: 24px;"
                        id="confirmButton"
                        disabled>
                    Confirm Booking
                </button>

                <p style="text-align: center; color: var(--text-light); font-size: 14px; margin-top: 16px;">
                    You will be redirected to payment after confirmation
                </p>
            </div>
        </div>
    </div>

    <script>
        let selectedSlot = null;

        function selectSlot(button) {
            // Remove selection from all buttons
            document.querySelectorAll('.slot-button').forEach(btn => {
                btn.classList.remove('selected');
            });

            // Select this button
            button.classList.add('selected');

            // Store selection
            selectedSlot = {
                id: button.dataset.slotId,
                date: button.dataset.date,
                time: button.dataset.time
            };

            // Update form and summary
            document.getElementById('selectedSlotId').value = selectedSlot.id;
            document.getElementById('selectedDate').textContent = new Date(selectedSlot.date).toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('selectedTime').textContent = selectedSlot.time;
            document.getElementById('confirmButton').disabled = false;
        }
    </script>
</body>
</html>