<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_data = isset($_POST['cart_data']) ? $_POST['cart_data'] : '';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

    // Prepare purchase record
    $purchase = [
        'username' => $username,
        'cart' => json_decode($cart_data, true),
        'payment_method' => $payment_method,
        'date' => date('Y-m-d H:i:s')
    ];

    // Save to purchases.json (append)
    $file = __DIR__ . '/purchases.json';
    $all_purchases = [];
    if (file_exists($file)) {
        $all_purchases = json_decode(file_get_contents($file), true) ?: [];
    }
    $all_purchases[] = $purchase;
    file_put_contents($file, json_encode($all_purchases, JSON_PRETTY_PRINT));
}
?>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Thank You</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet"
  />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap");

    body {
      background-color: #1b1e24;
      font-family: "Montserrat", sans-serif;
    }

    .valorant-gradient {
      background: linear-gradient(90deg, #8bc6b9, #a0d9d2);
      color: #1b1e24;
    }

    .btn-border {
      border: 2px solid #b02a2a;
      color: #b02a2a;
      font-weight: 600;
      letter-spacing: 0.05em;
      transition: all 0.3s ease;
    }

    .btn-border:hover {
      background-color: #b02a2a;
      color: white;
      box-shadow: 0 0 8px #b02a2a;
      cursor: pointer;
    }

    .glow-text {
      color: #b02a2a;
      text-shadow: 0 0 6px #b02a2a, 0 0 10px #ff3b3b;
      font-weight: 700;
      letter-spacing: 0.1em;
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen px-4 text-white">
    <audio id="bg-audio" autoplay loop>
  <source src="images/paymentvbgr.mp3" type="audio/mpeg" />
</audio>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const audio = document.getElementById('bg-audio');
    audio.volume = 1; // Set volume (0.0 to 1.0)
  });
</script>
  <div class="max-w-md w-full bg-[#1b1e24] border border-[#b02a2a] rounded-md p-10 text-center">
    <i class="fas fa-check-circle text-[#b02a2a] text-[80px] mb-6 glow-text"></i>
    <h1 class="text-[28px] tracking-widest mb-4 font-semibold glow-text uppercase">
      THANK YOU FOR PURCHASING
    </h1>
    <p class="text-[#b0b0b0] text-[16px] mb-8">
      Your purchase was successful! We appreciate your business and hope you enjoy your new items.
    </p>
    <button
      type="button"
      id="homeBtn"
      class="valorant-gradient font-semibold text-[18px] px-8 py-3 rounded-md w-full max-w-xs mx-auto mb-4"
      onclick="window.location.href='home.php'"
    >
      GO TO HOME
    </button>
    <p id="redirectText" class="text-[#b0b0b0] text-[14px]">
      Redirecting to home in 10 seconds...
    </p>
  </div>

  <script>
    let countdown = 10;
    const redirectText = document.getElementById('redirectText');

    const interval = setInterval(() => {
      countdown--;
      if (countdown > 0) {
        redirectText.textContent = `Redirecting to home in ${countdown} second${countdown === 1 ? '' : 's'}...`;
      } else {
        clearInterval(interval);
        window.location.href = 'home.php';
      }
    }, 1000);
  </script>
</body>
</html>