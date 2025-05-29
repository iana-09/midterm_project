<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php?error=Please log in first.');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);

$sections = [
    "Agents" => ["Jett", "Reyna", "Phoenix", "Sage", "Sova", "Waylay", "Raze", "Brimstone", "Cypher", "Killjoy"],
    "Maps" => ["Bind", "Haven", "Split", "Ascent", "Icebox", "Breeze", "Fracture", "Lotus", "Pearl", "Sunset"],
    "Bundles" => ["Araxys Bundle", "Elderflame Bundle", "Evory Bundle", "Gaia Bundle", "Glitchpop Bundle", "Kuronami Bundle", "Sentinels Bundle", "Mystbloom Bundle", "Prelude Bundle", "Primordium Bundle"],
    "Player Cards" => ["Card1", "Card2", "Card3", "Card4", "Card5", "Card6", "Card7", "Card8", "Card9", "Card10"],
    "Buddy" => ["Buddy1", "Buddy2", "Buddy3", "Buddy4", "Buddy5", "Buddy6", "Buddy7", "Buddy8", "Buddy9", "Buddy10"]
];

$prices = [
    "Agents" => [ "Jett" => 15, "Reyna" => 12, "Phoenix" => 10, "Sage" => 14, "Sova" => 11, "Waylay" => 13, "Raze" => 16, "Brimstone" => 14, "Cypher" => 12, "Killjoy" => 13 ],
    "Maps" => [ "Bind" => 5, "Haven" => 5, "Split" => 5, "Ascent" => 6, "Icebox" => 6, "Breeze" => 7, "Fracture" => 7, "Lotus" => 8, "Pearl" => 8, "Sunset" => 7 ],
    "Bundles" => [ "Araxys Bundle" => 50, "Elderflame Bundle" => 45, "Evory Bundle" => 40, "Gaia Bundle" => 30, "Glitchpop Bundle" => 25, "Kuronami Bundle" => 35, "Sentinels Bundle" => 28, "Mystbloom Bundle" => 20, "Prelude Bundle" => 22, "Primordium Bundle" => 26 ],
    "Player Cards" => array_fill_keys(["Card1","Card2","Card3","Card4","Card5","Card6","Card7","Card8","Card9","Card10"], 300),
    "Buddy" => array_fill_keys(["Buddy1","Buddy2","Buddy3","Buddy4","Buddy5","Buddy6","Buddy7","Buddy8","Buddy9","Buddy10"], 700),
];
?>

<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Valorant Order Form - Welcome <?= $username ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
<style>
   body {
    margin: 0;
    background: url('images/storebg.png') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .navbar {
    background: rgba(26, 26, 26, 0.8);
    padding: 1rem 2rem;
    color: #ff4655;
    font-weight: bold;
    font-size: 1.2rem;
  }
  .section {
    padding: 1rem 1.5rem;
    background: rgba(0, 0, 0, 0.6);
    margin: 1rem 1.5rem 2rem 2rem;
    border-radius: 0.5rem;
    max-width: calc(98vw - 120px);
    overflow: visible;
    box-sizing: border-box;
  }
  h2 {
    border-bottom: 2px solid #ff4655;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
  }

  #cartSidebar {
  pointer-events: none;  /* disable pointer events when hidden */
}
#cartSidebar.active {
  pointer-events: auto;  /* enable pointer events when shown */
}
  .thumbnails {
    display: flex;
    gap: 1rem;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none;  /* IE and Edge */
    max-width: 100%;
  }
  .thumbnails::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
  }
  .thumbnail {
    cursor: pointer;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0 5px #ff4655;
    transition: transform 0.2s ease;
    background: rgba(26,26,26,0.8);
    width: 156px;
    flex-shrink: 0;
  }
  .thumbnail:hover, .thumbnail:focus {
    outline: none;
    transform: scale(1.05);
    box-shadow: 0 0 10px #ff4655;
  }
  .thumbnail img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
  }
  .thumbnail-name {
    padding: 0.3rem;
    text-align: center;
    background: #1a1a1a;
  }
  #cartSidebar {
    position: fixed;
    top: 0;
    right: -400px;
    width: 350px;
    height: 100%;
    background: rgba(26, 26, 26, 0.95);
    color: white;
    box-shadow: -5px 0 15px rgba(255,70,85,0.6);
    padding: 1rem;
    transition: right 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
  }
  #cartSidebar.active {
    right: 0;
  }
  #cartSidebar header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }
  #cartSidebar header h3 {
    margin: 0;
    color: #ff4655;
  }
  #cartCloseBtn {
    background: transparent;
    border: none;
    font-size: 1.5rem;
    color: #ff4655;
    cursor: pointer;
  }
  .cart-item {
    margin-bottom: 1rem;
    border-bottom: 1px solid #ff4655;
    padding-bottom: 0.5rem;
  }
  .cart-item-name {
    font-weight: bold;
    margin-bottom: 0.3rem;
  }
  .confirm-btn {
    background-color: #ff4655;
    color: white;
    border: none;
    padding: 0.8rem 1rem;
    width: 100%;
    font-weight: bold;
    border-radius: 0.3rem;
    cursor: pointer;
    margin-top: 1rem;
  }

  /* Sidebar icon style */
  .sidebar-icon {
    width: 48px;
    height: 48px;
    object-fit: cover;
    cursor: pointer;
  }

  /* Right sidebar */
  aside.right-sidebar {
    position: fixed;
    top: 0;
    right: 0;
    height: 100%;
    width: 80px;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 1.5rem;
    gap: 1.5rem;
    z-index: 30;
  }
  aside.right-sidebar button {
    color: white;
    font-size: 1.8rem;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.2s ease;
  }
  aside.right-sidebar button:hover,
  aside.right-sidebar button:focus {
    color: #ff4655;
    outline: none;
    box-shadow: none; /* remove red glow */
    text-shadow: none;
  }

  aside.main-panel {
  position: fixed;
  top: 0;
  right: 110px;
  width: 320px;
  height: 100vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  color: white;
  font-family: 'Oswald', sans-serif;
  font-weight: 500;
  z-index: 20;
  padding-top: 1.5rem;
  box-sizing: border-box;
}
  aside.main-panel .stats {
    display: flex;
    justify-content: flex-end;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    flex-wrap: wrap;
    gap: 1rem;
  }
  aside.main-panel .stats > div {
    display: flex;
    align-items: center;
    gap: 0.3rem;
  }

  /* Cart button top-left */
  #cartBtn {
    position: fixed;
    top: 20px;
    left: 20px;
    background-color: #ff4655;
    border: none;
    color: white;
    padding: 1rem 1.3rem;
    font-weight: bold;
    border-radius: 2rem;
    cursor: pointer;
    box-shadow: none;
    transition: none;
    z-index: 1100;
  }
  #cartBtn:hover,
  #cartBtn:focus {
    color: white;
    background-color: #ff4655;
    outline: none;
    box-shadow: none;
    text-shadow: none;
  }

  #homeBtn {
  position: fixed;
  right: 16px;
  bottom: 24px;
  width: 48px;
  height: 48px;
  background: rgba(0, 0, 0, 0.6);
  border: none;
  color: #fff;
  font-size: 1.8rem;
  border-radius: 50%;
  cursor: pointer;
  z-index: 1001;
  transition: background 0.2s ease;
}

#homeBtn:hover,
#homeBtn:focus {
  background: #ff4655;
  color: white;
  outline: none;
}
</style>
</head>
<body>
<audio id="backgroundMusic" src="images/storevbg.mp3" autoplay loop ></audio>
<div class="navbar" role="banner">
  <div>Welcome <?= $username ?></div>
</div>

<?php foreach($sections as $sectionName => $items): ?>
<div class="section" role="main" aria-label="<?= htmlspecialchars($sectionName) ?>">
  <h2><?= htmlspecialchars($sectionName) ?></h2>
  <div class="thumbnails" role="list" aria-label="<?= htmlspecialchars($sectionName) ?> thumbnails">
    <?php foreach($items as $index => $item): 
      $imgSrc = "images/".strtolower(str_replace(' ', '_', $sectionName))."_".($index+1).".png";
      $altText = htmlspecialchars($item);
    ?>
    <div tabindex="0" class="thumbnail" data-name="<?= htmlspecialchars($item) ?>" data-section="<?= htmlspecialchars($sectionName) ?>" role="listitem" aria-label="<?= htmlspecialchars($sectionName).' '.$altText ?>">
      <img src="<?= $imgSrc ?>" alt="<?= $altText ?>" />
      <div class="thumbnail-name"><?= htmlspecialchars($item) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endforeach; ?>

<!-- RIGHT SIDEBAR (unchanged) -->
<aside class="right-sidebar" aria-label="Sidebar">
  <button id="cartSidebarToggle" aria-controls="cartSidebar" aria-expanded="false" aria-label="Open Shopping Cart"><i class="fas fa-shopping-cart"></i></button>
  <button><img src="images/user.png" alt="User profile" class="sidebar-icon"/></button>
<button id="homeBtn" onclick="location.href='home.php'" aria-label="Go to Home">
  <i class="fas fa-home"></i>
</button>
  </button>
</aside>

<!-- MAIN PANEL (unchanged) -->
<aside class="main-panel" aria-label="User stats panel">
  <div class="stats" aria-live="polite" aria-atomic="true">
    <div><i class="fas fa-gem"></i><span>1 / 4</span></div>
    <div><i class="fas fa-star"></i><span>2 / 3</span></div>
    <div><i class="far fa-clock"></i><span>212</span></div>
    <div><i class="fas fa-ruble-sign"></i><span>915</span></div>
    <div><i class="fas fa-crown"></i><span>8 528</span></div>
  </div>
</aside>

<!-- Cart Sidebar -->
<aside id="cartSidebar" aria-label="Shopping Cart" aria-live="polite" aria-atomic="true" tabindex="-1">
  <header>
    <h3>Shopping Cart</h3>
    <button id="cartCloseBtn" aria-label="Close Cart">&times;</button>
  </header>
  <div id="cartItems"></div>
  <button id="confirmBtn" class="confirm-btn" aria-disabled="true" disabled>Confirm Order</button>
</aside>

<button id="cartBtn" aria-controls="cartSidebar" aria-expanded="false" aria-label="Open Shopping Cart" hidden>Cart</button>

<script>
  const cartSidebar = document.getElementById('cartSidebar');
  cartSidebar.style.pointerEvents = 'none';
  const cartCloseBtn = document.getElementById('cartCloseBtn');
  const cartItemsContainer = document.getElementById('cartItems');
  const confirmBtn = document.getElementById('confirmBtn');
  const cartSidebarToggle = document.getElementById('cartSidebarToggle');

  // Inject prices from PHP into JS
  const prices = <?= json_encode($prices, JSON_UNESCAPED_UNICODE) ?>;

  let cart = [];

  // Update cart UI with quantity and price
  function updateCartUI() {
    cartItemsContainer.innerHTML = '';
    if (cart.length === 0) {
      cartItemsContainer.innerHTML = '<p>Your cart is empty.</p>';
      confirmBtn.disabled = true;
      confirmBtn.setAttribute('aria-disabled', 'true');
      return;
    }
    confirmBtn.disabled = false;
    confirmBtn.setAttribute('aria-disabled', 'false');

    cart.forEach((item, index) => {
      const div = document.createElement('div');
      div.className = 'cart-item';
      div.tabIndex = 0;
      const price = getPrice(item.section, item.name);
      const totalPrice = (price * item.quantity).toFixed(2);
      div.innerHTML = `
        <div class="cart-item-name">${item.name} (${item.section})</div>
        <div>Quantity: ${item.quantity}</div>
        <div>Price per item: $${price.toFixed(2)}</div>
        <div>Total: $${totalPrice}</div>
        <button class="remove-btn" aria-label="Remove ${item.name} from cart" data-index="${index}">Remove</button>
      `;
      cartItemsContainer.appendChild(div);
    });

    // Remove item event listeners
    cartItemsContainer.querySelectorAll('.remove-btn').forEach(btn => {
      btn.onclick = (e) => {
        const idx = parseInt(e.target.getAttribute('data-index'));
        cart.splice(idx, 1);
        updateCartUI();
      };
    });
  }

  // Add item or increase quantity
document.querySelectorAll('.thumbnail').forEach(el => {
  el.addEventListener('click', () => {
    // Play click sound on thumbnail click
    clickSound.currentTime = 0;
    clickSound.play();

    const name = el.getAttribute('data-name');
    const section = el.getAttribute('data-section');
    const existingItem = cart.find(item => item.name === name && item.section === section);
    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      cart.push({ name, section, quantity: 1 });
    }
    updateCartUI();
  });
});

  function getPrice(section, name) {
    return prices[section] && prices[section][name] ? prices[section][name] : 0;
  }

function toggleCartSidebar(open) {
  if (open) {
    cartSidebar.classList.add('active');
    cartSidebar.style.pointerEvents = 'auto';
    cartSidebarToggle.setAttribute('aria-expanded', 'true');
    cartSidebar.focus();
  } else {
    cartSidebar.classList.remove('active');
    cartSidebar.style.pointerEvents = 'none';
    cartSidebarToggle.setAttribute('aria-expanded', 'false');
    cartSidebarToggle.focus();
  }
}



  cartSidebarToggle.addEventListener('click', () => {
    const isOpen = cartSidebar.classList.contains('active');
    toggleCartSidebar(!isOpen);
  });

  const clickSound = new Audio('images/click.mp3');
  clickSound.volume = 0.4;

  const keySound = new Audio('images/keypress.mp3');
  keySound.volume = 0.010;

  const music = document.getElementById('backgroundMusic');
music.volume = 0.025;  // set volume to 30%

  const elementsWithSound = [
    ...document.querySelectorAll('button'),
    ...document.querySelectorAll('.menu-btn'),
    document.querySelector('.fa-shopping-cart'),
    document.querySelector('.sidebar-icon')
  ];

  elementsWithSound.forEach(el => {
    el.addEventListener('click', () => {
      clickSound.currentTime = 0;
      clickSound.play();
    });
  });

  cartCloseBtn.addEventListener('click', () => {
    toggleCartSidebar(false);
  });

  confirmBtn.addEventListener('click', () => {
    if (cart.length === 0) return;

    const cartJson = JSON.stringify(cart);
    document.getElementById('cartDataInput').value = cartJson;
    document.getElementById('cartForm').submit();
  });

  updateCartUI();
</script>

<!-- Hidden form for order submission -->
<form id="cartForm" action="cart.php" method="POST" style="display: none;">
  <input type="hidden" name="cart_data" id="cartDataInput" />
  <input type="hidden" name="customer_id" value="<?= htmlspecialchars(uniqid('VAL-', true)) ?>" />
</form>

</body>
</html>