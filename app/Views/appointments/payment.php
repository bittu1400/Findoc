<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - FINDOC</title>
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
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .payment-grid {
            display: grid;
            gap: 24px;
        }
        .card {
            background: var(--white);
            border-radius: 12px;
            padding: 32px;
            box-shadow: var(--shadow);
        }
        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border-color);
        }
        .appointment-summary {
            display: grid;
            gap: 16px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
        }
        .summary-label {
            color: var(--text-light);
        }
        .summary-value {
            font-weight: 600;
        }
        .total-row {
            background: var(--bg-light);
            padding: 16px;
            border-radius: 8px;
            margin-top: 12px;
        }
        .total-amount {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .payment-methods {
            display: grid;
            gap: 12px;
        }
        .payment-method {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }
        .payment-method:hover {
            border-color: var(--primary-color);
            background: var(--accent-color);
        }
        .payment-method input[type="radio"] {
            cursor: pointer;
        }
        .payment-icon {
            width: 48px;
            height: 48px;
            background: var(--accent-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .payment-info {
            flex: 1;
        }
        .payment-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .payment-description {
            font-size: 14px;
            color: var(--text-light);
        }
        .card-details {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .security-note {
            background: var(--accent-color);
            padding: 16px;
            border-radius: 8px;
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-light);
        }
        @media (max-width: 768px) {
            .form-row {
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
                <a href="/appointments">My Appointments</a>
                <a href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <h1>Complete Payment</h1>
        <p>Secure payment for your appointment</p>
    </div>

    <div class="container">
        <div class="payment-grid">
            <!-- Appointment Summary -->
            <div class="card">
                <h2 class="section-title">Appointment Summary</h2>
                <div class="appointment-summary">
                    <div class="summary-row">
                        <span class="summary-label">Doctor</span>
                        <span class="summary-value"><?= htmlspecialchars($appointment['doctor_name']) ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Specialty</span>
                        <span class="summary-value"><?= htmlspecialchars($appointment['specialty'] ?? 'General') ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Date</span>
                        <span class="summary-value">
                            <?= date('l, F d, Y', strtotime($appointment['slot_date'])) ?>
                        </span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Time</span>
                        <span class="summary-value">
                            <?= date('h:i A', strtotime($appointment['start_time'])) ?>
                        </span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Clinic</span>
                        <span class="summary-value"><?= htmlspecialchars($appointment['clinic_name'] ?? 'N/A') ?></span>
                    </div>
                </div>

                <div class="total-row">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 18px; font-weight: 600;">Total Amount</span>
                        <span class="total-amount">$<?= number_format($appointment['consultation_fee'], 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="card">
                <h2 class="section-title">Payment Method</h2>

                <form method="POST" action="/appointments/<?= $appointment['appointment_id'] ?>/payment">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="card" checked>
                            <div class="payment-icon">💳</div>
                            <div class="payment-info">
                                <div class="payment-name">Credit/Debit Card</div>
                                <div class="payment-description">Pay securely with your card</div>
                            </div>
                        </label>

                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="wallet">
                            <div class="payment-icon">👛</div>
                            <div class="payment-info">
                                <div class="payment-name">Digital Wallet</div>
                                <div class="payment-description">PayPal, Apple Pay, Google Pay</div>
                            </div>
                        </label>

                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="bank">
                            <div class="payment-icon">🏦</div>
                            <div class="payment-info">
                                <div class="payment-name">Bank Transfer</div>
                                <div class="payment-description">Direct bank transfer</div>
                            </div>
                        </label>
                    </div>

                    <!-- Card Details (Demo - In production, use Stripe Elements or similar) -->
                    <div class="card-details">
                        <div class="form-group">
                            <label>Cardholder Name</label>
                            <input type="text" placeholder="John Doe" required>
                        </div>

                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" placeholder="123" maxlength="3" required>
                            </div>
                            <div class="form-group">
                                <label>ZIP Code</label>
                                <input type="text" placeholder="12345" maxlength="5">
                            </div>
                        </div>
                    </div>

                    <div class="security-note">
                        🔒 Your payment information is encrypted and secure. We never store your card details.
                    </div>

                    <button type="submit" class="btn btn-primary btn-full" style="margin-top: 24px; padding: 16px;">
                        Pay $<?= number_format($appointment['consultation_fee'], 2) ?>
                    </button>
                </form>

                <p style="text-align: center; color: var(--text-light); font-size: 14px; margin-top: 16px;">
                    By completing this payment, you agree to our terms and conditions
                </p>
            </div>
        </div>
    </div>

    <script>
        // Simple card number formatting
        document.querySelector('input[placeholder="1234 5678 9012 3456"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Expiry date formatting
        document.querySelector('input[placeholder="MM/YY"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });

        // CVV - numbers only
        document.querySelector('input[placeholder="123"]').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>