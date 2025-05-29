<?php
session_start();

// Check if cart_data is posted
if (!isset($_POST['cart_data']) || empty($_POST['cart_data'])) {
    echo "<h2 style='color: white; text-align: center;'>No items were selected in your cart.</h2>";
    echo '<p style="text-align: center;"><a href="store.php" style="color: #ff4655;">Go back to store</a></p>';
    exit;
}

// Decode JSON cart data
$cart = json_decode($_POST['cart_data'], true);

if ($cart === null || !is_array($cart) || count($cart) === 0) {
    echo "<h2 style='color: white; text-align: center;'>Your cart is empty or invalid data received.</h2>";
    echo '<p style="text-align: center;"><a href="store.php" style="color: #ff4655;">Go back to store</a></p>';
    exit;
}

// Pricing data
$prices = [
    "Agents" => [ "Jett" => 15, "Reyna" => 12, "Phoenix" => 10, "Sage" => 14, "Sova" => 11, "Waylay" => 13, "Raze" => 16, "Brimstone" => 14, "Cypher" => 12, "Killjoy" => 13 ],
    "Maps" => [ "Bind" => 5, "Haven" => 5, "Split" => 5, "Ascent" => 6, "Icebox" => 6, "Breeze" => 7, "Fracture" => 7, "Lotus" => 8, "Pearl" => 8, "Sunset" => 7 ],
    "Bundles" => [ "Prime Bundle" => 50, "Phantom" => 45, "Operator" => 40, "Ghost" => 30, "Sheriff" => 25, "Spectre" => 35, "Guardian" => 28, "Classic" => 20, "Bucky" => 22, "Marshal" => 26 ],
    "Player Cards" => array_fill_keys(["Card1","Card2","Card3","Card4","Card5","Card6","Card7","Card8","Card9","Card10"], 300),
    "Buddy" => array_fill_keys(["Buddy1","Buddy2","Buddy3","Buddy4","Buddy5","Buddy6","Buddy7","Buddy8","Buddy9","Buddy10"], 700),
];

$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';

// Normalize section to match keys in $prices
function normalizeSection($section) {
    $section = strtolower(trim($section));
    if (in_array($section, ['bundle', 'bundles'])) return 'Bundles';
    if (in_array($section, ['agent', 'agents'])) return 'Agents';
    if (in_array($section, ['map', 'maps'])) return 'Maps';
    if (in_array($section, ['player card', 'player cards'])) return 'Player Cards';
    if ($section === 'buddy' || $section === 'buddies') return 'Buddy';
    return ucfirst($section); // Capitalize first letter for safety
}

// Normalize name to match keys in $prices (trim spaces)
function normalizeName($name) {
    return trim($name);
}

function getPrice($section, $name, $prices) {
    if (!isset($prices[$section])) return 0;
    if (!isset($prices[$section][$name])) return 0;
    return $prices[$section][$name];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Confirm Purchase</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap");
    .payment-option {
      box-shadow: 0 2px 12px 0 rgba(0,0,0,0.25);
      transition: box-shadow 0.2s, border 0.2s, transform 0.2s;
    }
    .payment-option.selected {
      border-width: 2px;
      box-shadow: 0 0 0 6px #00c3a0, 0 2px 16px 0 rgba(0,0,0,0.25);
      z-index: 1;
      transform: scale(1.07);
    }
    .payment-option.selected[data-method="banktransfer"] {
      box-shadow: 0 0 0 6px #1e40af, 0 2px 16px 0 rgba(0,0,0,0.25);
    }
    .payment-option.selected[data-method="gcash"] {
      box-shadow: 0 0 0 6px #0097e6, 0 2px 16px 0 rgba(0,0,0,0.25);
    }
    .payment-option.selected[data-method="711"] {
      box-shadow: 0 0 0 6px #ffbb00, 0 2px 16px 0 rgba(0,0,0,0.25);
    }
  </style>
</head>
<body class="relative font-montserrat text-white min-h-screen overflow-x-hidden" 
      style="background-image: url('images/storebg.png'); background-size: cover; background-position: center;">
  <div class="relative z-10 max-w-5xl mx-auto px-8 py-16">

<audio id="bg-audio" autoplay loop>
  <source src="images/cartvbg.mp3" type="audio/mpeg" />
</audio>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const audio = document.getElementById('bg-audio');
    audio.volume = 0.05; // Set volume (0.0 to 1.0)
  });
</script>

    <h1 class="text-center text-white text-[28px] tracking-widest font-semibold mb-12">
      CONFIRM PURCHASE — <?= $username ?>
    </h1>

    <div class="flex flex-col md:flex-row md:justify-center md:items-start gap-10">
      <div class="flex-shrink-0">
        <img src="images/bundles_1.png"
             alt="Placeholder image"
             class="w-[480px] h-[210px] object-cover rounded-md shadow-lg"/>
      </div>
      <div class="text-[18px] leading-snug font-semibold flex flex-col justify-center space-y-3 text-[#b02a2a]">

        <?php
          $total = 0;
          foreach ($cart as $item):
            $rawName = $item['name'] ?? 'Unknown';
            $rawSection = $item['section'] ?? 'Unknown';
            $quantity = intval($item['quantity'] ?? 1);

            $section = normalizeSection($rawSection);
            $name = normalizeName($rawName);

            $price = getPrice($section, $name, $prices);
            $subtotal = $price * $quantity;
            $total += $subtotal;
        ?>
          <div class="flex justify-between w-[320px]">
            <span><?= htmlspecialchars($name) ?> (<?= htmlspecialchars($section) ?>):</span>
            <span>$<?= number_format($subtotal, 2) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="mt-10 border-t border-gray-600 pt-6 max-w-md mx-auto">
      <div class="flex justify-between text-[18px] text-gray-300 font-semibold tracking-wide">
        <span>SUBTOTAL</span>
        <span>$<?= number_format($total, 2) ?></span>
      </div>
      <div class="flex justify-between text-[18px] text-[#b02a2a] font-semibold tracking-wide mt-2">
        <span>DISCOUNT</span>
        <span>-$<?= number_format($total * 0.25, 2) ?></span>
      </div>
    </div>

    <div class="mt-10 flex justify-center items-center space-x-10 max-w-md mx-auto">
      <div class="flex items-center space-x-3 text-[22px] text-[#b0b07a] font-semibold tracking-wide">
        <i class="fas fa-exclamation-triangle"></i>
        <span>TOTAL PAYABLE</span>
      </div>
      <div class="text-[28px] font-semibold text-white tracking-wide">
        $<?= number_format($total - ($total * 0.25), 2) ?>
      </div>
    </div>

    <div class="mt-10 flex justify-center space-x-10 max-w-md mx-auto">
      <a href="store.php"
         class="flex items-center justify-center space-x-3 border border-gray-600 text-gray-300 text-[24px] font-semibold tracking-wide w-[160px] h-[60px] rounded-md hover:border-[#ff4655] hover:text-[#ff4655] transition">
        <i class="fas fa-chevron-left"></i>
        <span>Back</span>
      </a>
      <form action="payment.php" method="post" onsubmit="return validatePaymentMethod();">
       <!-- Payment Method Selection -->
<div class="mb-4">
  <label class="block mb-4 text-lg text-white font-semibold">Select Payment Method:</label>
  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <label class="payment-option flex flex-col items-center justify-center min-h-[180px] min-w-[300px] p-10 bg-[#222] rounded-2xl cursor-pointer border-2 border-transparent hover:shadow-2xl hover:scale-105 hover:border-[#00c3a0] transition-all text-lg"
           data-method="paymaya">
      <input type="radio" name="payment_method" value="paymaya" class="hidden payment-radio" required>
      <img src="images/paymaya.png" alt="PayMaya" class="w-20 h-20 mb-4"/>
      <span class="text-white font-bold text-2xl text-center">PayMaya</span>
    </label>
    <label class="payment-option flex flex-col items-center justify-center min-h-[180px] min-w-[300px] p-10 bg-[#222] rounded-2xl cursor-pointer border-2 border-transparent hover:shadow-2xl hover:scale-105 hover:border-[#1e40af] transition-all text-lg sm:ml-60  "
           data-method="banktransfer">
      <input type="radio" name="payment_method" value="banktransfer" class="hidden payment-radio" required>
      <img src="images/bank.png" alt="Bank Transfer" class="w-20 h-20 mb-4"/>
      <span class="text-white font-bold text-2xl text-center">Bank Transfer</span>
    </label>
    <label class="payment-option flex flex-col items-center justify-center min-h-[180px] min-w-[300px] p-10 bg-[#222] rounded-2xl cursor-pointer border-2 border-transparent hover:shadow-2xl hover:scale-105 hover:border-[#0097e6] transition-all text-lg"
           data-method="gcash">
      <input type="radio" name="payment_method" value="gcash" class="hidden payment-radio" required>
      <img src="images/gcash.png" alt="GCash" class="w-20 h-20 mb-4"/>
      <span class="text-white font-bold text-2xl text-center">GCash</span>
    </label>
    <label class="payment-option flex flex-col items-center justify-center min-h-[180px] min-w-[300px] p-10 bg-[#222] rounded-2xl cursor-pointer border-2 border-transparent hover:shadow-2xl hover:scale-105 hover:border-[#ffbb00] transition-all text-lg sm:ml-60"
           data-method="711">
      <input type="radio" name="payment_method" value="711" class="hidden payment-radio" required>
      <img src="images/711.png" alt="7/11" class="w-20 h-20 mb-4"/>
      <span class="text-white font-bold text-2xl text-center">7/11 Convenience Store</span>
    </label>
  </div>
</div>
        <!-- Pass cart data to payment.php -->
        <input type="hidden" name="cart_data" value='<?= htmlspecialchars(json_encode($cart), ENT_QUOTES) ?>'>
        <button type="submit"
                class="flex items-center justify-center space-x-3 bg-gradient-to-r from-[#8bc6b9] to-[#a0d9d2] text-[#1b1e24] text-[24px] font-semibold tracking-wide w-[160px] h-[60px] rounded-md hover:brightness-110 transition">
          <span>Purchase</span>
          <i class="fas fa-check"></i>
        </button>
      </form>
    </div>
    <script>
      function validatePaymentMethod() {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        if (!checked) {
          alert('Please select a payment method.');
          return false;
        }
        return true;
      }
      // Highlight selected payment method
      document.querySelectorAll('.payment-radio').forEach(radio => {
        radio.addEventListener('change', function() {
          document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
          const label = this.closest('.payment-option');
          label.classList.add('selected');
          label.setAttribute('data-method', this.value);
        });
      });
    </script>
  </div>
</body>
</html>