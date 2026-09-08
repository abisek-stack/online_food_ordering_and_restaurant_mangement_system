<?php
require_once 'customer_header.php';

// =================================================================================
// 1. PHP INITIALIZATION AND ORDER PROCESSING (FROM order.php)
// =================================================================================

// session_start(); // Note: customer_header.php likely already starts the session. If not, uncomment this.
require_once 'db.php';
require_once 'inventory_model.php';

// NOTE: This file assumes your login.php and order_model.php are functional.
// The model functions (getMenuItems, createOrder, getOrdersByUser) are commented
// out/mocked as their definitions were not provided.

if (!isset($_SESSION['username'])) { /* header("Location: login.php"); exit(); */ }
if (!isset($_SESSION['user_id'])) { /* die("Please update login.php to store user_id in session."); */ }
$current_user_id = (int)($_SESSION['user_id'] ?? 101); // Mock ID for UI if session not set
$current_user_name = "Jane Doe"; // Mock Name for Modal
$currency = "Rs.";
$tax_rate = 0.0; // TAX REMOVED: Set to 0% as requested

// require_once 'order_model.php'; // Model functions are not provided


$message = '';
$orderId  = null;

// --- Menu Data (49 Items) ---
$rawMenuItems = [
    ["id"=>1,"name"=>"Aloo Paratha (2 Pcs.) with Chhola","price"=>60,"image"=>"alooparatha.webp"],
    ["id"=>2,"name"=>"Plain Paratha (2 Pcs.) with Chhola","price"=>50,"image"=>"plain-paratha.jpeg"],
    ["id"=>3,"name"=>"Roti (3 Pcs.) with Chhola","price"=>45,"image"=>"roti-chhola.jpeg"],
    ["id"=>4,"name"=>"Plain Paratha (1 Pc.)","price"=>25,"image"=>"plain-paratha-1pc.jpeg"],
    ["id"=>5,"name"=>"Aloo Paratha (1 Pc.)","price"=>25,"image"=>"aaloo_paratcha-1pc.jpg"],

    ["id"=>6,"name"=>"Veg (Steam) Momo","price"=>100,"image"=>"veg-momo-steam.jpeg"],
    ["id"=>7,"name"=>"Veg (Fry) Momo","price"=>110,"image"=>"veg-momo-fry.jpeg"],
    ["id"=>8,"name"=>"Veg (Chilly) Momo","price"=>120,"image"=>"veg-momo-chilly.jpeg"],
    ["id"=>9,"name"=>"Chicken (Steam) Momo","price"=>110,"image"=>"chicken-momo-steam.jpeg"],
    ["id"=>10,"name"=>"Chicken (Fry) Momo","price"=>120,"image"=>"chicken-momo-fry.jpeg"],
    ["id"=>11,"name"=>"Chicken (Chilly) Momo","price"=>130,"image"=>"chicken-momo-chilly.jpeg"],
    ["id"=>12,"name"=>"Buff (Steam) Momo","price"=>120,"image"=>"buff-momo-steam.jpeg"],
    ["id"=>13,"name"=>"Buff (Fry) Momo","price"=>130,"image"=>"buff-momo-fry.jpeg"],
    ["id"=>14,"name"=>"Buff (Chilly) Momo","price"=>140,"image"=>"buff-momo-chilly.jpeg"],

    ["id"=>15,"name"=>"Chicken Chowmins","price"=>120,"image"=>"chicken-chowmein.jpg"],
    ["id"=>16,"name"=>"Buff Chowmins","price"=>130,"image"=>"buff-chowmein.jpg"],
    ["id"=>17,"name"=>"Veg Chowmins","price"=>110,"image"=>"veg-chowmein.jpg"],
    ["id"=>18,"name"=>"Egg Chowmins","price"=>115,"image"=>"egg-chowmein.jpg"],
    ["id"=>19,"name"=>"Mix Chowmins","price"=>140,"image"=>"mix-chowmein.jpg"],

    ["id"=>20,"name"=>"Mix Thukpa","price"=>110,"image"=>"mix-thukpa.jpg"],
    ["id"=>21,"name"=>"Buff Thukpa","price"=>100,"image"=>"buff-thukpa.jpg"],
    ["id"=>22,"name"=>"Chicken Thukpa","price"=>100,"image"=>"chicken-thukpa.jpg"],
    ["id"=>23,"name"=>"Veg Thukpa","price"=>90,"image"=>"veg-thukpa.jpg"],

    ["id"=>24,"name"=>"Veg Fry Rice","price"=>110,"image"=>"veg-fry-rice.jpg"],
    ["id"=>25,"name"=>"Mix Fry Rice","price"=>130,"image"=>"mix-fry-rice.jpg"],
    ["id"=>26,"name"=>"Chicken Fry Rice","price"=>120,"image"=>"chicken-fry-rice.jpg"],
    ["id"=>27,"name"=>"Buff Fry Rice","price"=>120,"image"=>"buff-fry-rice.jpg"],
    ["id"=>28,"name"=>"Egg Fry Rice","price"=>115,"image"=>"egg-fry-rice.jpg"],

    ["id"=>29,"name"=>"Mutton Thali (Sukuti)","price"=>160,"image"=>"mutton-thali.jpg"],
    ["id"=>30,"name"=>"Chicken Thali","price"=>150,"image"=>"chicken-thali.jpg"],
    ["id"=>31,"name"=>"Roti Thali","price"=>140,"image"=>"roti-thali.jpg"],
    ["id"=>32,"name"=>"Local Chicken with Rice/Dhido","price"=>170,"image"=>"local-chicken-dhido.jpg"],

    ["id"=>33,"name"=>"Mutton Curry","price"=>120,"image"=>"mutton-curry.jpg"],
    ["id"=>34,"name"=>"Chicken Curry","price"=>110,"image"=>"chicken-curry.jpg"],
    ["id"=>35,"name"=>"Sukuti Sadeko","price"=>130,"image"=>"sukuti-sadeko.jpg"],
    ["id"=>36,"name"=>"Chicken Chilly","price"=>140,"image"=>"chicken-chilly.jpg"],
    ["id"=>37,"name"=>"Buff Chilly","price"=>150,"image"=>"buff-chilly.jpg"],
    ["id"=>38,"name"=>"Sukuti Fry","price"=>150,"image"=>"sukuti-fry.jpg"],

    ["id"=>39,"name"=>"Black Tea","price"=>30,"image"=>"black-tea.jpg"],
    ["id"=>40,"name"=>"Milk Tea","price"=>40,"image"=>"milk-tea.jpg"],
    ["id"=>41,"name"=>"Masala Tea","price"=>40,"image"=>"masala-tea.jpg"],
    ["id"=>42,"name"=>"Black Coffee","price"=>40,"image"=>"black-coffee.jpg"],
    ["id"=>43,"name"=>"Milk Coffee","price"=>50,"image"=>"milk-coffee.jpg"],
    ["id"=>44,"name"=>"Soft Drink","price"=>50,"image"=>"soft-drink.jpg"],
    ["id"=>45,"name"=>"Water (Small)","price"=>20,"image"=>"water-small.jpg"],
    ["id"=>46,"name"=>"Water (Big)","price"=>30,"image"=>"water-big.jpg"],

    ["id"=>47,"name"=>"Plain Sweet Lassi","price"=>60,"image"=>"plain-lassi.jpg"],
    ["id"=>48,"name"=>"Banana Lassi","price"=>70,"image"=>"banana-lassi.jpg"],
    ["id"=>49,"name"=>"Lassi w/ Ice Cream","price"=>80,"image"=>"lassi-icecream.jpg"],
];

/**
 * Adds mock categories, descriptions, and ratings required for the cc.tct UI filters/sorts.
 */
function categorizeAndEnrichMenuItems($items) {
    $categorized = [];
    $categories = [
        'Paratha & Roti' => [1, 2, 3, 4, 5],
        'Momos' => [6, 7, 8, 9, 10, 11, 12, 13, 14],
        'Chowmein' => [15, 16, 17, 18, 19],
        'Thukpa' => [20, 21, 22, 23],
        'Fry Rice' => [24, 25, 26, 27, 28],
        'Thali & Mains' => [29, 30, 31, 32],
        'Curry & Sides' => [33, 34, 35, 36, 37, 38],
        'Beverages & Lassi' => [39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49],
    ];
    
    $idToCategory = [];
    foreach ($categories as $cat => $ids) {
        foreach ($ids as $id) {
            $idToCategory[$id] = $cat;
        }
    }

    foreach ($items as $item) {
        $item['category'] = $idToCategory[$item['id']] ?? 'Other';
        // Assign mock ratings (4.0 to 5.0)
        $item['rating'] = 4.0 + (mt_rand(0, 10) / 10.0); 
        // DESCRIPTION REMOVED: Set to empty string as requested
        $item['description'] = '';
        $categorized[] = $item;
    }
    return $categorized;
}

$menuItems = categorizeAndEnrichMenuItems($rawMenuItems);
$categories = array_unique(array_column($menuItems, 'category'));
// helper for coupon messages shown in the checkout modal
$couponMessage = '';


// --- POST Handling for Order Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['is_final_order'])) {
    $current_user_id = $_SESSION['user_id'] ?? null;
    $qtyPost = $_POST['qty'] ?? [];
    $customerName = $_POST['customer_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';

    // Build the cart: menu_item_id => qty
    $cart = [];
    foreach ($qtyPost as $id => $q) {
        $id = (int)$id;
        $q  = (int)$q;
        if ($q > 0) $cart[$id] = $q;
    }

    if (empty($cart)) {
        $message = "Cart is empty!";
    } else {
        // Calculate total from menu (use your existing menu array $rawMenuItems)
        $total = 0.0;
        foreach ($cart as $itemId => $qty) {
            foreach ($rawMenuItems as $menuItem) {
                if ($menuItem['id'] == $itemId) {
                    $total += $menuItem['price'] * $qty;
                    break;
                }
            }
        }

        // --------- COUPON VALIDATION & APPLY (optional) ----------
        $couponCode = trim($_POST['coupon_code'] ?? '');
        $couponMessage = ''; // reset for this request
        $discountAmount = 0.00;

        if ($couponCode !== '') {
            // Look up coupon in discounts table
            $cstmt = $conn->prepare("
                SELECT code, discount_type, value, start_date, end_date, status
                FROM discounts
                WHERE code = ? LIMIT 1
            ");
            $cstmt->bind_param("s", $couponCode);
            $cstmt->execute();
            $cres = $cstmt->get_result();

            if ($disc = $cres->fetch_assoc()) {
                $today = date('Y-m-d');
                // Check active status and date range
                if ($disc['status'] !== 'Active' || $today < $disc['start_date'] || $today > $disc['end_date']) {
                    $couponMessage = "Coupon '{$couponCode}' is not active or is expired.";
                } else {
                    // Compute discount
                    if ($disc['discount_type'] === 'percent') {
                        $discountAmount = round($total * ((float)$disc['value'] / 100.0), 2);
                    } else { // fixed
                        $discountAmount = (float)$disc['value'];
                    }
                    if ($discountAmount > $total) $discountAmount = $total;
                    $total = round(max(0, $total - $discountAmount), 2);
                    $couponMessage = "Coupon '{$couponCode}' applied. You saved Rs. " . number_format($discountAmount,2);
                }
            } else {
                $couponMessage = "Coupon code '{$couponCode}' not found.";
            }
            $cstmt->close();
        }
        // --------- END COUPON VALIDATION & APPLY ----------

        try {
            // Start transaction so order insert + ingredient deduction are atomic
            $conn->begin_transaction();

            // 1) Insert order (status PENDING)
            $stmt = $conn->prepare(
                "INSERT INTO orders (user_id, customer_name, phone, location, order_total, status) VALUES (?, ?, ?, ?, ?, 'PENDING')"
            );
            $stmt->bind_param("isssd", $current_user_id, $customerName, $phone, $location, $total);
            $stmt->execute();
            $orderId = $stmt->insert_id;
            $stmt->close();

            // 2) Insert order items (include unit_price if you track it)
            $stmtItems = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $itemId => $qty) {
                // find unit price from your $rawMenuItems array
                $unit_price = 0.0;
                foreach ($rawMenuItems as $mi) {
                    if ($mi['id'] == $itemId) { $unit_price = (float)$mi['price']; break; }
                }
                $stmtItems->bind_param("iiid", $orderId, $itemId, $qty, $unit_price);
                $stmtItems->execute();
            }
            $stmtItems->close();

            // 3) Deduct ingredients (this checks stock and throws error if not enough)
            deduct_ingredients_for_order($orderId, $cart);

            // 4) Commit the transaction
            $conn->commit();

            // Redirect to the same page (order_now.php)
            header("Location: order_now.php?success=1&order_id=" . (int)$orderId);
            exit;
        } catch (Throwable $e) {
            if ($conn->errno || $conn->error || method_exists($conn, 'rollback')) {
                $conn->rollback();
            }
            $message = "Order failed: " . $e->getMessage();
        }
    }
}

// $orders = getOrdersByUser($current_user_id); // Model function is undefined
$orders = []; // Mock orders for the "Your Recent Orders" table
$success = isset($_GET['success']) && $_GET['success'] === '1';
if ($success) $orderId = (int)($_GET['order_id'] ?? 0);
?>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // --- Tailwind Custom Configuration for Professional Branding (cc.tct) ---
    tailwind.config = {
        darkMode: 'class', // Enable class-based dark mode
        theme: {
            extend: {
                colors: {
                    'brand-primary': '#E94560',    // Swiggy/Zomato Red for CTAs and accents
                    'brand-dark': '#1A1A2E',       // Deep Navy/Dark Purple for dark surfaces
                    'brand-secondary': '#FFC400',  // Gold/Yellow for alerts/highlights
                    'surface-light': '#FFFFFF',
                    'surface-dark': '#2C2C40',
                },
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                boxShadow: {
                    'zomato': '0 4px 12px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05)',
                    'zomato-dark': '0 4px 12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 196, 0, 0.1)',
                }
            }
        }
    }
</script>
<style>
    /* =================================================================================
    2. CUSTOM CSS FOR AESTHETICS AND RESPONSIVENESS (cc.tct)
    ================================================================================= */
    /* Menu Item Hover Effect */
    .food-card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .food-card:hover {
        transform: translateY(-4px); /* Slight lift */
        box-shadow: 0 15px 30px rgba(233, 69, 96, 0.2); /* Red glow shadow */
    }

    /* Sticky Desktop Cart (Sidebar) */
    .sticky-cart-container {
        position: sticky;
        top: 2rem; /* Adjusted for layout */
        height: calc(100vh - 4rem);
        overflow-y: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .sticky-cart-container::-webkit-scrollbar {
        display: none;
    }

    /* Mobile Floating Cart */
    @media (max-width: 1024px) {
        .mobile-cart-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            transition: transform 0.3s ease-out;
        }
        .mobile-cart-fixed.hidden {
            transform: translateY(100%);
        }
    }

    /* Toast Notification Styling */
    #toast-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    .toast {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.3s, transform 0.3s;
    }
    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    
    <?php if ($success): ?>
        <div class="p-4 mb-6 rounded-lg bg-green-100 text-green-800 border-l-4 border-green-500 font-semibold">
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>Order #<?= htmlspecialchars($orderId) ?> placed successfully!</span>
            </div>
        </div>
    <?php elseif (!empty($message)): ?>
        <div class="p-4 mb-6 rounded-lg bg-red-100 text-red-800 border-l-4 border-red-500 font-semibold">
            <div class="flex items-center space-x-2">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                <span>Error: <?= htmlspecialchars($message) ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <h1 style="font-size: 28px; color: #2c2f48;" class="text-4xl font-extrabold mb-2 text-gray-800 dark:text-gray-100">
        Order Now
    </h1>
    <p class="mb-8 text-gray-600 dark:text-gray-400">Choose your favorite dishes and place an order with ease!</p>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8 xl:col-span-9">

            <div class="bg-white dark:bg-surface-dark p-4 rounded-xl shadow-zomato dark:shadow-zomato-dark mb-8 sticky top-20 z-30 transition duration-300 border-t-4 border-brand-primary">
                <div class="flex flex-wrap items-center gap-4">
                    <label class="font-semibold text-sm text-gray-700 dark:text-gray-300 flex-shrink-0">Filters:</label>
                    
                    <select id="categoryFilter" class="flex-grow md:flex-grow-0 px-4 py-2 border rounded-full text-sm dark:bg-brand-dark/70 dark:border-gray-700 focus:ring-brand-secondary focus:border-brand-secondary transition">
                        <option value="All">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select id="priceFilter" class="flex-grow md:flex-grow-0 px-4 py-2 border rounded-full text-sm dark:bg-brand-dark/70 dark:border-gray-700 focus:ring-brand-secondary focus:border-brand-secondary transition">
                        <option value="All">Price Range</option>
                        <option value="low">Under <?= $currency ?>100</option>
                        <option value="high"><?= $currency ?>100 - <?= $currency ?>200</option>
                        <option value="premium">Over <?= $currency ?>200</option>
                    </select>
                    
                    <select id="sortOption" class="flex-grow md:flex-grow-0 px-4 py-2 border rounded-full text-sm dark:bg-brand-dark/70 dark:border-gray-700 focus:ring-brand-secondary focus:border-brand-secondary transition">
                        <option value="default">Sort By: Recommended</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                        <option value="rating-desc">Rating: High to Low</option>
                    </select>

                    <div class="relative flex-grow">
                        <input type="text" id="mainSearchInput" placeholder="Search dishes..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-full shadow-inner dark:bg-brand-dark/50 focus:ring-2 focus:ring-brand-primary focus:outline-none transition duration-200 text-sm dark:text-gray-200">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div id="menuGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($menuItems as $item): ?>
                <div class="food-card bg-white dark:bg-surface-dark rounded-xl overflow-hidden shadow-zomato dark:shadow-zomato-dark flex flex-col menu-item"
                     data-id="<?= $item['id'] ?>"
                     data-category="<?= htmlspecialchars($item['category']) ?>"
                     data-price="<?= $item['price'] ?>"
                     data-rating="<?= $item['rating'] ?>"
                     data-name="<?= htmlspecialchars($item['name']) ?>">
                    
                    <div class="h-48 overflow-hidden relative">
                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" 
                             alt="<?= htmlspecialchars($item['name']) ?>" 
                             onerror="this.onerror=null;this.src='https://placehold.co/600x480/333333/FFFFFF?text=Dish';"
                             class="w-full h-full object-cover transition duration-500 hover:scale-110">
                        <span class="absolute top-2 right-2 px-3 py-1 text-xs font-bold text-white bg-brand-primary/90 rounded-full shadow-lg flex items-center">
                            <i data-lucide="star" class="w-3 h-3 mr-1 fill-brand-secondary text-brand-secondary"></i>
                            <?= number_format($item['rating'], 1) ?>
                        </span>
                    </div>

                    <div class="p-4 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-lg mb-1 text-gray-900 dark:text-gray-100 leading-tight"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="text-gray-500 dark:text-gray-400 text-xs mb-3 font-medium italic">Category: <?= htmlspecialchars($item['category']) ?></p>
                        </div>
                        
                        <div class="flex items-center justify-between mt-3 pt-3 border-t dark:border-gray-700">
                            <span class="font-extrabold text-2xl text-brand-primary dark:text-brand-secondary">
                                <?= $currency ?><?= number_format($item['price'], 0) ?>
                            </span>

                            <button type="button" 
                                    onclick="addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>)"
                                    class="add-to-cart-btn px-4 py-2 bg-brand-primary text-white font-semibold rounded-lg shadow-lg shadow-brand-primary/50 hover:bg-brand-primary/90 transition duration-200 flex items-center space-x-1 active:scale-[0.98]">
                                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                                <span>Add</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="lg:col-span-4 xl:col-span-3 hidden lg:block">
            <div class="sticky-cart-container bg-white dark:bg-surface-dark rounded-xl shadow-zomato dark:shadow-zomato-dark p-6 border-l-4 border-brand-primary">
                
                <h3 class="text-2xl font-bold mb-4 flex items-center justify-between text-gray-800 dark:text-gray-100">
                    Your Order Cart
                    <span id="desktop-item-count" class="text-sm font-semibold px-3 py-1 bg-brand-secondary text-brand-dark rounded-full">0 Items</span>
                </h3>
                
                <div id="desktop-cart-list" class="space-y-4 pb-4">
                    <p class="text-center text-gray-500 dark:text-gray-400 italic py-8" id="empty-cart-message-desktop">
                        Your cart is empty. Add some delicious food!
                    </p>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    
                    <div class="flex justify-between font-medium text-lg text-gray-600 dark:text-gray-300">
                        <span>Subtotal</span>
                        <span id="desktop-subtotal" class="font-semibold"><?= $currency ?> 0.00</span>
                    </div>
                    <div class="flex justify-between font-extrabold text-xl mb-4 border-t dark:border-gray-600 pt-2">
                        <span>Grand Total</span>
                        <span id="desktop-grand-total" class="text-brand-primary dark:text-brand-secondary"><?= $currency ?> 0.00</span>
                    </div>
                    
                    <button type="button" onclick="openCheckoutModal()" id="desktop-checkout-btn" disabled
                        class="w-full px-6 py-3 bg-brand-primary text-white font-extrabold text-lg rounded-xl shadow-lg shadow-brand-primary/50 transition duration-200 opacity-50 cursor-not-allowed flex items-center justify-center space-x-2">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                        <span>Proceed to Checkout</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>
</main>


<div id="mobile-cart-fixed" class="mobile-cart-fixed hidden bg-white dark:bg-brand-dark shadow-2xl p-3 border-t-2 border-brand-primary">
    <button type="button" onclick="openCheckoutModal()"
            class="w-full px-4 py-3 bg-brand-primary text-white font-extrabold text-xl rounded-xl shadow-lg shadow-brand-primary/50 transition duration-200 flex items-center justify-between hover:scale-[1.01] active:scale-[0.99]">
        <div class="flex items-center space-x-2">
            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            <span id="mobile-total-items">0 Items</span>
        </div>
        <span id="mobile-grand-total"><?= $currency ?> 0.00</span>
    </button>
</div>


<div id="checkoutModal" class="fixed inset-0 bg-black bg-opacity-70 z-[1000] hidden flex items-center justify-center p-4 transition-opacity modal-leave-to">
    <div class="bg-white dark:bg-surface-dark w-full max-w-lg rounded-xl shadow-2xl transform scale-95 transition-transform duration-300 ease-in-out p-6">
        
        <div class="flex justify-between items-center pb-4 border-b dark:border-gray-700 mb-4">
            <h3 class="text-2xl font-extrabold text-brand-primary dark:text-brand-secondary flex items-center space-x-2">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
                <span>Confirm Delivery</span>
            </h3>
            <button onclick="closeCheckoutModal()" class="text-gray-500 hover:text-brand-primary dark:text-gray-400 dark:hover:text-brand-secondary transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="max-h-60 overflow-y-auto pr-2 mb-4 space-y-3 border p-3 rounded-lg dark:border-gray-700">
            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2 border-b dark:border-gray-700 pb-2">Your Items:</h4>
            <div id="modal-cart-summary"></div>
        </div>

        <form method="POST" id="deliveryForm" action="order_now.php">
            <input type="hidden" name="is_final_order" value="1">
            <div id="hidden-cart-inputs"></div>
            <div class="space-y-4">
                <input type="text" name="customer_name" placeholder="Full Name" required class="w-full p-2 border rounded-lg dark:bg-brand-dark/70 dark:border-gray-700">
                <input type="tel" name="phone" placeholder="Phone Number" required class="w-full p-2 border rounded-lg dark:bg-brand-dark/70 dark:border-gray-700">
                <textarea name="location" placeholder="Delivery Address ..." required rows="3" class="w-full p-2 border rounded-lg dark:bg-brand-dark/70 dark:border-gray-700"></textarea>
                
                <div class="mb-3">
                    <label for="coupon_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coupon code (optional)</label>
                    <input type="text" id="coupon_code" name="coupon_code" placeholder="Enter coupon code" class="w-full mt-1 p-2 border rounded-lg dark:bg-brand-dark/70 dark:border-gray-700"/>
                    <?php if (!empty($couponMessage)): ?>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1"><?= htmlspecialchars($couponMessage) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-6 p-4 bg-brand-primary/10 dark:bg-brand-secondary/10 rounded-lg flex justify-between items-center">
                <p class="text-xl font-extrabold text-gray-800 dark:text-gray-100">Total Payable</p>
                <p id="modal-final-total" class="text-3xl font-black text-brand-primary dark:text-brand-secondary"><?= $currency ?> 0.00</p>
            </div>

            <button type="submit"
                    class="w-full mt-6 px-6 py-3 bg-brand-primary text-white font-extrabold text-lg rounded-xl shadow-lg shadow-brand-primary/50 hover:bg-brand-primary/90 transition duration-200 flex items-center justify-center space-x-2 active:scale-[0.99]">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>Confirm Order & Pay Now</span>
            </button>
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-12">
    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100 border-b pb-2 dark:border-gray-700">Your Recent Orders</h2>
    <div class="bg-white dark:bg-surface-dark rounded-xl shadow-zomato dark:shadow-zomato-dark p-6 overflow-x-auto">
        <?php if (empty($orders)): ?>
            <p class="text-gray-500 dark:text-gray-400">No orders yet.</p>
        <?php else: ?>
            <table class="w-full text-left min-w-max">
                <thead>
                    <tr class="text-gray-600 dark:text-gray-300 border-b dark:border-gray-700">
                        <th class="p-3 text-sm font-semibold">Order #</th>
                        <th class="p-3 text-sm font-semibold">Date</th>
                        <th class="p-3 text-sm font-semibold">Status</th>
                        <th class="p-3 text-sm font-semibold">Total (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-brand-dark/50 transition">
                            <td class="p-3 font-medium">#<?= (int)$o['id'] ?></td>
                            <td class="p-3 text-sm"><?= htmlspecialchars($o['created_at']) ?></td>
                            <td class="p-3 text-sm">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full 
                                    <?php 
                                        $status = strtolower($o['status']);
                                        if ($status === 'delivered') echo 'bg-green-100 text-green-800';
                                        elseif ($status === 'processing') echo 'bg-blue-100 text-blue-800';
                                        else echo 'bg-yellow-100 text-yellow-800';
                                    ?>
                                ">
                                    <?= htmlspecialchars($o['status']) ?>
                                </span>
                            </td>
                            <td class="p-3 font-bold text-brand-primary dark:text-brand-secondary"><?= $currency ?><?= number_format((float)$o['order_total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div id="toast-container"></div>

<script>
    // Global Constants
    const CURRENCY = '<?= $currency ?>';
    const TAX_RATE = 0; // TAX REMOVED: Set to 0

    // --- State Management ---
    let cart = {};
    const menuItemsData = <?= json_encode($menuItems); ?>;

    // --- DOM Elements ---
    const menuGrid = document.getElementById('menuGrid');
    const desktopList = document.getElementById('desktop-cart-list');
    const desktopCount = document.getElementById('desktop-item-count');
    const desktopSubtotalEl = document.getElementById('desktop-subtotal');
    const desktopGrandTotalEl = document.getElementById('desktop-grand-total');
    const desktopCheckoutBtn = document.getElementById('desktop-checkout-btn');
    const mobileCartFixed = document.getElementById('mobile-cart-fixed');
    const mobileTotalItemsEl = document.getElementById('mobile-total-items');
    const mobileGrandTotalEl = document.getElementById('mobile-grand-total');
    const checkoutModal = document.getElementById('checkoutModal');
    const modalCartSummary = document.getElementById('modal-cart-summary');
    const modalFinalTotalEl = document.getElementById('modal-final-total');
    const toastContainer = document.getElementById('toast-container');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');
    const sortOption = document.getElementById('sortOption');
    const hiddenCartInputs = document.getElementById('hidden-cart-inputs');
    const mainSearchInput = document.getElementById('mainSearchInput');

    // --- Cart Modification Functions ---
    function addToCart(id, name, price) {
        id = String(id); 
        if (cart[id]) {
            cart[id].qty += 1;
        } else {
            cart[id] = { name, price, qty: 1 };
        }
        showToast(`${name} added to cart!`, 'success');
        updateCartUI();
    }

    function changeQuantity(id, newQty) {
        id = String(id);
        if (newQty <= 0) {
            delete cart[id];
            showToast('Item removed from cart.', 'info');
        } else {
            cart[id].qty = newQty;
            showToast('Cart updated.', 'info');
        }
        updateCartUI();
    }

    // --- UI Rendering and Calculation ---
    function updateCartUI() {
        let subtotal = 0;
        let totalItems = 0;
        const hasItems = Object.keys(cart).length > 0;

        for (const id in cart) {
            const item = cart[id];
            subtotal += item.qty * item.price;
            totalItems += item.qty;
        }

        const grandTotal = subtotal;
        const formatPrice = (p) => `${CURRENCY} ${p.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;

        if (desktopSubtotalEl) desktopSubtotalEl.textContent = formatPrice(subtotal);
        if (desktopGrandTotalEl) desktopGrandTotalEl.textContent = formatPrice(grandTotal);
        if (desktopCount) desktopCount.textContent = `${totalItems} Items`;
        if (desktopCheckoutBtn) {
            desktopCheckoutBtn.disabled = !hasItems;
            desktopCheckoutBtn.classList.toggle('opacity-50', !hasItems);
            desktopCheckoutBtn.classList.toggle('cursor-not-allowed', !hasItems);
        }
        
        if (mobileTotalItemsEl) mobileTotalItemsEl.textContent = `${totalItems} Items`;
        if (mobileGrandTotalEl) mobileGrandTotalEl.textContent = formatPrice(grandTotal);
        if (mobileCartFixed) mobileCartFixed.classList.toggle('hidden', !hasItems);

        renderCartList(hasItems, formatPrice, desktopList, 'empty-cart-message-desktop');
        renderCartList(hasItems, formatPrice, modalCartSummary, 'empty-cart-message-modal');
        
        if (modalFinalTotalEl) modalFinalTotalEl.textContent = formatPrice(grandTotal);
        
        generateHiddenFormInputs();
        applyFiltersAndSorting(); // Re-render menu to update button states
    }
    
    function renderCartList(hasItems, formatPrice, containerElement, emptyMessageId) {
        if (!containerElement) return;
        containerElement.innerHTML = '';

        if (!hasItems) {
            containerElement.innerHTML = `<p class="text-center text-gray-500 dark:text-gray-400 italic py-8" id="${emptyMessageId}">Your cart is empty.</p>`;
            return;
        }

        for (const id in cart) {
            const item = cart[id];
            const itemTotal = item.qty * item.price;
            
            const itemHtml = `
                <div class="flex items-center justify-between p-2 border-b dark:border-gray-700 last:border-b-0">
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <button type="button" onclick="changeQuantity(${id}, ${item.qty - 1})" class="w-6 h-6 rounded-full bg-brand-primary/10 dark:bg-brand-primary/20 text-brand-primary hover:bg-brand-primary/30 transition">
                            <i data-lucide="minus" class="w-4 h-4 mx-auto"></i>
                        </button>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">${item.qty}</span>
                        <button type="button" onclick="changeQuantity(${id}, ${item.qty + 1})" class="w-6 h-6 rounded-full bg-brand-primary/10 dark:bg-brand-primary/20 text-brand-primary hover:bg-brand-primary/30 transition">
                            <i data-lucide="plus" class="w-4 h-4 mx-auto"></i>
                        </button>
                    </div>
                    <span class="flex-grow text-sm font-medium text-gray-700 dark:text-gray-300 mx-3 truncate">${item.name}</span>
                    <span class="font-bold text-sm text-brand-primary dark:text-brand-secondary flex-shrink-0">${formatPrice(itemTotal)}</span>
                </div>
            `;
            containerElement.innerHTML += itemHtml;
        }
        lucide.createIcons();
    }

    function generateHiddenFormInputs() {
        if (!hiddenCartInputs) return;
        hiddenCartInputs.innerHTML = '';
        for (const id in cart) {
            const item = cart[id];
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `qty[${id}]`;
            input.value = item.qty;
            hiddenCartInputs.appendChild(input);
        }
    }

    // --- Modal Functions ---
    function openCheckoutModal() {
        if (Object.keys(cart).length === 0) {
            showToast('Your cart is empty.', 'error');
            return;
        }
        updateCartUI(); 
        checkoutModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            checkoutModal.children[0].classList.remove('scale-95');
            checkoutModal.classList.remove('modal-leave-to');
        }, 10);
    }

    function closeCheckoutModal() {
        checkoutModal.children[0].classList.add('scale-95');
        checkoutModal.classList.add('modal-leave-to');
        setTimeout(() => {
            checkoutModal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    // --- Filtering and Sorting ---
    if(categoryFilter) categoryFilter.addEventListener('change', applyFiltersAndSorting);
    if(priceFilter) priceFilter.addEventListener('change', applyFiltersAndSorting);
    if(sortOption) sortOption.addEventListener('change', applyFiltersAndSorting);
    if(mainSearchInput) mainSearchInput.addEventListener('keyup', applyFiltersAndSorting);

    function applyFiltersAndSorting() {
        const selectedCategory = categoryFilter.value;
        const selectedPriceRange = priceFilter.value;
        const selectedSort = sortOption.value;
        const searchTerm = mainSearchInput.value.toLowerCase();
        
        let filteredItems = menuItemsData.filter(item => {
            if (selectedCategory !== 'All' && item.category !== selectedCategory) return false;
            if (selectedPriceRange === 'low' && item.price >= 100) return false;
            if (selectedPriceRange === 'high' && (item.price < 100 || item.price > 200)) return false;
            if (selectedPriceRange === 'premium' && item.price <= 200) return false;
            if (searchTerm && !item.name.toLowerCase().includes(searchTerm)) return false;
            return true;
        });

        filteredItems.sort((a, b) => {
            switch (selectedSort) {
                case 'price-asc': return a.price - b.price;
                case 'price-desc': return b.price - a.price;
                case 'rating-desc': return b.rating - a.rating;
                default: return 0;
            }
        });
        renderMenuGrid(filteredItems);
    }

    function renderMenuGrid(items) {
        if (!menuGrid) return;
        menuGrid.innerHTML = '';
        
        if (items.length === 0) {
             menuGrid.innerHTML = '<p class="text-xl text-center text-gray-500 dark:text-gray-400 py-12 col-span-full">No items found.</p>';
             return;
        }

        items.forEach(item => {
            const inCart = cart[item.id];
            const isCartedClass = inCart ? 'bg-green-500 hover:bg-green-400' : 'bg-brand-primary hover:bg-brand-primary/90';
            const buttonText = inCart ? `Added (${inCart.qty})` : 'Add';
            const icon = inCart ? 'check' : 'plus-circle';

            const itemHtml = `
                <div class="food-card bg-white dark:bg-surface-dark rounded-xl overflow-hidden shadow-zomato dark:shadow-zomato-dark flex flex-col menu-item" data-id="${item.id}">
                    <div class="h-48 overflow-hidden relative">
                        <img src="uploads/${item.image}" alt="${item.name}" onerror="this.onerror=null;this.src='https://placehold.co/600x480/333333/FFFFFF?text=Dish';" class="w-full h-full object-cover transition duration-500 hover:scale-110">
                        <span class="absolute top-2 right-2 px-3 py-1 text-xs font-bold text-white bg-brand-primary/90 rounded-full shadow-lg flex items-center">
                            <i data-lucide="star" class="w-3 h-3 mr-1 fill-brand-secondary text-brand-secondary"></i>
                            ${item.rating.toFixed(1)}
                        </span>
                    </div>
                    <div class="p-4 flex-grow flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-lg mb-1 text-gray-900 dark:text-gray-100 leading-tight">${item.name}</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-xs mb-3 font-medium italic">Category: ${item.category}</p>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-3 border-t dark:border-gray-700">
                            <span class="font-extrabold text-2xl text-brand-primary dark:text-brand-secondary">${CURRENCY} ${item.price.toFixed(0)}</span>
                            <button type="button" onclick="addToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', ${item.price})" class="add-to-cart-btn px-4 py-2 ${isCartedClass} text-white font-semibold rounded-lg shadow-lg shadow-brand-primary/50 transition duration-200 flex items-center space-x-1 active:scale-[0.98]">
                                <i data-lucide="${icon}" class="w-5 h-5"></i>
                                <span>${buttonText}</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            menuGrid.innerHTML += itemHtml;
        });
        lucide.createIcons();
    }
    
    // --- Toast Notification Logic ---
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const typeClasses = {
            success: 'bg-green-600 text-white',
            error: 'bg-red-600 text-white',
            info: 'bg-blue-600 text-white'
        };
        const icons = {
            success: '<i data-lucide="check-circle" class="w-5 h-5"></i>',
            error: '<i data-lucide="alert-triangle" class="w-5 h-5"></i>',
            info: '<i data-lucide="info" class="w-5 h-5"></i>'
        };
        toast.className = `toast p-4 rounded-xl shadow-lg mt-3 flex items-center space-x-3 ${typeClasses[type]}`;
        toast.innerHTML = `${icons[type]} <span>${message}</span>`;
        toastContainer.prepend(toast); 
        
        setTimeout(() => {
            toast.classList.add('show');
            lucide.createIcons();
        }, 10);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300); 
        }, 3000);
    }
    
    // --- Initialization ---
    // Note: Dark mode logic is assumed to be in customer_header.php or a global script.
    // If not, the dark mode toggle logic from order.php should be included here.
    window.onload = function() {
        lucide.createIcons();
        updateCartUI();
    };
</script>

<?php require_once 'customer_footer.php'; ?>