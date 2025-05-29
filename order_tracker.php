<?php
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$file = __DIR__ . '/purchases.json';
$purchases = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Filter purchases for this user, most recent first
$user_purchases = [];
if ($username) {
    foreach (array_reverse($purchases) as $purchase) {
        if ($purchase['username'] === $username) {
            $user_purchases[] = $purchase;
        }
    }
}

// Example status steps (customize as needed)
$status_steps = [
    "Order Placed",
    "Packed by Seller",
    "Picked up by Courier",
    "In Transit",
    "Delivered"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Tracker</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap');
        body {
            background: #181a20 url('images/storebg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Oswald', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
        }
        .tracker-title {
            font-family: 'Oswald', Arial, sans-serif;
            font-size: 2.2rem;
            color: #ff4655;
            letter-spacing: 0.12em;
            font-weight: 700;
            text-shadow: 0 2px 8px #000a;
        }
        .tracker-subtitle {
            color: #b0b0b0;
            font-size: 1.1rem;
            letter-spacing: 0.08em;
            margin-bottom: 2rem;
        }
        .tracker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .tracker-card {
            background: rgba(24,24,24,0.92);
            border: 1.5px solid #23272f;
            border-radius: 1.1rem;
            box-shadow: 0 2px 16px 0 #0008;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            max-width: 370px;
            margin: 0 auto;
        }
        .tracker-section-title {
            color: #b0b0b0;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 0.08em;
        }
        .tracker-items-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .tracker-items-list li {
            margin-bottom: 0.4rem;
            background: rgba(34,34,44,0.7);
            border-radius: 0.5rem;
            padding: 0.4rem 0.7rem;
            color: #fff;
            font-size: 1.02rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            letter-spacing: 0.04em;
        }
        .tracker-items-list li .item-name {
            color: #ff4655;
            font-weight: 600;
        }
        .tracker-items-list li .item-qty {
            color: #b0b0b0;
            font-size: 0.98rem;
            font-weight: 400;
        }
        .tracker-steps {
            margin-top: 2rem;
        }
        .tracker-step {
            display: flex;
            align-items: center;
            margin-bottom: 1.2rem;
        }
        .tracker-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #23272f;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 1rem;
            border: 2px solid #ff4655;
            transition: background 0.2s, border 0.2s;
        }
        .tracker-step.active .tracker-circle {
            background: #ff4655;
            border-color: #ff4655;
        }
        .tracker-label {
            font-size: 1.1rem;
            color: #fff;
            letter-spacing: 0.08em;
        }
        .tracker-step.completed .tracker-circle {
            background: #2ecc71;
            border-color: #2ecc71;
        }
        .tracker-step.completed .tracker-label {
            color: #2ecc71;
        }
        .no-orders {
            background: rgba(24,24,24,0.92);
            border: 1.5px dashed #ff4655;
            border-radius: 1.1rem;
            color: #b0b0b0;
            font-size: 1.2rem;
            padding: 2rem 1rem;
            text-align: center;
            max-width: 350px;
            margin: 0 auto;
        }
        .back-btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.7rem 2rem;
            border-radius: 0.7rem;
            background: #23272f;
            color: #ff4655;
            font-family: 'Oswald', Arial, sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            box-shadow: 0 2px 12px #0006;
            border: 1.5px solid #23272f;
            transition: background 0.18s, color 0.18s, border 0.18s;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #ff4655;
            color: #fff;
            border-color: #ff4655;
        }
    </style>
</head>
<body class="py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 text-center">
            <div class="tracker-title flex items-center justify-center gap-4">
                <i class="fas fa-shipping-fast"></i>
                Order Tracker
            </div>
            <div class="tracker-subtitle">Track the status of all your purchases</div>
            <div class="text-center">
                <a href="home.php" class="back-btn"><i class="fas fa-chevron-left mr-2"></i>Back to Menu</a>
            </div>
        </div>
        <?php if (count($user_purchases) > 0): ?>
            <div class="tracker-grid">
                <?php foreach ($user_purchases as $idx => $purchase): ?>
                    <?php
                        // For the most recent purchase, always start at "Order Placed"
                        $current_status = ($idx === 0) ? 0 : min(count($status_steps)-1, rand(1, count($status_steps)));
                    ?>
                    <div class="tracker-card">
                        <div class="tracker-section-title mb-1"><i class="fas fa-calendar-alt"></i> Order Date: <span class="text-[#ff4655]"><?= htmlspecialchars($purchase['date']) ?></span></div>
                        <div class="tracker-section-title mb-1"><i class="fas fa-credit-card"></i> Payment: <span class="text-[#ff4655]"><?= htmlspecialchars(ucfirst($purchase['payment_method'])) ?></span></div>
                        <div class="tracker-section-title mb-1"><i class="fas fa-box"></i> Items:</div>
                        <ul class="tracker-items-list mb-2">
                            <?php foreach ($purchase['cart'] as $item): ?>
                                <li>
                                    <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                                    <span class="item-qty">x<?= intval($item['quantity']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tracker-steps">
                            <?php foreach ($status_steps as $i => $step): ?>
                                <div class="tracker-step <?= $i < $current_status ? 'completed' : ($i == $current_status ? 'active' : '') ?>">
                                    <div class="tracker-circle">
                                        <?= $i < $current_status ? '<i class="fas fa-check"></i>' : $i+1 ?>
                                    </div>
                                    <div class="tracker-label"><?= $step ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-orders col-span-full">
                <div class="mb-2"><i class="fas fa-box-open fa-2x text-[#ff4655]"></i></div>
                No recent orders found.<br>
                <a href="store.php" class="text-[#ff4655] underline hover:text-[#b02a2a]">Go to Store</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>