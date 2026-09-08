<?php
// Set the timezone to Nepal
date_default_timezone_set('Asia/Kathmandu');

// --- HOME DELIVERY STATUS LOGIC ---
$current_hour = (int)date('G');
$delivery_status = ($current_hour >= 6 && $current_hour < 22)
    ? ['text' => 'Open', 'class' => 'open']
    : ['text' => 'Closed', 'class' => 'closed'];

// --- MENU ITEMS DATA ---
$menu = [
    ["id"=>1,"name"=>"Aloo Paratha (2 Pcs.) with Chhola","price"=>60,"image"=>"alooparatha.webp", "category" => "Parathas & Rotis"],
    ["id"=>2,"name"=>"Plain Paratha (2 Pcs.) with Chhola","price"=>50,"image"=>"plain-paratha.jpeg", "category" => "Parathas & Rotis"],
    ["id"=>3,"name"=>"Roti (3 Pcs.) with Chhola","price"=>45,"image"=>"roti-chhola.jpeg", "category" => "Parathas & Rotis"],
    ["id"=>4,"name"=>"Plain Paratha (1 Pc.)","price"=>25,"image"=>"plain-paratha-1pc.jpeg", "category" => "Parathas & Rotis"],
    ["id"=>5,"name"=>"Aloo Paratha (1 Pc.)","price"=>25,"image"=>"aaloo_paratcha-1pc.jpg", "category" => "Parathas & Rotis"],

    ["id"=>6,"name"=>"Veg (Steam) Momo","price"=>100,"image"=>"veg-momo-steam.jpeg", "category" => "Momos"],
    ["id"=>7,"name"=>"Veg (Fry) Momo","price"=>110,"image"=>"veg-momo-fry.jpeg", "category" => "Momos"],
    ["id"=>8,"name"=>"Veg (Chilly) Momo","price"=>120,"image"=>"veg-momo-chilly.jpeg", "category" => "Momos"],
    ["id"=>9,"name"=>"Chicken (Steam) Momo","price"=>110,"image"=>"chicken-momo-steam.jpeg", "category" => "Momos"],
    ["id"=>10,"name"=>"Chicken (Fry) Momo","price"=>120,"image"=>"chicken-momo-fry.jpeg", "category" => "Momos"],
    ["id"=>11,"name"=>"Chicken (Chilly) Momo","price"=>130,"image"=>"chicken-momo-chilly.jpeg", "category" => "Momos"],
    ["id"=>12,"name"=>"Buff (Steam) Momo","price"=>120,"image"=>"buff-momo-steam.jpeg", "category" => "Momos"],
    ["id"=>13,"name"=>"Buff (Fry) Momo","price"=>130,"image"=>"buff-momo-fry.jpeg", "category" => "Momos"],
    ["id"=>14,"name"=>"Buff (Chilly) Momo","price"=>140,"image"=>"buff-momo-chilly.jpeg", "category" => "Momos"],

    ["id"=>15,"name"=>"Chicken Chowmein","price"=>120,"image"=>"chicken-chowmein.jpg", "category" => "Chowmein"],
    ["id"=>16,"name"=>"Buff Chowmein","price"=>130,"image"=>"buff-chowmein.jpg", "category" => "Chowmein"],
    ["id"=>17,"name"=>"Veg Chowmein","price"=>110,"image"=>"veg-chowmein.jpg", "category" => "Chowmein"],
    ["id"=>18,"name"=>"Egg Chowmein","price"=>115,"image"=>"egg-chowmein.jpg", "category" => "Chowmein"],
    ["id"=>19,"name"=>"Mix Chowmein","price"=>140,"image"=>"mix-chowmein.jpg", "category" => "Chowmein"],

    ["id"=>20,"name"=>"Mix Thukpa","price"=>110,"image"=>"mix-thukpa.jpg", "category" => "Thukpa"],
    ["id"=>21,"name"=>"Buff Thukpa","price"=>100,"image"=>"buff-thukpa.jpg", "category" => "Thukpa"],
    ["id"=>22,"name"=>"Chicken Thukpa","price"=>100,"image"=>"chicken-thukpa.jpg", "category" => "Thukpa"],
    ["id"=>23,"name"=>"Veg Thukpa","price"=>90,"image"=>"veg-thukpa.jpg", "category" => "Thukpa"],

    ["id"=>24,"name"=>"Veg Fry Rice","price"=>110,"image"=>"veg-fry-rice.jpg", "category" => "Fry Rice"],
    ["id"=>25,"name"=>"Mix Fry Rice","price"=>130,"image"=>"mix-fry-rice.jpg", "category" => "Fry Rice"],
    ["id"=>26,"name"=>"Chicken Fry Rice","price"=>120,"image"=>"chicken-fry-rice.jpg", "category" => "Fry Rice"],
    ["id"=>27,"name"=>"Buff Fry Rice","price"=>120,"image"=>"buff-fry-rice.jpg", "category" => "Fry Rice"],
    ["id"=>28,"name"=>"Egg Fry Rice","price"=>115,"image"=>"egg-fry-rice.jpg", "category" => "Fry Rice"],

    ["id"=>29,"name"=>"Mutton Thali (Sukuti)","price"=>160,"image"=>"mutton-thali.jpg", "category" => "Thali & Meals"],
    ["id"=>30,"name"=>"Chicken Thali","price"=>150,"image"=>"chicken-thali.jpg", "category" => "Thali & Meals"],
    ["id"=>31,"name"=>"Roti Thali","price"=>140,"image"=>"roti-thali.jpg", "category" => "Thali & Meals"],
    ["id"=>32,"name"=>"Local Chicken with Rice/Dhido","price"=>170,"image"=>"local-chicken-dhido.jpg", "category" => "Thali & Meals"],

    ["id"=>33,"name"=>"Mutton Curry","price"=>120,"image"=>"mutton-curry.jpg", "category" => "Curries & Sides"],
    ["id"=>34,"name"=>"Chicken Curry","price"=>110,"image"=>"chicken-curry.jpg", "category" => "Curries & Sides"],
    ["id"=>35,"name"=>"Sukuti Sadeko","price"=>130,"image"=>"sukuti-sadeko.jpg", "category" => "Curries & Sides"],
    ["id"=>36,"name"=>"Chicken Chilly","price"=>140,"image"=>"chicken-chilly.jpg", "category" => "Curries & Sides"],
    ["id"=>37,"name"=>"Buff Chilly","price"=>150,"image"=>"buff-chilly.jpg", "category" => "Curries & Sides"],
    ["id"=>38,"name"=>"Sukuti Fry","price"=>150,"image"=>"sukuti-fry.jpg", "category" => "Curries & Sides"],

    ["id"=>39,"name"=>"Black Tea","price"=>30,"image"=>"black-tea.jpg", "category" => "Beverages"],
    ["id"=>40,"name"=>"Milk Tea","price"=>40,"image"=>"milk-tea.jpg", "category" => "Beverages"],
    ["id"=>41,"name"=>"Masala Tea","price"=>40,"image"=>"masala-tea.jpg", "category" => "Beverages"],
    ["id"=>42,"name"=>"Black Coffee","price"=>40,"image"=>"black-coffee.jpg", "category" => "Beverages"],
    ["id"=>43,"name"=>"Milk Coffee","price"=>50,"image"=>"milk-coffee.jpg", "category" => "Beverages"],
    ["id"=>44,"name"=>"Soft Drink","price"=>50,"image"=>"soft-drink.jpg", "category" => "Beverages"],
    ["id"=>45,"name"=>"Water (Small)","price"=>20,"image"=>"water-small.jpg", "category" => "Beverages"],
    ["id"=>46,"name"=>"Water (Big)","price"=>30,"image"=>"water-big.jpg", "category" => "Beverages"],

    ["id"=>47,"name"=>"Plain Sweet Lassi","price"=>60,"image"=>"plain-lassi.jpg", "category" => "Lassi"],
    ["id"=>48,"name"=>"Banana Lassi","price"=>70,"image"=>"banana-lassi.jpg", "category" => "Lassi"],
    ["id"=>49,"name"=>"Lassi w/ Ice Cream","price"=>80,"image"=>"lassi-icecream.jpg", "category" => "Lassi"]
];

// Get 8 popular dishes for the featured section
$featured_dishes = array_slice($menu, 5, 8);


$categories = [];
foreach ($menu as $item) {
    $categories[$item['category']][] = $item;
}

// --- Menu Book Pagination Logic (Two-Page Spread) ---
$book_pages = [];
$items_per_page_side = 10; // Max items per single page (20 per spread)

// Page 0: Front Cover
$book_pages[] = [
    'type' => 'cover',
    'side' => 'right', // Cover page is always the right-hand page when closed
    'name' => 'Flavoro Menu'
];

// Page 1: Index/Welcome Page (Left Side)
$book_pages[] = [
    'type' => 'index',
    'side' => 'left',
    'content' => 'Welcome to Flavoro. Enjoy our curated selection of authentic Nepalese and Indian cuisine. Turn the page to begin your culinary journey.'
];

$page_side = 'right'; // Start filling the right page after the index

foreach ($categories as $categoryName => $categoryItems) {
    // 1. Add Category Header Page
    $book_pages[] = [
        'type' => 'category_header',
        'side' => $page_side,
        'name' => $categoryName,
        'image' => $categoryItems[0]['image'] ?? 'default.jpg'
    ];
    // Switch to the opposite side for the next page
    $page_side = $page_side === 'left' ? 'right' : 'left';

    // 2. Add Menu Item Pages
    $chunks = array_chunk($categoryItems, $items_per_page_side);
    foreach ($chunks as $pageItems) {
        $book_pages[] = [
            'type' => 'menu_page',
            'side' => $page_side,
            'items' => $pageItems,
            'category' => $categoryName
        ];
        // Switch to the opposite side for the next page
        $page_side = $page_side === 'left' ? 'right' : 'left';
    }
}

// Ensure the last functional page is on the left side, so the back cover is on the right
if ($page_side === 'right') {
    $book_pages[] = [
        'type' => 'blank',
        'side' => 'right',
        'content' => '---'
    ];
    $page_side = 'left';
}

// Add a final 'contact/thank you' page (Left Side before Back Cover)
$book_pages[] = [
    'type' => 'footer',
    'side' => 'left',
    'name' => 'Thank You'
];

// Back Cover is handled by CSS/JS and is not included in this $book_pages array,
// as the pages array size determines the last available spread.

$totalPages = count($book_pages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavoro - Delicious Food, Delivered.</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* --- Custom & Merged Styles --- */
        :root {
            --primary-color: #f97316; /* Tailwind orange-500 */
            --dark-color: #0a0a0a; /* neutral-950 */
            --mid-dark: #171717; /* neutral-900 */
            --text-light: #e5e5e5; /* neutral-200 */
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            background-color: var(--dark-color);
            color: var(--text-light);
            overflow-x: hidden;
        }

        .lux-font {
            font-family: 'Playfair Display', serif;
        }

        /* Hero Section Custom BG */
        .hero-section {
            background-image: linear-gradient(rgba(10, 10, 10, 0.6), rgba(10, 10, 10, 0.8)), url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            min-height: 90vh; /* Full viewport height */
        }
        
        /* Custom Keyframe for CTA Pulse */
        @keyframes glowing {
            0% { box-shadow: 0 0 5px var(--primary-color), 0 0 10px var(--primary-color); }
            50% { box-shadow: 0 0 20px var(--primary-color), 0 0 40px var(--primary-color); }
            100% { box-shadow: 0 0 5px var(--primary-color), 0 0 10px var(--primary-color); }
        }

        .cta-button-glowing:hover {
            animation: glowing 1.5s infinite;
        }

        /* Smooth Mobile Menu Panel */
        .menu-panel {
            /* ... (removed since we are using inline search now) ... */
        }

        /* Custom scrollbar for dark theme */
        .scrollable-content::-webkit-scrollbar {
            width: 8px;
        }
        .scrollable-content::-webkit-scrollbar-track {
            background: #262626; /* neutral-800 */
        }
        .scrollable-content::-webkit-scrollbar-thumb {
            background: #404040; /* neutral-600 */
            border-radius: 4px;
        }
        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* --- Menu Book Styles (Updated for 2-page spread) --- */
        .menu-book-container {
            position: relative;
            perspective: 2000px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 600px;
        }
        
        .menu-book {
            position: relative;
            width: 90%; /* Use 90% of container width */
            max-width: 900px; /* **Significantly bigger** */
            height: 600px; /* Taller height */
            transform-style: preserve-3d;
            transition: transform 1s;
        }
        
        /* Two-page spread container */
        .spread {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex; /* Pages within spread are side-by-side */
            transform-style: preserve-3d;
            
            /* The whole spread acts like one page turning */
            transform-origin: left;
            transition: transform 0.8s;
            backface-visibility: hidden;
            
            /* Initially hidden, will be shown by JS */
            opacity: 0;
            pointer-events: none;
        }
        
        .spread.current-spread {
            opacity: 1;
            pointer-events: all;
            z-index: 20;
        }

        .spread-left, .spread-right {
            width: 50%;
            height: 100%;
            padding: 1rem;
        }
        
        .page-content {
            background-color: #262626; /* neutral-800 for the page itself */
            color: var(--text-light);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            padding: 1.5rem;
            height: 100%;
            overflow-y: auto;
            border: 1px solid #404040;
        }
        
        .spread-left .page-content {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
            border-right: none;
        }
        .spread-right .page-content {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
            border-left: 1px solid #525252;
        }

        /* Front and Back Cover Styling */
        .page-cover-front .page-content {
            background-color: var(--mid-dark);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
            border: 4px solid var(--primary-color);
        }
        .page-cover-back .page-content {
            background-color: var(--mid-dark);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
            border: 4px solid var(--primary-color);
            transform: rotateY(180deg); /* Flip back cover */
            backface-visibility: visible;
        }
        
        .menu-list-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #525252;
        }
        .menu-list-item:last-child {
            border-bottom: none;
        }
        
        /* Mobile adjustment: On small screens, keep it a single column */
        @media (max-width: 768px) {
            .menu-book {
                max-width: 400px;
                height: 500px;
            }
            .spread {
                flex-direction: column;
                /* Reset transform for single-page view on mobile, but keep the illusion */
                transform: none !important;
                backface-visibility: visible;
            }
            .spread-left, .spread-right {
                width: 100%;
                height: 50%; /* Each page takes half of the book height */
                padding: 0.5rem;
            }
            .spread-right {
                display: none; /* Only show one page at a time on small screen for readability */
            }
            .spread-left .page-content, .spread-right .page-content {
                border-radius: 5px;
                border: 1px solid #404040;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body class="bg-neutral-950 text-gray-200">

    <header class="main-header bg-neutral-900/50 backdrop-blur-md shadow-xl sticky top-0 z-50 transition-all duration-300 border-b border-orange-400/20">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="#" class="logo flex items-center space-x-2">
                <img src="uploads/flavoro.jpg" alt="Flavoro Logo" class="h-10 w-auto rounded-full shadow-lg border-2 border-orange-400">
                <span class="text-3xl font-extrabold lux-font text-orange-400 hidden sm:block tracking-wider">Flavoro</span>
            </a>
            
            <nav class="hidden lg:flex space-x-8 text-lg font-medium">
                <a href="#" class="text-gray-200 hover:text-orange-400 transition duration-300 relative group">
                    Home
                    <span class="absolute left-0 bottom-0 h-0.5 bg-orange-400 w-0 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#full-menu" class="text-gray-200 hover:text-orange-400 transition duration-300 relative group">
                    Menu
                    <span class="absolute left-0 bottom-0 h-0.5 bg-orange-400 w-0 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#featured-dishes" class="text-gray-200 hover:text-orange-400 transition duration-300 relative group">
                    Popular
                    <span class="absolute left-0 bottom-0 h-0.5 bg-orange-400 w-0 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#footer-contact" class="text-gray-200 hover:text-orange-400 transition duration-300 relative group">
                    Contact
                    <span class="absolute left-0 bottom-0 h-0.5 bg-orange-400 w-0 group-hover:w-full transition-all duration-300"></span>
                </a>
            </nav>

            <div class="nav-right flex items-center gap-4 md:gap-6">
                <div class="delivery-status font-semibold py-1 px-4 rounded-full text-sm shadow-inner 
                    <?php echo $delivery_status['class'] === 'open' ? 'bg-green-500/20 text-green-400 border border-green-500/50' : 'bg-red-500/20 text-red-400 border border-red-500/50'; ?>">
                    Delivery: *<?php echo $delivery_status['text']; ?>*
                </div>
                
                <div class="auth-buttons hidden sm:flex items-center gap-3">
                    <a href="login.php" class="login-btn text-orange-400 hover:text-neutral-900 bg-transparent border-2 border-orange-400 hover:bg-orange-400 transition-all duration-300 font-medium py-2 px-4 rounded-full text-sm">Login</a>
                    <a href="register.php" class="register-btn text-neutral-900 bg-orange-400 hover:bg-orange-500 transition-all duration-300 font-medium py-2 px-4 rounded-full text-sm shadow-lg shadow-orange-500/30">Register</a>
                </div>
                
                </div>
        </div>
    </header>

    <main>
        <section class="hero-section flex items-center justify-center text-center text-white pt-24 pb-16 md:pt-48 md:pb-32 lg:pt-64 lg:pb-40 relative rounded-b-[5rem] lg:rounded-b-[10rem] overflow-hidden shadow-2xl shadow-neutral-900">
            <div class="p-4 md:p-8 mx-auto max-w-5xl">
                <h1 class="text-5xl md:text-8xl font-extrabold mb-6 lux-font drop-shadow-2xl opacity-0 transform translate-y-10" 
                    data-aos="fade-up" data-aos-duration="1000" data-aos-easing="ease-out-cubic">
                    Taste the Tradition, Slice by Slice.
                </h1>
                <p class="text-xl md:text-2xl mb-12 font-light drop-shadow-xl max-w-3xl mx-auto text-gray-300 opacity-0 transform translate-y-10" 
                    data-aos="fade-up" data-aos-duration="1200" data-aos-delay="200" data-aos-easing="ease-out-cubic">
                    Your favorite meals, crafted with love and delivered in a flash.
                </p>
                <a href="#full-menu" class="cta-button-glowing bg-orange-500 hover:bg-orange-600 text-neutral-950 font-extrabold py-4 px-12 rounded-full shadow-2xl shadow-orange-500/50 transition-all duration-300 transform hover:scale-105 active:scale-95 text-lg inline-block opacity-0"
                    data-aos="fade-up" data-aos-duration="1500" data-aos-delay="400" data-aos-easing="ease-out-cubic">
                    View Menu Book <i class="fas fa-book-open ml-2 text-sm"></i>
                </a>
            </div>
            <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-orange-400 animate-bounce hidden sm:block">
                <p class="text-sm">Scroll Down</p>
                <i class="fas fa-angle-down text-xl"></i>
            </div>
        </section>
        
        <section id="highlights" class="py-20 bg-neutral-950">
            <div class="container mx-auto px-6">
                <h2 class="text-4xl md:text-5xl font-extrabold text-center text-gray-100 mb-16 lux-font" 
                    data-aos="fade-zoom-in" data-aos-duration="1000">
                    Why Choose Flavoro?
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                    
                    <div class="p-8 bg-neutral-900 rounded-2xl shadow-2xl border-t-4 border-orange-500 hover:shadow-orange-500/20 transition duration-500 transform hover:-translate-y-2 text-center"
                        data-aos="fade-up" data-aos-duration="800">
                        <div class="text-4xl text-orange-400 mb-4 inline-block p-4 bg-orange-400/10 rounded-full">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-100 mb-3 lux-font">Fresh Ingredients</h3>
                        <p class="text-gray-400">We source locally, ensuring every meal is prepared with the freshest produce.</p>
                    </div>

                    <div class="p-8 bg-neutral-900 rounded-2xl shadow-2xl border-t-4 border-orange-500 hover:shadow-orange-500/20 transition duration-500 transform hover:-translate-y-2 text-center"
                        data-aos="fade-up" data-aos-duration="800" data-aos-delay="150">
                        <div class="text-4xl text-orange-400 mb-4 inline-block p-4 bg-orange-400/10 rounded-full">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-100 mb-3 lux-font">Lightning Fast Delivery</h3>
                        <p class="text-gray-400">Our riders ensure your hot food reaches you in record time, safely and quickly.</p>
                    </div>

                    <div class="p-8 bg-neutral-900 rounded-2xl shadow-2xl border-t-4 border-orange-500 hover:shadow-orange-500/20 transition duration-500 transform hover:-translate-y-2 text-center"
                        data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                        <div class="text-4xl text-orange-400 mb-4 inline-block p-4 bg-orange-400/10 rounded-full">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-100 mb-3 lux-font">Affordable Prices</h3>
                        <p class="text-gray-400">Great taste shouldn't break the bank. Enjoy premium food at local prices.</p>
                    </div>

                    <div class="p-8 bg-neutral-900 rounded-2xl shadow-2xl border-t-4 border-orange-500 hover:shadow-orange-500/20 transition duration-500 transform hover:-translate-y-2 text-center"
                        data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
                        <div class="text-4xl text-orange-400 mb-4 inline-block p-4 bg-orange-400/10 rounded-full">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-100 mb-3 lux-font">Authentic Taste</h3>
                        <p class="text-gray-400">Traditional recipes passed down, giving you a truly authentic culinary experience.</p>
                    </div>

                </div>
            </div>
        </section>

        <section id="featured-dishes" class="py-20 bg-neutral-900">
            <div class="container mx-auto px-6">
                <h2 class="text-4xl md:text-5xl font-extrabold text-center text-gray-100 mb-16 lux-font" 
                    data-aos="fade-up" data-aos-duration="1000">
                    Our Customer Favorites 🔥
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php 
                    // Use the featured dishes array
                    foreach ($featured_dishes as $item): ?>
                        <div class="group main-menu-card bg-neutral-950 rounded-2xl overflow-hidden shadow-2xl border border-neutral-700 hover:border-orange-500 transition duration-500"
                            data-aos="fade-up" data-aos-duration="800" data-aos-delay="<?php echo $item['id'] * 100; // Staggered animation ?>">
                            
                            <div class="overflow-hidden">
                                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>

                            <div class="p-5 text-center">
                                <h3 class="text-xl font-semibold text-gray-100 mb-2 lux-font"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-sm font-medium text-gray-400 mb-3"><?php echo htmlspecialchars($item['category']); ?></p>
                                <p class="price text-3xl font-bold text-orange-500 mb-4">Rs. <?php echo htmlspecialchars($item['price']); ?></p>
                                
                                <a href="login.php" class="inline-block bg-orange-500 hover:bg-orange-600 text-neutral-950 font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-xl shadow-orange-500/30">
                                    Order Now
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-16" data-aos="fade-up" data-aos-duration="1000">
                    <a href="#full-menu" class="text-xl font-semibold text-orange-400 border border-orange-400 hover:bg-orange-400 hover:text-neutral-950 py-3 px-10 rounded-full transition-all duration-300">
                        Open Menu Book <i class="fas fa-book-open ml-2"></i>
                    </a>
                </div>
            </div>
        </section>
        
        <section id="full-menu" class="py-20 bg-neutral-950 border-t border-neutral-800">
            <div class="container mx-auto px-6">
                <h2 class="text-4xl md:text-5xl font-extrabold text-center text-gray-100 mb-10 lux-font"
                    data-aos="fade-up" data-aos-duration="1000">
                    Our Digital Menu Book
                </h2>
                
                <div class="max-w-xl mx-auto mb-10" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="relative">
                        <input type="text" id="book-menu-search" class="w-full py-4 px-6 pl-14 border-2 border-orange-400 rounded-full text-lg bg-neutral-800 text-gray-200 placeholder-neutral-500 focus:ring-1 focus:ring-orange-400 focus:border-orange-400 transition shadow-lg shadow-neutral-900/50" placeholder="Quickly search by food name or category...">
                        <i class="fas fa-magnifying-glass absolute left-5 top-1/2 transform -translate-y-1/2 text-orange-400 text-xl"></i>
                    </div>
                </div>

                <div class="flex flex-col items-center">
                    
                    <div class="menu-book-container w-full" data-aos="flip-up" data-aos-duration="1500" data-aos-delay="200">
                        <div class="menu-book" id="menu-book">
                            
                            <?php 
                            $spreadIndex = 0;
                            // Loop through the pages array, two at a time
                            for ($i = 0; $i < $totalPages; $i += 2): 
                                $leftPage = $book_pages[$i] ?? null;
                                $rightPage = $book_pages[$i+1] ?? null;

                                // The first spread (index 0) is the Cover.
                                $isCoverSpread = $spreadIndex === 0;
                                // The last visible spread is when the total number of pages is reached
                                $isLastSpread = $i >= $totalPages - 2; 

                                // Determine visibility class
                                $spreadClass = $isCoverSpread ? 'current-spread' : '';
                            ?>
                                <div class="spread absolute" id="spread-<?php echo $spreadIndex; ?>" data-spread-index="<?php echo $spreadIndex; ?>" data-aos="page-turn">
                                    <div class="spread-left" id="left-page-<?php echo $i; ?>">
                                        <?php if ($leftPage): ?>
                                            <div class="page-content scrollable-content 
                                                <?php echo $isCoverSpread ? 'page-cover-front' : ''; /* Front cover styling is only for the first page */ ?>">
                                                
                                                <?php if ($leftPage['type'] === 'cover'): ?>
                                                    <div class="text-center py-16">
                                                        <span class="text-7xl text-orange-400 mb-4"><i class="fas fa-book-open"></i></span>
                                                        <h1 class="text-5xl lux-font font-bold text-gray-100 mb-2">Flavoro</h1>
                                                        <p class="text-2xl lux-font text-orange-400">The Culinary Journey</p>
                                                        <p class="mt-8 text-gray-400">Tap the arrow to open</p>
                                                    </div>
                                                <?php elseif ($leftPage['type'] === 'index'): ?>
                                                    <div class="py-4">
                                                        <h4 class="text-3xl font-extrabold lux-font text-gray-100 mb-6 border-b border-orange-400/50 pb-2">Welcome</h4>
                                                        <p class="text-gray-300 text-lg mb-8"><?php echo htmlspecialchars($leftPage['content']); ?></p>
                                                        <h5 class="text-xl font-bold text-orange-400 mb-3">Sections:</h5>
                                                        <ul class="space-y-2 text-gray-400">
                                                            <?php foreach(array_keys($categories) as $cat): ?>
                                                                <li><i class="fas fa-caret-right text-orange-400 mr-2"></i><?php echo htmlspecialchars($cat); ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        <p class="mt-8 text-sm text-gray-500">Page 1</p>
                                                    </div>
                                                <?php elseif ($leftPage['type'] === 'category_header'): ?>
                                                    <div class="text-center py-8">
                                                        <h4 class="text-4xl font-extrabold lux-font text-orange-400 mb-4"><?php echo htmlspecialchars($leftPage['name']); ?></h4>
                                                        <p class="text-lg text-gray-300">Authentic Flavors, Freshly Prepared.</p>
                                                        <img src="uploads/<?php echo htmlspecialchars($leftPage['image']); ?>" alt="Category Image" class="w-full h-48 object-cover rounded-xl mt-6 shadow-lg border-2 border-orange-400/50">
                                                        <p class="mt-8 text-sm text-gray-500">Page <?php echo $i; ?></p>
                                                    </div>
                                                <?php elseif ($leftPage['type'] === 'menu_page'): ?>
                                                    <h4 class="text-2xl font-bold border-b pb-2 mb-4 text-orange-400/80 lux-font"><?php echo htmlspecialchars($leftPage['category']); ?></h4>
                                                    <ul class="space-y-3 menu-item-list" data-category="<?php echo strtolower(htmlspecialchars($leftPage['category'])); ?>">
                                                        <?php foreach ($leftPage['items'] as $item): ?>
                                                            <li class="menu-list-item" data-name="<?php echo strtolower(htmlspecialchars($item['name'])); ?>">
                                                                <span class="text-gray-100 font-medium flex-1 mr-4"><?php echo htmlspecialchars($item['name']); ?></span>
                                                                <span class="text-xl font-bold text-orange-500 flex-shrink-0">Rs. <?php echo htmlspecialchars($item['price']); ?></span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <div class="text-center text-sm text-gray-500 mt-4">Page <?php echo $i; ?></div>
                                                <?php elseif ($leftPage['type'] === 'footer'): ?>
                                                    <div class="py-16 text-center">
                                                        <h4 class="text-4xl font-extrabold lux-font text-gray-100 mb-4">Thank You!</h4>
                                                        <p class="text-lg text-orange-400 mb-8">Ready to order? Contact us below.</p>
                                                        <a href="#footer-contact" class="inline-block bg-orange-500 hover:bg-orange-600 text-neutral-950 font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-xl shadow-orange-500/30">
                                                            Contact Info
                                                        </a>
                                                        <p class="mt-8 text-sm text-gray-500">Page <?php echo $i; ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="spread-right" id="right-page-<?php echo $i+1; ?>">
                                        <?php if ($rightPage): ?>
                                            <div class="page-content scrollable-content">
                                                
                                                <?php if ($rightPage['type'] === 'category_header'): ?>
                                                    <div class="text-center py-8">
                                                        <h4 class="text-4xl font-extrabold lux-font text-orange-400 mb-4"><?php echo htmlspecialchars($rightPage['name']); ?></h4>
                                                        <p class="text-lg text-gray-300">Authentic Flavors, Freshly Prepared.</p>
                                                        <img src="uploads/<?php echo htmlspecialchars($rightPage['image']); ?>" alt="Category Image" class="w-full h-48 object-cover rounded-xl mt-6 shadow-lg border-2 border-orange-400/50">
                                                        <p class="mt-8 text-sm text-gray-500">Page <?php echo $i+1; ?></p>
                                                    </div>
                                                <?php elseif ($rightPage['type'] === 'menu_page'): ?>
                                                    <h4 class="text-2xl font-bold border-b pb-2 mb-4 text-orange-400/80 lux-font"><?php echo htmlspecialchars($rightPage['category']); ?></h4>
                                                    <ul class="space-y-3 menu-item-list" data-category="<?php echo strtolower(htmlspecialchars($rightPage['category'])); ?>">
                                                        <?php foreach ($rightPage['items'] as $item): ?>
                                                            <li class="menu-list-item" data-name="<?php echo strtolower(htmlspecialchars($item['name'])); ?>">
                                                                <span class="text-gray-100 font-medium flex-1 mr-4"><?php echo htmlspecialchars($item['name']); ?></span>
                                                                <span class="text-xl font-bold text-orange-500 flex-shrink-0">Rs. <?php echo htmlspecialchars($item['price']); ?></span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <div class="text-center text-sm text-gray-500 mt-4">Page <?php echo $i+1; ?></div>
                                                <?php elseif ($rightPage['type'] === 'blank'): ?>
                                                     <div class="py-16 text-center text-gray-600 italic">
                                                         --- Next spread is the end ---
                                                         <p class="mt-8 text-sm text-gray-500">Page <?php echo $i+1; ?></p>
                                                     </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif ($i + 1 == $totalPages): /* Back Cover */ ?>
                                            <div class="page-content page-cover-back flex flex-col justify-center items-center text-center p-8">
                                                <span class="text-7xl text-orange-600 mb-4"><i class="fas fa-heart"></i></span>
                                                <h1 class="text-4xl lux-font font-bold text-gray-100 mb-2">See You Soon!</h1>
                                                <p class="text-xl lux-font text-orange-400">Order Online Now</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php 
                            $spreadIndex++;
                            endfor; ?>

                        </div>
                    </div>
                    
                    <div class="menu-book-nav flex justify-center space-x-8 mt-6">
                        <button id="prev-spread" class="bg-neutral-800 text-orange-400 hover:bg-orange-500 hover:text-neutral-950 p-4 rounded-full transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-xl shadow-neutral-800/50" disabled>
                            <i class="fas fa-arrow-left text-xl"></i> Prev Spread
                        </button>
                        <button id="next-spread" class="bg-neutral-800 text-orange-400 hover:bg-orange-500 hover:text-neutral-950 p-4 rounded-full transition-all duration-300 shadow-xl shadow-neutral-800/50">
                            Next Spread <i class="fas fa-arrow-right text-xl"></i>
                        </button>
                    </div>
                    
                    <p class="text-gray-500 mt-4" id="spread-indicator">Spread 1 of <?php echo $spreadIndex; ?></p>

                    <div class="text-center text-lg mt-12">
                        <a href="login.php" class="inline-block bg-orange-500 hover:bg-orange-600 text-neutral-950 font-extrabold py-3 px-10 rounded-full transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-2xl shadow-orange-500/50">
                            Start Your Order Now <i class="fas fa-cart-shopping ml-2"></i>
                        </a>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <footer class="main-footer bg-neutral-950 border-t border-neutral-800" id="footer-contact">
        <div class="container mx-auto px-6 pt-16 pb-8">
            <div class="footer-content border-b border-neutral-800 pb-12 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-10 text-center md:text-left">
                
                <div class="footer-section about lg:col-span-1" data-aos="fade-up" data-aos-duration="1000">
                    <h4 class="text-2xl font-extrabold mb-4 lux-font text-orange-400">Flavoro</h4>
                    <p class="text-gray-400 text-sm mb-4">Flavoro brings the taste of authentic cuisine to your home. We believe in fresh ingredients, great taste, and fast delivery.</p>
                    <div class="social-icons mt-4 flex justify-center md:justify-start space-x-4">
                        <a href="#" class="text-2xl text-gray-300 hover:text-orange-400 transition-colors duration-300 transform hover:scale-110"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-2xl text-gray-300 hover:text-orange-400 transition-colors duration-300 transform hover:scale-110"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-2xl text-gray-300 hover:text-orange-400 transition-colors duration-300 transform hover:scale-110"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-section links hidden lg:block" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <h4 class="text-lg font-semibold mb-4 text-orange-400">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-400 hover:text-orange-400 transition">Home</a></li>
                        <li><a href="#full-menu" class="text-gray-400 hover:text-orange-400 transition">Menu</a></li>
                        <li><a href="#featured-dishes" class="text-gray-400 hover:text-orange-400 transition">Popular Dishes</a></li>
                        <li><a href="login.php" class="text-gray-400 hover:text-orange-400 transition">My Account</a></li>
                    </ul>
                </div>

                <div class="footer-section contact" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <h4 class="text-lg font-semibold mb-4 text-orange-400">Get In Touch</h4>
                    <div class="space-y-3 text-sm">
                        <p class="text-gray-400 flex items-center justify-center md:justify-start">
                            <i class="fas fa-phone mr-3 text-orange-400"></i> +977 9800000000
                        </p>
                        <p class="text-gray-400 flex items-center justify-center md:justify-start">
                            <i class="fas fa-envelope mr-3 text-orange-400"></i> contact@flavoro.com
                        </p>
                        <p class="text-gray-400 flex items-center justify-center md:justify-start">
                            <i class="fas fa-map-marker-alt mr-3 text-orange-400"></i> Pathari Shanishchare, Koshi, Nepal
                        </p>
                    </div>
                </div>

                <div class="footer-section map lg:col-span-1" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                    <h4 class="text-lg font-semibold mb-4 text-orange-400">Our Location</h4>
                    <div class="map-container rounded-xl overflow-hidden shadow-2xl shadow-neutral-900/50 h-48 border border-neutral-700">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d57073.49755486016!2d87.6033502570085!3d26.63469145946401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ef6e363656c12d%3A0x67396615b3991264!2sPathari%20Shanishchare!5e0!3m2!1sen!2snp!4v1725595304123!5m2!1sen!2snp" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full border-0"></iframe>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom mt-8 pt-4 text-sm text-gray-600 text-center">
                © <?php echo date('Y'); ?> Flavoro | All Rights Reserved. Designed with ❤ in Nepal.
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 50,
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Hide/Show Header on Scroll for a cleaner look
            let lastScrollTop = 0;
            const header = document.querySelector('.main-header');
            const headerHeight = header.offsetHeight;

            window.addEventListener('scroll', function() {
                let st = window.pageYOffset || document.documentElement.scrollTop;

                if (st > lastScrollTop && st > headerHeight) {
                    header.style.top = `-${headerHeight}px`;
                } else {
                    header.style.top = '0';
                }
                lastScrollTop = st <= 0 ? 0 : st;
            }, false);

            // --- Menu Book Logic (Updated for Spread View) ---
            const book = document.getElementById('menu-book');
            const spreads = document.querySelectorAll('.spread');
            const totalSpreads = spreads.length;
            const prevBtn = document.getElementById('prev-spread');
            const nextBtn = document.getElementById('next-spread');
            const spreadIndicator = document.getElementById('spread-indicator');
            const searchInput = document.getElementById('book-menu-search');
            const allMenuItems = document.querySelectorAll('.menu-list-item');
            const menuLists = document.querySelectorAll('.menu-item-list');

            let currentSpreadIndex = 0; // Starts at 0 (the cover)
            
            function updateBook() {
                spreads.forEach((spread, index) => {
                    const pageIndex = index * 2; // Left page index
                    
                    // 1. Z-Index and Flip Effect
                    if (index < currentSpreadIndex) {
                        // Spreads we've passed (flipped to the left side)
                        spread.style.transform = 'rotateY(-180deg)';
                        spread.style.zIndex = totalSpreads + 10 - index;
                    } else if (index === currentSpreadIndex) {
                        // Current open spread
                        spread.style.transform = 'rotateY(0deg)';
                        spread.style.zIndex = totalSpreads + 20; // Highest Z-Index
                    } else {
                        // Spreads that are yet to be opened (stacked on the right)
                        spread.style.transform = 'rotateY(0deg)';
                        spread.style.zIndex = totalSpreads - index;
                    }
                    
                    // 2. Visibility and Current Class (for Mobile)
                    if (index === currentSpreadIndex) {
                        spread.classList.add('current-spread');
                    } else {
                         spread.classList.remove('current-spread');
                    }
                });

                // 3. Update Buttons and Indicator
                prevBtn.disabled = currentSpreadIndex === 0;
                nextBtn.disabled = currentSpreadIndex === totalSpreads - 1;
                spreadIndicator.textContent = `Spread ${currentSpreadIndex + 1} of ${totalSpreads}`;
            }

            function goToSpread(index) {
                if (index >= 0 && index < totalSpreads) {
                    currentSpreadIndex = index;
                    updateBook();
                    // Clear search when changing spreads, or you can maintain the filter
                    // searchInput.value = ''; 
                    // filterMenu(''); 
                }
            }

            prevBtn.addEventListener('click', () => {
                goToSpread(currentSpreadIndex - 1);
            });

            nextBtn.addEventListener('click', () => {
                goToSpread(currentSpreadIndex + 1);
            });

            // --- Search Functionality ---
            function filterMenu(searchTerm) {
                const lowerCaseSearch = searchTerm.toLowerCase();
                let foundItem = false;
                
                // Hide all items initially
                allMenuItems.forEach(item => {
                    item.style.display = 'none';
                });
                
                // Show only matching items
                allMenuItems.forEach(item => {
                    const itemName = item.dataset.name;
                    if (itemName.includes(lowerCaseSearch)) {
                        item.style.display = 'flex';
                        foundItem = true;
                    }
                });
                
                // Show/Hide Category Headers based on content
                menuLists.forEach(list => {
                    const itemsInList = list.querySelectorAll('.menu-list-item');
                    let visibleItems = 0;
                    itemsInList.forEach(item => {
                        if (item.style.display === 'flex') {
                            visibleItems++;
                        }
                    });
                    
                    const categoryHeader = list.closest('.page-content').querySelector('h4');
                    if (categoryHeader && list.dataset.category) { // Only target menu page headers
                         if (visibleItems > 0) {
                            categoryHeader.style.display = 'block';
                        } else {
                            // If search is empty, show all headers. If not, hide empty ones.
                            categoryHeader.style.display = lowerCaseSearch === '' ? 'block' : 'none';
                        }
                    }
                });

                // Logic for spreads: If a search term is active, show all menu pages regardless of spread.
                if (lowerCaseSearch !== '') {
                    // For active search, flatten the view to make all menu items searchable and visible
                    spreads.forEach((spread, index) => {
                        // Reset all to visible or standard state to show filtered list
                        if (index > 0 && index < totalSpreads - 1) { // Only menu spreads
                            spread.style.opacity = 1;
                            spread.style.transform = 'none'; // Un-flip all
                            spread.style.zIndex = 50;
                            spread.classList.add('current-spread');
                            prevBtn.style.display = 'none';
                            nextBtn.style.display = 'none';
                            spreadIndicator.textContent = 'Filtered View';
                        }
                    });
                    book.style.maxWidth = 'none'; // Allow the book to take full width for the filtered list
                    book.style.transform = 'none';
                } else {
                    // Reset to book view
                    book.style.maxWidth = '900px';
                    prevBtn.style.display = 'inline-flex';
                    nextBtn.style.display = 'inline-flex';
                    updateBook();
                }
            }

            searchInput.addEventListener('input', function(e) {
                filterMenu(e.target.value);
            });
            
            updateBook(); // Initial setup
        });
    </script>
</body>
</html>