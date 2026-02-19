<?php
// ============================================================
// KANOJA CUSTOMER APP - Complete Version with Fixed Orders
// ============================================================

$firebaseConfig = [
    "apiKey" => "AIzaSyDXh7dcRFMsVQUWM7fLNZ5V4Z-T9T7ZWcc",
    "authDomain" => "kanoja.firebaseapp.com",
    "databaseURL" => "https://kanoja-default-rtdb.firebaseio.com",
    "projectId" => "kanoja",
    "storageBucket" => "kanoja.firebasestorage.app",
    "messagingSenderId" => "519176946748",
    "appId" => "1:519176946748:web:4c334f1316ce05c41d6f39"
];

$smtp_host = 'smtp.gmail.com';
$smtp_port = 465;
$smtp_username = 'kanojaoffical@gmail.com';
$smtp_password = 'fomfuhqryedkemfv';

$razorpay_key_id = 'rzp_test_YOUR_KEY_ID';
$razorpay_key_secret = 'YOUR_KEY_SECRET';

$minimum_order_amount = 250;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['email']) && isset($input['otp'])) {
        $to = $input['email'];
        $otp = $input['otp'];

        $subject = "Your Kanoja Verification Code";
        $message_body = "Your OTP Code is: " . $otp . "\n\nThank you for using Kanoja!";
        $headers = "From: Kanoja <" . $smtp_username . ">\r\n";
        $headers .= "Reply-To: " . $smtp_username . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $mail_sent = mail($to, $subject, $message_body, $headers);
        
        if ($mail_sent) {
            echo json_encode(["status" => "success", "message" => "OTP sent successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Mail failed", "debug_otp" => $otp]);
        }
    } elseif (isset($input['action']) && $input['action'] === 'create_razorpay_order') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'amount' => $input['amount'],
            'currency' => 'INR',
            'receipt' => 'order_' . time(),
            'payment_capture' => 1
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($razorpay_key_id . ':' . $razorpay_key_secret)
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        
        echo $response;
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid input"]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <title>Kanoja - Food Delivery App | Order Food Online</title>
    <meta name="description" content="Kanoja delivers fresh food, cold beverages, tasty snacks to your doorstep. Fast delivery, best prices. Order now!">
    <meta name="keywords" content="Kanoja, food delivery, order food online, beverage delivery, snack delivery, fast delivery">
    <meta name="author" content="Kanoja">
    <meta name="robots" content="index, follow">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="Kanoja - Food Delivery App">
    <meta property="og:description" content="Order food, beverages, and snacks online. Fast doorstep delivery.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <style>
        :root {
            --primary-color: #ff6b35;
            --primary-gradient: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            --secondary-color: #f8f9fa;
            --text-color: #2d3436;
            --light-text: #636e72;
            --border-color: #dfe6e9;
            --white: #ffffff;
            --danger: #d63031;
            --success: #00b894;
            --food-color: #ff6b6b;
            --beverage-color: #4ecdc4;
            --snack-color: #ffe66d;
            --tobacco-color: #95a5a6;
            --warning: #ffc107;
            --warning-bg: #fff3cd;
            --warning-text: #856404;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6fa;
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .mobile-container {
            width: 100%;
            max-width: 480px;
            height: 100vh;
            max-height: 900px;
            background-color: var(--secondary-color);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 481px) {
            .mobile-container {
                height: 90vh;
                border-radius: 30px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            }
        }

        .page {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            background-color: var(--secondary-color);
            display: flex;
            flex-direction: column;
            z-index: 1;
            overflow-y: auto;
        }

        .page.active { opacity: 1; visibility: visible; z-index: 5; }

        .page-history {
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            z-index: 1000;
            display: none;
        }

        /* Search Suggestions */
        .search-container {
            position: relative;
            width: 100%;
        }

        .search-bar {
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: none;
            border-radius: 30px;
            font-size: 1em;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            outline: none;
        }

        .search-bar .fa-search {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
            z-index: 2;
        }

        .suggestions-container {
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .suggestions-container.show { display: block; }

        .suggestion-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.3s;
            border-bottom: 1px solid var(--border-color);
        }

        .suggestion-item:hover { background: #f8f9fa; }

        .suggestion-item img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 12px;
        }

        .suggestion-info { flex: 1; }
        .suggestion-info h4 { margin: 0; font-size: 0.95em; color: var(--text-color); }
        .suggestion-info p { margin: 5px 0 0; font-size: 0.85em; color: var(--light-text); }
        .suggestion-price { font-weight: 600; color: var(--primary-color); }
        .no-results { padding: 20px; text-align: center; color: var(--light-text); }

        /* Profile Dropdown */
        .profile-container { position: relative; }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 30px;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s;
        }

        .profile-trigger:hover { background: rgba(255,255,255,0.3); }
        .profile-trigger i { font-size: 1.2em; }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9em;
        }

        .profile-name {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 500;
        }

        .dropdown-menu {
            position: absolute;
            top: 50px;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            z-index: 1000;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 15px 15px 0 0;
        }

        .dropdown-header h4 { margin: 0; font-size: 1em; color: var(--text-color); }
        .dropdown-header p { margin: 5px 0 0; font-size: 0.8em; color: var(--light-text); }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .dropdown-item:hover { background: #f8f9fa; }
        .dropdown-item i { width: 20px; color: var(--primary-color); font-size: 1.1em; }
        .dropdown-item.logout { border-top: 1px solid var(--border-color); color: var(--danger); }
        .dropdown-item.logout i { color: var(--danger); }

        /* Auth Modal */
        .auth-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .auth-modal.active { display: flex; }

        .auth-card {
            background: white;
            width: 100%;
            max-width: 360px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            text-align: center;
            padding: 40px 30px 20px;
            background: var(--primary-gradient);
            color: white;
        }

        .logo-circle {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5em;
            backdrop-filter: blur(10px);
        }

        .auth-header h1 { margin: 0; font-size: 1.8em; font-weight: 700; }
        .auth-header p { margin: 10px 0 0; opacity: 0.9; font-size: 0.95em; }

        .auth-body { padding: 30px; }

        .input-group { position: relative; margin-bottom: 20px; }
        
        .input-label {
            font-size: 0.75em;
            color: var(--light-text);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .auth-input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1em;
            outline: none;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .auth-input:focus {
            background: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
        }

        .input-group i {
            position: absolute;
            bottom: 15px;
            left: 15px;
            color: #b2bec3;
            font-size: 1.1em;
        }

        .btn-main {
            width: 100%;
            padding: 16px;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
            margin-bottom: 15px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-main:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(255, 107, 53, 0.4); }
        .btn-main:active { transform: scale(0.98); }
        .btn-main:disabled { background: #bdc3c7; cursor: not-allowed; box-shadow: none; opacity: 0.5; }

        .btn-google {
            background: white;
            color: #333;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .btn-google:hover { border-color: var(--primary-color); background: #fff; }

        .auth-switch {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: var(--light-text);
        }

        .auth-switch a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        #auth-error {
            color: var(--danger);
            text-align: center;
            margin-bottom: 15px;
            font-size: 0.9em;
            background: rgba(214, 48, 49, 0.1);
            padding: 12px;
            border-radius: 8px;
            display: none;
        }

        .close-auth {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .close-auth:hover { background: rgba(255,255,255,0.3); }

        /* App Header */
        .app-header {
            background: var(--primary-gradient);
            padding: 50px 20px 20px;
            color: white;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.2);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-top h1 {
            margin: 0;
            font-size: 1.6em;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Categories */
        .category-section {
            display: flex;
            justify-content: space-around;
            padding: 25px 10px;
            gap: 10px;
        }

        .category-item {
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }

        .category-item:active { transform: scale(0.9); }

        .category-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px;
            font-size: 1.6em;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .category-item:hover .category-icon { transform: translateY(-5px); }

        .category-food .category-icon { background: #fff5f5; color: var(--food-color); }
        .category-beverage .category-icon { background: #e8f8f5; color: var(--beverage-color); }
        .category-snack .category-icon { background: #fffbeb; color: #f59e0b; }
        .category-tobacco .category-icon { background: #f3f4f6; color: #6b7280; }

        .category-item p {
            margin: 0;
            font-size: 0.8em;
            font-weight: 500;
            color: var(--light-text);
        }

        /* Main Content */
        main {
            flex: 1;
            overflow-y: auto;
            padding: 0 20px 100px;
        }

        .section-title {
            font-size: 1.2em;
            font-weight: 700;
            margin: 25px 0 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .horizontal-scroll {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding-bottom: 20px;
            scroll-behavior: smooth;
        }

        .horizontal-scroll::-webkit-scrollbar { display: none; }

        /* Promo Banner - Dynamic from Admin */
        .promo-banner {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
            border-radius: 20px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .promo-banner:hover { transform: scale(1.02); }

        .promo-content h3 { 
            margin: 0; 
            font-size: 1.8em; 
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .promo-content p { 
            margin: 5px 0 0; 
            opacity: 0.95; 
            font-size: 1.1em;
            font-weight: 500;
        }
        .promo-min-order {
            margin-top: 10px;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 0.85em;
            display: inline-block;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .promo-banner i {
            font-size: 12em;
            position: absolute;
            right: -20px;
            bottom: -30px;
            opacity: 0.2;
            transform: rotate(10deg);
        }

        /* Product Cards */
        .card {
            flex-shrink: 0;
            width: 160px;
            background: white;
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            overflow: hidden;
            position: relative;
            transition: transform 0.3s;
        }

        .card:hover { transform: translateY(-5px); }

        .card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .card-content { padding: 12px; }
        .card-content h3 {
            margin: 0 0 5px;
            font-size: 0.95em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
        }

        .card-content p {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
            font-size: 1em;
        }

        .add-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 35px;
            height: 35px;
            background: var(--primary-gradient);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(255, 107, 53, 0.4);
            transition: transform 0.2s;
        }

        .add-btn:hover { transform: scale(1.1); }

        /* Floating Cart */
        #cart-fab-container {
            position: fixed;
            bottom: 90px;
            right: 20px;
            z-index: 100;
            display: none;
        }

        #cart-fab-container.visible { display: block; }

        .fab-btn {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.4em;
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
            cursor: pointer;
            position: relative;
            transition: transform 0.2s;
            border: none;
        }

        .fab-btn:hover { transform: scale(1.1); }
        .fab-btn:active { transform: scale(0.95); }

        #cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 0.8em;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            border: 3px solid white;
        }

        /* Navigation */
        nav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: var(--primary-gradient);
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -5px 20px rgba(255, 107, 53, 0.3);
            border-radius: 30px 30px 0 0;
            z-index: 10;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            transition: all 0.3s;
            padding: 10px 20px;
            border-radius: 15px;
            text-decoration: none;
        }

        .nav-item.active {
            color: white;
            background: rgba(255,255,255,0.2);
            transform: translateY(-5px);
        }

        .nav-item i { font-size: 1.3em; margin-bottom: 3px; }
        .nav-item p { margin: 0; font-size: 0.7em; font-weight: 500; }

        /* Cart Page */
        .page-header {
            padding: 50px 20px 20px;
            text-align: center;
            position: relative;
            background: var(--secondary-color);
        }

        .back-btn {
            position: absolute;
            left: 20px;
            top: 55px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: none;
            cursor: pointer;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .cart-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .cart-item img {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            margin-right: 15px;
        }

        .cart-item-details { flex: 1; }
        .cart-item-details h4 { margin: 0 0 5px; font-size: 1em; }
        .cart-item-details p { color: var(--primary-color); font-weight: 700; margin: 0; }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8f9fa;
            padding: 5px 12px;
            border-radius: 25px;
        }

        .qty-controls button {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: white;
            cursor: pointer;
            font-weight: 700;
            color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .cart-summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid var(--border-color);
        }

        /* Minimum Order Warning */
        #minimumOrderWarning {
            background: var(--warning-bg);
            color: var(--warning-text);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95em;
            border-left: 4px solid var(--warning);
            box-shadow: 0 4px 10px rgba(255, 193, 7, 0.2);
        }

        #minimumOrderWarning i {
            font-size: 1.3em;
            color: var(--warning);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.95em;
        }

        .summary-row.total {
            font-size: 1.3em;
            font-weight: 700;
            color: var(--text-color);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        /* Checkout Page */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
            font-size: 0.9em;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-control:focus { border-color: var(--primary-color); }

        .location-btn {
            width: 100%;
            padding: 15px;
            background: #0984e3;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1em;
            font-weight: 500;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .location-btn:hover { background: #0770c2; transform: translateY(-2px); }

        #map-container {
            height: 200px;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            margin: 15px 0;
            border: 3px solid var(--primary-color);
            display: none;
        }

        .or-divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #b2bec3;
            margin: 25px 0;
            font-weight: 600;
            font-size: 0.8em;
        }

        .or-divider::before, .or-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dfe6e9;
        }

        .or-divider::before { margin-right: 15px; }
        .or-divider::after { margin-left: 15px; }

        .otp-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .verify-btn {
            background: #2d3436;
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 10px;
            cursor: pointer;
            white-space: nowrap;
            font-weight: 500;
            transition: all 0.3s;
        }

        .verify-btn:hover { background: var(--primary-color); }

        .verified-badge {
            color: var(--success);
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            background: rgba(0, 184, 148, 0.1);
            padding: 10px 15px;
            border-radius: 8px;
            width: fit-content;
        }

        /* Payment Methods */
        .payment-methods {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .payment-method {
            flex: 1;
            padding: 15px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background: rgba(255, 107, 53, 0.1);
        }

        .payment-method i {
            font-size: 1.5em;
            margin-bottom: 8px;
            color: var(--primary-color);
        }

        .payment-method p {
            margin: 0;
            font-weight: 600;
            font-size: 0.9em;
        }

        .payment-method small {
            color: var(--light-text);
            font-size: 0.8em;
        }

        .payment-loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .payment-loading.show { display: block; }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border-color);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Orders Page */
        .order-filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .order-filter {
            padding: 8px 16px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 0.9em;
            cursor: pointer;
            white-space: nowrap;
        }

        .order-filter.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .order-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-id {
            font-family: monospace;
            font-weight: 600;
            color: var(--primary-color);
        }

        .order-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-placed { background: #fff3cd; color: #856404; }
        .status-preparing { background: #d1ecf1; color: #0c5460; }
        .status-outfordelivery { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .order-items-preview {
            color: var(--light-text);
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
            font-size: 0.9em;
        }

        .order-total {
            font-weight: 700;
            color: var(--primary-color);
        }

        .order-date {
            color: var(--light-text);
        }

        .track-bar {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-top: 25px;
        }

        .track-bar::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 10%;
            right: 10%;
            height: 4px;
            background: #f1f2f6;
            z-index: 1;
        }

        .track-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 25%;
        }

        .dot {
            width: 18px;
            height: 18px;
            background: #dfe6e9;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .track-step p {
            font-size: 0.65em;
            margin-top: 8px;
            color: #b2bec3;
            font-weight: 600;
            text-transform: uppercase;
        }

        .track-step.active .dot {
            background: var(--primary-color);
            transform: scale(1.3);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.2);
        }

        .track-step.active p { color: var(--primary-color); }
        .track-step.passed .dot { background: var(--primary-color); }
        .track-step.passed p { color: var(--primary-color); }

        /* Order Detail Modal */
        .order-detail-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            z-index: 2000;
            display: none;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .order-detail-modal.active { display: flex; }

        .order-detail-card {
            background: white;
            width: 100%;
            max-width: 400px;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 20px;
            padding: 20px;
            animation: slideUp 0.3s ease;
        }

        .order-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .close-detail {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            color: var(--light-text);
        }

        .order-items-list { margin: 20px 0; }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .order-item-name { font-weight: 500; }
        .order-item-price { color: var(--primary-color); }

        /* Notifications */
        .notification-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .notification-card.unread {
            background: #fff5f0;
            border-left: 4px solid var(--primary-color);
        }

        .notif-icon {
            width: 45px;
            height: 45px;
            background: #fff5f0;
            color: var(--primary-color);
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2em;
            flex-shrink: 0;
        }

        .notif-content h4 { margin: 0 0 5px; font-size: 0.95em; }
        .notif-content p {
            margin: 0;
            font-size: 0.85em;
            color: var(--light-text);
            line-height: 1.4;
        }

        .notif-time {
            font-size: 0.75em;
            color: #b2bec3;
            margin-top: 5px;
            display: block;
        }

        /* Thank You Page */
        .success-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 40px;
            text-align: center;
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            animation: scaleIn 0.5s ease;
            box-shadow: 0 20px 40px rgba(0, 184, 148, 0.3);
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-icon i { font-size: 4em; color: white; }
        .success-container h2 { color: #2d3436; margin: 0 0 10px; font-size: 1.8em; }
        .success-container p { color: var(--light-text); margin: 0 0 30px; font-size: 1.1em; }

        .track-btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 30px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .track-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(255, 107, 53, 0.4); }

        .home-link {
            color: var(--light-text);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            cursor: pointer;
        }

        .home-link:hover { color: var(--primary-color); }

        /* Age Verification */
        .age-verify {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .age-verify.active { display: flex; }

        .age-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 350px;
            animation: slideUp 0.3s ease;
        }

        .age-card h3 { margin: 0 0 15px; color: var(--danger); }
        .age-card p { color: var(--light-text); margin-bottom: 25px; }
        
        .age-buttons { display: flex; gap: 15px; }
        .age-btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
        }
        .age-yes { background: var(--success); color: white; }
        .age-no { background: #dfe6e9; color: var(--text-color); }

        /* Profile Page */
        .profile-page { padding: 20px; }

        .profile-header { text-align: center; margin-bottom: 30px; }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 3em;
            color: white;
        }

        .profile-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-section h3 { margin: 0 0 15px; font-size: 1.1em; color: var(--text-color); }
        .profile-field { margin-bottom: 15px; }
        .profile-field label {
            font-size: 0.8em;
            color: var(--light-text);
            display: block;
            margin-bottom: 5px;
        }

        .profile-field input, .profile-field textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
        }

        .profile-field input:read-only { background: #f8f9fa; }

        /* Settings Page */
        .settings-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .settings-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .settings-item:last-child { border-bottom: none; }
        .settings-item span { color: var(--text-color); }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        @media (max-width: 480px) {
            .mobile-container { border-radius: 0; height: 100vh; }
            nav { border-radius: 0; }
        }
    </style>
</head>
<body>

    <div class="mobile-container">

        <div class="page-history" id="pageHistory"></div>

        <!-- MAIN APP -->
        <div id="appContainer" class="page active">
            <header class="app-header">
                <div class="header-top">
                    <h1><i class="fas fa-bolt"></i> Kanoja</h1>
                    
                    <div class="profile-container" id="profileContainer">
                        <div class="profile-trigger" onclick="toggleDropdown()">
                            <div class="profile-avatar" id="profileAvatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="profile-name" id="profileName">Guest</span>
                            <i class="fas fa-chevron-down" id="dropdownArrow"></i>
                        </div>
                        
                        <div class="dropdown-menu" id="dropdownMenu">
                            <div class="dropdown-header" id="dropdownHeader">
                                <h4 id="dropdownName">Guest User</h4>
                                <p id="dropdownEmail">Sign in to access your account</p>
                            </div>
                            <div class="dropdown-item" onclick="navigateToProfile()">
                                <i class="fas fa-user-circle"></i>
                                <span>My Profile</span>
                            </div>
                            <div class="dropdown-item" onclick="navigateToOrders()">
                                <i class="fas fa-receipt"></i>
                                <span>My Orders</span>
                            </div>
                            <div class="dropdown-item" onclick="navigateToNotifications()">
                                <i class="fas fa-bell"></i>
                                <span>Notifications</span>
                            </div>
                            <div class="dropdown-item" onclick="navigateToSettings()">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </div>
                            <div class="dropdown-item logout" id="logoutBtn" onclick="handleLogout()" style="display: none;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </div>
                            <div class="dropdown-item" id="loginBtn" onclick="showAuthModal()">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Sign In / Register</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="search-container">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search food, drinks, snacks..." autocomplete="off">
                    </div>
                    <div class="suggestions-container" id="suggestionsContainer"></div>
                </div>
            </header>

            <main>
                <section class="category-section">
                    <div class="category-item category-food" onclick="filterCategory('food')">
                        <div class="category-icon"><i class="fas fa-utensils"></i></div>
                        <p>Food</p>
                    </div>
                    <div class="category-item category-beverage" onclick="filterCategory('beverage')">
                        <div class="category-icon"><i class="fas fa-glass-whiskey"></i></div>
                        <p>Beverages</p>
                    </div>
                    <div class="category-item category-snack" onclick="filterCategory('snack')">
                        <div class="category-icon"><i class="fas fa-cookie-bite"></i></div>
                        <p>Snacks</p>
                    </div>
                    <div class="category-item category-tobacco" onclick="showAgeVerify('tobacco')">
                        <div class="category-icon"><i class="fas fa-smoking"></i></div>
                        <p>Tobacco</p>
                    </div>
                </section>

                <!-- Dynamic Promo Banner (Controlled by Admin) -->
                <div class="promo-banner" id="dynamicBanner" onclick="trackBannerClick()">
                    <div class="promo-content" id="bannerContent">
                        <h3 id="bannerHeadline">Hungry? We're Open!</h3>
                        <p id="bannerSubheadline">Serving You from 9 PM to 4 PM</p>
                        <div class="promo-min-order" id="bannerMinOrder">
                            Min Order: ₹250
                        </div>
                    </div>
                    <i class="fas fa-utensils" id="bannerIcon"></i>
                </div>

                <h2 class="section-title"><i class="fas fa-fire" style="color: #ff6b6b;"></i> Popular Restaurants</h2>
                <div class="horizontal-scroll" id="restaurantsContainer"></div>

                <h2 class="section-title"><i class="fas fa-thumbs-up" style="color: var(--primary-color);"></i> Recommended For You</h2>
                <div class="horizontal-scroll" id="dishesContainer"></div>
            </main>

            <div id="cart-fab-container">
                <button class="fab-btn" onclick="handleCartClick()">
                    <i class="fas fa-shopping-basket"></i>
                    <div id="cart-count" style="display: none;">0</div>
                </button>
            </div>

            <nav>
                <div class="nav-item active" onclick="showPage('appContainer')">
                    <i class="fas fa-home"></i>
                    <p>Home</p>
                </div>
                <div class="nav-item" onclick="navigateToOrders()">
                    <i class="fas fa-receipt"></i>
                    <p>Orders</p>
                </div>
                <div class="nav-item" onclick="navigateToNotifications()">
                    <i class="fas fa-bell"></i>
                    <p>Alerts</p>
                </div>
                <div class="nav-item" onclick="toggleDropdown()">
                    <i class="fas fa-user"></i>
                    <p>Profile</p>
                </div>
            </nav>
        </div>

        <!-- CART PAGE -->
        <div id="cartPage" class="page">
            <header class="page-header">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2>My Cart</h2>
            </header>
            
            <main style="padding: 20px;">
                <div id="cartItemsContainer"></div>
                
                <div class="cart-summary" id="cartSummary" style="display: none;">
                    <div id="minimumOrderWarning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="minimumOrderMessage">Add ₹<span id="shortByAmount">0</span> more to reach minimum order (₹250)</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal-price">₹0</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span>₹40</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="total-price">₹40</span>
                    </div>
                    <button class="btn-main" onclick="handleCheckoutClick()" style="margin-top: 20px;" id="checkoutBtn">
                        Proceed to Checkout
                    </button>
                </div>
            </main>
        </div>

        <!-- CHECKOUT PAGE -->
        <div id="checkoutPage" class="page">
            <header class="page-header">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2>Checkout</h2>
            </header>
            
            <main style="padding: 20px;">
                <div class="form-group">
                    <label>Delivery Location</label>
                    <button class="location-btn" onclick="getLocation()">
                        <i class="fas fa-location-arrow"></i> Use My Current Location
                    </button>
                    <div id="map-container">
                        <div id="map" style="height: 100%; width: 100%;"></div>
                    </div>
                    <input type="text" id="addressText" class="form-control" placeholder="House/Flat No., Landmark, Area" style="margin-top: 10px;">
                    <input type="hidden" id="lat">
                    <input type="hidden" id="lng">
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" id="contactPhone" class="form-control" placeholder="10-digit mobile number">
                </div>

                <div class="or-divider">Email Verification (Optional)</div>

                <div class="form-group">
                    <div style="display: flex; gap: 10px;">
                        <input type="email" id="verifyEmail" class="form-control" readonly style="flex: 1;">
                        <button class="verify-btn" id="sendOtpBtn" onclick="sendOTP()">Send OTP</button>
                    </div>
                    
                    <div class="otp-group" id="otpSection" style="display: none;">
                        <input type="text" id="otpCode" class="form-control" placeholder="Enter 6-digit OTP" maxlength="6">
                        <button class="verify-btn" style="background: var(--success);" onclick="verifyOTP()">Verify</button>
                    </div>

                    <div id="verifiedBadge" class="verified-badge">
                        <i class="fas fa-check-circle"></i> Verified Successfully
                    </div>
                    <p id="otpMsg" style="font-size: 0.85em; color: var(--light-text); margin-top: 8px;"></p>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <div class="payment-methods">
                        <div class="payment-method selected" onclick="selectPaymentMethod('cod')" id="payment-cod">
                            <i class="fas fa-money-bill-wave"></i>
                            <p>Cash on Delivery</p>
                            <small>Pay when you receive</small>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('card')" id="payment-card">
                            <i class="fas fa-credit-card"></i>
                            <p>Card/UPI Payment</p>
                            <small>Pay with Razorpay</small>
                        </div>
                    </div>
                </div>

                <div class="payment-loading" id="payment-loading">
                    <div class="spinner"></div>
                    <p>Processing your payment...</p>
                </div>

                <button class="btn-main" id="placeOrderBtn" onclick="placeOrder()" disabled>
                    Enter Phone or Verify Email to Order
                </button>
            </main>
        </div>

        <!-- ORDERS PAGE -->
        <div id="ordersPage" class="page">
            <header class="page-header">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2>My Orders</h2>
            </header>
            
            <main style="padding: 20px;">
                <div class="order-filters" id="orderFilters">
                    <div class="order-filter active" onclick="filterOrders('all')">All</div>
                    <div class="order-filter" onclick="filterOrders('active')">Active</div>
                    <div class="order-filter" onclick="filterOrders('delivered')">Delivered</div>
                    <div class="order-filter" onclick="filterOrders('cancelled')">Cancelled</div>
                </div>
                
                <div id="ordersListContainer"></div>
            </main>
        </div>

        <!-- ORDER DETAIL MODAL -->
        <div class="order-detail-modal" id="orderDetailModal">
            <div class="order-detail-card" id="orderDetailCard"></div>
        </div>

        <!-- NOTIFICATIONS PAGE -->
        <div id="notificationsPage" class="page">
            <header class="page-header">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2>Notifications</h2>
            </header>
            
            <main style="padding: 20px;">
                <div id="notificationListContainer"></div>
            </main>
        </div>

        <!-- PROFILE PAGE -->
        <div id="profilePage" class="page">
            <header class="page-header">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2>My Profile</h2>
            </header>
            
            <main class="profile-page">
                <div class="profile-header">
                    <div class="profile-avatar-large" id="profileAvatarLarge">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 id="profileDisplayName">Guest User</h3>
                    <p id="profileEmail" style="color: var(--light-text);">Not signed in</p>
                </div>

                <div class="profile-section">
                    <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
                    
                    <div class="profile-field">
                        <label>Full Name</label>
                        <input type="text" id="profileFullName" placeholder="Your name" readonly>
                    </div>
                    
                    <div class="profile-field">
                        <label>Email Address</label>
                        <input type="email" id="profileEmailInput" placeholder="Your email" readonly>
                    </div>
                    
                    <div class="profile-field">
                        <label>Phone Number</label>
                        <input type="tel" id="profilePhone" placeholder="Add phone number">
                    </div>
                    
                    <div class="profile-field">
                        <label>Default Address</label>
                        <textarea id="profileAddress" placeholder="Add your default delivery address" rows="3"></textarea>
                    </div>
                    
                    <button class="btn-main" onclick="saveProfile()" style="margin-top: 10px;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>

                <div class="profile-section">
                    <h3><i class="fas fa-cog"></i> Account Actions</h3>
                    
                    <div class="dropdown-item" onclick="navigateToOrders()" style="padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <i class="fas fa-receipt"></i>
                        <span>My Orders</span>
                    </div>
                    
                    <div class="dropdown-item" onclick="navigateToNotifications()" style="padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </div>
                    
                    <div class="dropdown-item" onclick="changePassword()" style="padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <i class="fas fa-key"></i>
                        <span>Change Password</span>
                    </div>
                    
                    <div class="dropdown-item logout" onclick="handleLogout()" style="padding: 12px 0;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </div>
                </div>
            </main>
        </div>

        <!-- SETTINGS PAGE -->
        <div id="settingsPage" class="page">
            <header class="page-header">
                <button class="back-btn" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2>Settings</h2>
            </header>
            
            <main style="padding: 20px;">
                <div class="settings-section">
                    <h3><i class="fas fa-bell"></i> Notifications</h3>
                    
                    <div class="settings-item">
                        <span>Push Notifications</span>
                        <label class="switch">
                            <input type="checkbox" id="notificationsEnabled" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="settings-item">
                        <span>Email Notifications</span>
                        <label class="switch">
                            <input type="checkbox" id="emailNotifications" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="settings-item">
                        <span>Sound Alerts for Order Updates</span>
                        <label class="switch">
                            <input type="checkbox" id="soundAlerts" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div class="settings-section">
                    <h3><i class="fas fa-globe"></i> Language & Region</h3>
                    
                    <div class="settings-item">
                        <span>Language</span>
                        <select class="form-control" style="width: auto;" id="languageSelect">
                            <option value="en">English</option>
                            <option value="hi">Hindi</option>
                            <option value="gu">Gujarati</option>
                        </select>
                    </div>
                    
                    <div class="settings-item">
                        <span>Currency</span>
                        <select class="form-control" style="width: auto;" id="currencySelect">
                            <option value="INR">INR (₹)</option>
                            <option value="USD">USD ($)</option>
                        </select>
                    </div>
                </div>

                <div class="settings-section">
                    <h3><i class="fas fa-paint-brush"></i> Appearance</h3>
                    
                    <div class="settings-item">
                        <span>Dark Mode</span>
                        <label class="switch">
                            <input type="checkbox" id="darkMode">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div class="settings-section">
                    <h3><i class="fas fa-info-circle"></i> About</h3>
                    
                    <div class="dropdown-item" onclick="openPrivacyPolicy()" style="padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <i class="fas fa-shield-alt"></i>
                        <span>Privacy Policy</span>
                    </div>
                    
                    <div class="dropdown-item" onclick="openTerms()" style="padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <i class="fas fa-file-contract"></i>
                        <span>Terms of Service</span>
                    </div>
                    
                    <div class="dropdown-item" onclick="contactSupport()" style="padding: 12px 0;">
                        <i class="fas fa-headset"></i>
                        <span>Contact Support</span>
                    </div>
                    
                    <p style="text-align: center; color: var(--light-text); font-size: 0.8em; margin-top: 20px;">
                        Version 2.0.0
                    </p>
                </div>
            </main>
        </div>

        <!-- THANK YOU PAGE -->
        <div id="thankYouPage" class="page">
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Order Placed!</h2>
                <p>Your delicious food is being prepared</p>
                <button class="track-btn" onclick="navigateToOrders()">
                    <i class="fas fa-map-marker-alt"></i> Track Order
                </button>
                <a class="home-link" onclick="goHome(); return false;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>

    </div>

    <!-- Auth Modal -->
    <div class="auth-modal" id="authModal">
        <div class="auth-card">
            <button class="close-auth" onclick="closeAuthModal()"><i class="fas fa-times"></i></button>
            
            <div id="loginForm">
                <div class="auth-header">
                    <div class="logo-circle"><i class="fas fa-bolt"></i></div>
                    <h1>Welcome Back</h1>
                    <p>Sign in to continue with your order</p>
                </div>
                
                <div class="auth-body">
                    <p id="auth-error"></p>
                    
                    <div class="input-group">
                        <label class="input-label">Email Address</label>
                        <input type="email" id="loginEmail" class="auth-input" placeholder="name@example.com">
                        <i class="fas fa-envelope"></i>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Password</label>
                        <input type="password" id="loginPassword" class="auth-input" placeholder="••••••••">
                        <i class="fas fa-lock"></i>
                    </div>
                    
                    <button class="btn-main" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    
                    <button class="btn-main btn-google" id="googleBtn">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="G" width="20"> 
                        Sign in with Google
                    </button>
                    
                    <div class="auth-switch">
                        Don't have an account? <a id="gotoRegister">Register</a>
                    </div>
                </div>
            </div>

            <div id="registerForm" style="display: none;">
                <div class="auth-header">
                    <div class="logo-circle"><i class="fas fa-user-plus"></i></div>
                    <h1>Create Account</h1>
                    <p>Join the Kanoja family</p>
                </div>
                
                <div class="auth-body">
                    <div class="input-group">
                        <label class="input-label">Full Name</label>
                        <input type="text" id="regName" class="auth-input" placeholder="John Doe">
                        <i class="fas fa-user"></i>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Email Address</label>
                        <input type="email" id="registerEmail" class="auth-input" placeholder="name@example.com">
                        <i class="fas fa-envelope"></i>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">Password</label>
                        <input type="password" id="registerPassword" class="auth-input" placeholder="••••••••">
                        <i class="fas fa-lock"></i>
                    </div>
                    
                    <button class="btn-main" id="registerBtn">
                        <i class="fas fa-user-plus"></i> Sign Up
                    </button>
                    
                    <div class="auth-switch">
                        Already have an account? <a id="gotoLogin">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Age Verification Modal -->
    <div class="age-verify" id="ageVerifyModal">
        <div class="age-card">
            <i class="fas fa-exclamation-triangle" style="font-size: 3em; color: var(--danger); margin-bottom: 15px;"></i>
            <h3>Age Verification Required</h3>
            <p>You must be 18 years or older to purchase tobacco products. Please confirm your age.</p>
            <div class="age-buttons">
                <button class="age-btn age-no" onclick="closeAgeVerify()">I am under 18</button>
                <button class="age-btn age-yes" onclick="confirmAge()">I am 18+</button>
            </div>
        </div>
    </div>

    <!-- Audio for Notifications -->
    <audio id="notificationSound" preload="auto" style="display: none;">
        <source src="https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3" type="audio/mpeg">
    </audio>

    <!-- Firebase Scripts -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
    
    <!-- Leaflet Maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // ==========================================
        // FIREBASE CONFIGURATION
        // ==========================================
        const firebaseConfig = <?php echo json_encode($firebaseConfig); ?>;
        
        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();
        const db = firebase.database();
        const googleProvider = new firebase.auth.GoogleAuthProvider();

        // ==========================================
        // RAZORPAY CONFIGURATION
        // ==========================================
        const razorpayKeyId = '<?php echo $razorpay_key_id; ?>';
        let paymentMethod = 'cod';

        // ==========================================
        // MINIMUM ORDER CONFIGURATION
        // ==========================================
        const MINIMUM_ORDER = <?php echo $minimum_order_amount; ?>;

        // ==========================================
        // PAGE HISTORY MANAGEMENT
        // ==========================================
        let pageHistory = ['appContainer'];
        let currentPage = 'appContainer';
        let isNavigating = false;
        let isBackNavigation = false;

        function showPage(pageId) {
            if (isNavigating) return;
            isNavigating = true;
            
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            document.getElementById(pageId).classList.add('active');
            
            if (currentPage !== pageId && !isBackNavigation) {
                pageHistory.push(pageId);
                history.pushState({ page: pageId }, '', `#${pageId}`);
            }
            
            currentPage = pageId;
            isBackNavigation = false;
            
            if (pageId === 'checkoutPage') {
                setTimeout(() => { if(map) map.invalidateSize(); }, 300);
                if(currentUser) {
                    document.getElementById('verifyEmail').value = currentUser.email;
                }
                checkOrderValidity();
            }
            
            if (pageId === 'ordersPage' && currentUser) loadOrders();
            if (pageId === 'notificationsPage' && currentUser) loadNotifications();
            if (pageId === 'profilePage') loadProfile();
            
            document.getElementById('dropdownMenu').classList.remove('show');
            isNavigating = false;
        }

        function goBack() {
            if (isNavigating) return;
            isNavigating = true;
            isBackNavigation = true;
            
            if (pageHistory.length > 1) {
                pageHistory.pop();
                const previousPage = pageHistory[pageHistory.length - 1];
                
                document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
                document.getElementById(previousPage).classList.add('active');
                currentPage = previousPage;
                
                if (previousPage === 'checkoutPage') {
                    setTimeout(() => { if(map) map.invalidateSize(); }, 300);
                }
            } else {
                if (currentPage !== 'appContainer') {
                    pageHistory = ['appContainer'];
                    showPage('appContainer');
                }
            }
            
            isNavigating = false;
        }

        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.page) {
                const targetPage = event.state.page;
                
                if (!pageHistory.includes(targetPage)) {
                    pageHistory.push(targetPage);
                } else {
                    const targetIndex = pageHistory.indexOf(targetPage);
                    pageHistory = pageHistory.slice(0, targetIndex + 1);
                }
                
                document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
                document.getElementById(targetPage).classList.add('active');
                currentPage = targetPage;
                
                if (targetPage === 'checkoutPage') {
                    setTimeout(() => { if(map) map.invalidateSize(); }, 300);
                }
            } else {
                goBack();
            }
        });

        history.replaceState({ page: 'appContainer' }, '', '#appContainer');

        function goHome() {
            pageHistory = ['appContainer'];
            showPage('appContainer');
            history.pushState({ page: 'appContainer' }, '', '#appContainer');
        }

        // ==========================================
        // SOUND NOTIFICATION SYSTEM
        // ==========================================
        const notificationSound = document.getElementById('notificationSound');
        let soundAlertsEnabled = true;

        function playNotificationSound() {
            if (soundAlertsEnabled && notificationSound) {
                notificationSound.play().catch(e => console.log('Sound play failed:', e));
            }
        }

        // ==========================================
        // SEARCH WITH SUGGESTIONS
        // ==========================================
        let searchTimeout = null;
        let allDishes = [];
        let allRestaurants = [];

        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            const suggestionsContainer = document.getElementById('suggestionsContainer');

            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                
                if (searchTimeout) clearTimeout(searchTimeout);

                if (query.length < 2) {
                    suggestionsContainer.classList.remove('show');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    suggestionsContainer.classList.remove('show');
                }
            });
        }

        function performSearch(query) {
            const suggestionsContainer = document.getElementById('suggestionsContainer');
            
            const results = allDishes.filter(dish => 
                dish.name.toLowerCase().includes(query) || 
                (dish.category && dish.category.toLowerCase().includes(query))
            );

            if (results.length === 0) {
                suggestionsContainer.innerHTML = '<div class="no-results">No products found</div>';
                suggestionsContainer.classList.add('show');
                return;
            }

            const limitedResults = results.slice(0, 10);

            suggestionsContainer.innerHTML = limitedResults.map(item => `
                <div class="suggestion-item" onclick="selectSuggestion('${item.key}')">
                    <img src="${item.imageUrl}" alt="${item.name}" onerror="this.src='https://via.placeholder.com/40'">
                    <div class="suggestion-info">
                        <h4>${item.name}</h4>
                        <p>${item.category || 'Product'}</p>
                    </div>
                    <div class="suggestion-price">₹${item.price}</div>
                </div>
            `).join('');

            suggestionsContainer.classList.add('show');
        }

        function selectSuggestion(productId) {
            const product = allDishes.find(d => d.key === productId);
            if (product) {
                addToCart(product.key, product.name, product.price, product.imageUrl);
                document.getElementById('suggestionsContainer').classList.remove('show');
                document.getElementById('searchInput').value = '';
            }
        }

        // ==========================================
        // DYNAMIC BANNER FROM ADMIN
        // ==========================================
        function loadBannerSettings() {
            db.ref('settings/banner').on('value', snap => {
                if (snap.exists()) {
                    const settings = snap.val();
                    
                    if (settings.active === false) {
                        document.getElementById('dynamicBanner').style.display = 'none';
                        return;
                    }
                    
                    document.getElementById('dynamicBanner').style.display = 'flex';
                    document.getElementById('bannerHeadline').textContent = settings.headline || 'Hungry? We\'re Open!';
                    document.getElementById('bannerSubheadline').textContent = settings.subheadline || 'Serving You from 9 PM to 4 PM';
                    document.getElementById('bannerMinOrder').textContent = settings.minOrderDisplay || 'Min Order: ₹250';
                    document.getElementById('bannerIcon').className = `fas ${settings.icon || 'fa-utensils'}`;
                    
                    const gradient = `linear-gradient(135deg, ${settings.colorStart || '#2d3436'} 0%, ${settings.colorEnd || '#636e72'} 100%)`;
                    document.getElementById('dynamicBanner').style.background = gradient;
                }
            });
        }

        function trackBannerClick() {
            if (currentUser) {
                db.ref('analytics/banner').push({
                    type: 'click',
                    userId: currentUser.uid,
                    timestamp: Date.now()
                });
            }
            
            // Track banner view
            db.ref('analytics/banner').push({
                type: 'view',
                userId: currentUser ? currentUser.uid : 'guest',
                timestamp: Date.now()
            });
            
            // Scroll to dishes section or perform action
            document.querySelector('.section-title').scrollIntoView({ behavior: 'smooth' });
        }

        // ==========================================
        // DROPDOWN MENU FUNCTIONS
        // ==========================================
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            
            dropdown.classList.toggle('show');
            arrow.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const trigger = document.querySelector('.profile-trigger');
            const navProfile = document.querySelector('.nav-item:last-child');
            
            if (!trigger.contains(event.target) && !navProfile.contains(event.target)) {
                dropdown.classList.remove('show');
                document.getElementById('dropdownArrow').style.transform = 'rotate(0deg)';
            }
        });

        // ==========================================
        // NAVIGATION FUNCTIONS
        // ==========================================
        let pendingAction = null;

        function navigateToProfile() {
            if (!currentUser) {
                pendingAction = () => showPage('profilePage');
                showAuthModal();
            } else {
                showPage('profilePage');
            }
        }

        function navigateToOrders() {
            if (!currentUser) {
                pendingAction = () => {
                    showPage('ordersPage');
                    loadOrders();
                };
                showAuthModal();
            } else {
                showPage('ordersPage');
                loadOrders();
            }
        }

        function navigateToNotifications() {
            if (!currentUser) {
                pendingAction = () => {
                    showPage('notificationsPage');
                    loadNotifications();
                };
                showAuthModal();
            } else {
                showPage('notificationsPage');
                loadNotifications();
            }
        }

        function navigateToSettings() {
            if (!currentUser) {
                pendingAction = () => showPage('settingsPage');
                showAuthModal();
            } else {
                showPage('settingsPage');
            }
        }

        // ==========================================
        // AUTH MODAL MANAGEMENT
        // ==========================================
        function showAuthModal() {
            document.getElementById('authModal').classList.add('active');
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('dropdownMenu').classList.remove('show');
        }

        function closeAuthModal() {
            document.getElementById('authModal').classList.remove('active');
            pendingAction = null;
        }

        document.getElementById('gotoRegister').onclick = () => {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        };

        document.getElementById('gotoLogin').onclick = () => {
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        };

        // ==========================================
        // AUTHENTICATION
        // ==========================================
        document.getElementById('loginBtn').onclick = () => {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            auth.signInWithEmailAndPassword(email, password)
                .then(() => {
                    closeAuthModal();
                    if (pendingAction) {
                        pendingAction();
                        pendingAction = null;
                    }
                })
                .catch(err => showError(err.message));
        };

        document.getElementById('googleBtn').onclick = () => {
            auth.signInWithPopup(googleProvider)
                .then(() => {
                    closeAuthModal();
                    if (pendingAction) {
                        pendingAction();
                        pendingAction = null;
                    }
                })
                .catch(err => showError(err.message));
        };

        document.getElementById('registerBtn').onclick = () => {
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const name = document.getElementById('regName').value;
            
            auth.createUserWithEmailAndPassword(email, password)
                .then(cred => cred.user.updateProfile({ displayName: name }))
                .then(() => {
                    closeAuthModal();
                    if (pendingAction) {
                        pendingAction();
                        pendingAction = null;
                    }
                })
                .catch(err => showError(err.message));
        };

        function showError(msg) {
            const err = document.getElementById('auth-error');
            err.textContent = msg;
            err.style.display = 'block';
            setTimeout(() => err.style.display = 'none', 5000);
        }

        function handleLogout() {
            if (currentUser) {
                logout();
            } else {
                showAuthModal();
            }
        }

        function logout() {
            // Clear local cart array
            cart = [];
            
            // Update cart UI to reflect empty cart
            updateCartUI();
            
            // Hide cart FAB
            document.getElementById('cart-fab-container').classList.remove('visible');
            
            // Clear cart count badge
            const badge = document.getElementById('cart-count');
            badge.textContent = '0';
            badge.style.display = 'none';
            
            // Sign out from Firebase
            auth.signOut();
            
            // Navigate to home page
            showPage('appContainer');
            
            // Update profile UI
            updateProfileUI(null);
            
            // Clear any pending actions
            pendingAction = null;
            
            // Clear cart items container if on cart page
            const cartContainer = document.getElementById('cartItemsContainer');
            if (cartContainer) {
                cartContainer.innerHTML = '<p style="text-align: center; color: var(--light-text); margin-top: 50px;">Your cart is empty</p>';
            }
            
            // Hide cart summary
            const cartSummary = document.getElementById('cartSummary');
            if (cartSummary) {
                cartSummary.style.display = 'none';
            }
            
            // Show success message
            showToast('Logged out successfully', 'success');
        }

        function updateProfileUI(user) {
            const profileName = document.getElementById('profileName');
            const profileAvatar = document.getElementById('profileAvatar');
            const profileAvatarLarge = document.getElementById('profileAvatarLarge');
            const dropdownName = document.getElementById('dropdownName');
            const dropdownEmail = document.getElementById('dropdownEmail');
            const logoutBtn = document.getElementById('logoutBtn');
            const loginBtn = document.getElementById('loginBtn');
            
            if (user) {
                const name = user.displayName || 'User';
                const email = user.email || '';
                const initial = name.charAt(0).toUpperCase();
                
                profileName.textContent = name.split(' ')[0];
                profileAvatar.innerHTML = `<span>${initial}</span>`;
                if (profileAvatarLarge) {
                    profileAvatarLarge.innerHTML = `<span style="font-size: 2em;">${initial}</span>`;
                }
                dropdownName.textContent = name;
                dropdownEmail.textContent = email;
                logoutBtn.style.display = 'flex';
                loginBtn.style.display = 'none';
            } else {
                profileName.textContent = 'Guest';
                profileAvatar.innerHTML = '<i class="fas fa-user"></i>';
                if (profileAvatarLarge) {
                    profileAvatarLarge.innerHTML = '<i class="fas fa-user"></i>';
                }
                dropdownName.textContent = 'Guest User';
                dropdownEmail.textContent = 'Sign in to access your account';
                logoutBtn.style.display = 'none';
                loginBtn.style.display = 'flex';
            }
        }

        let currentUser = null;

        auth.onAuthStateChanged(user => {
            if(user) {
                currentUser = user;
                updateProfileUI(user);
                loadUserCart();
                listenForNotifications();
            } else {
                currentUser = null;
                updateProfileUI(null);
            }
        });

        // ==========================================
        // CATEGORY FILTERING
        // ==========================================
        let selectedCategory = null;

        function filterCategory(category) {
            selectedCategory = category;
            const filtered = allDishes.filter(d => d.category === category);
            renderDishes(filtered);
        }

        function showAgeVerify(category) {
            document.getElementById('ageVerifyModal').classList.add('active');
            selectedCategory = category;
        }

        function closeAgeVerify() {
            document.getElementById('ageVerifyModal').classList.remove('active');
            selectedCategory = null;
        }

        function confirmAge() {
            closeAgeVerify();
            filterCategory('tobacco');
        }

        // ==========================================
        // DATA LOADING
        // ==========================================
        function loadData() {
            db.ref('restaurants').once('value', snap => {
                allRestaurants = [];
                snap.forEach(child => {
                    allRestaurants.push({ key: child.key, ...child.val() });
                });
                renderRestaurants();
            });

            db.ref('dishes').once('value', snap => {
                allDishes = [];
                snap.forEach(child => {
                    allDishes.push({ key: child.key, ...child.val() });
                });
                renderDishes(allDishes);
            });
        }

        function renderRestaurants() {
            const container = document.getElementById('restaurantsContainer');
            container.innerHTML = allRestaurants.map(r => `
                <div class="card">
                    <img src="${r.imageUrl}" alt="${r.name}" onerror="this.src='https://via.placeholder.com/150'">
                    <div class="card-content">
                        <h3>${r.name}</h3>
                        <p><i class="fas fa-star" style="color: #ffd700;"></i> ${r.rating}</p>
                    </div>
                </div>
            `).join('');
        }

        function renderDishes(dishes) {
            const container = document.getElementById('dishesContainer');
            container.innerHTML = dishes.map(d => `
                <div class="card">
                    <img src="${d.imageUrl}" alt="${d.name}" onerror="this.src='https://via.placeholder.com/150'">
                    <button class="add-btn" onclick="addToCart('${d.key}', '${d.name}', ${d.price}, '${d.imageUrl}')">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="card-content">
                        <h3>${d.name}</h3>
                        <p>₹${d.price}</p>
                    </div>
                </div>
            `).join('');
        }

        // ==========================================
        // CART MANAGEMENT
        // ==========================================
        let cart = [];

        function addToCart(id, name, price, image) {
            const existing = cart.find(i => i.id === id);
            if(existing) {
                existing.quantity++;
            } else {
                cart.push({ id, name, price, image, quantity: 1 });
            }
            updateCartUI();
            showToast(`${name} added to cart!`);
            
            document.getElementById('cart-fab-container').classList.add('visible');
            
            if(currentUser) {
                db.ref(`carts/${currentUser.uid}`).set(cart);
            }
        }

        function updateCartUI() {
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-count');
            const fabContainer = document.getElementById('cart-fab-container');
            
            if(count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
                fabContainer.classList.add('visible');
            } else {
                badge.textContent = '0';
                badge.style.display = 'none';
                fabContainer.classList.remove('visible');
            }

            const container = document.getElementById('cartItemsContainer');
            const summary = document.getElementById('cartSummary');
            const minimumWarning = document.getElementById('minimumOrderWarning');
            const checkoutBtn = document.getElementById('checkoutBtn');
            
            if(!container) return;
            
            if(cart.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: var(--light-text); margin-top: 50px;">Your cart is empty</p>';
                if(summary) summary.style.display = 'none';
                return;
            }

            summary.style.display = 'block';
            container.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}">
                    <div class="cart-item-details">
                        <h4>${item.name}</h4>
                        <p>₹${item.price * item.quantity}</p>
                        <div class="qty-controls">
                            <button onclick="changeQty('${item.id}', -1)">-</button>
                            <span>${item.quantity}</span>
                            <button onclick="changeQty('${item.id}', 1)">+</button>
                        </div>
                    </div>
                </div>
            `).join('');

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const total = subtotal + 40;
            const check = checkMinimumOrder();
            
            document.getElementById('subtotal-price').textContent = `₹${subtotal}`;
            document.getElementById('total-price').textContent = `₹${total}`;
            
            if (!check.meetsRequirement) {
                minimumWarning.style.display = 'flex';
                document.getElementById('shortByAmount').textContent = check.shortBy;
                checkoutBtn.disabled = true;
            } else {
                minimumWarning.style.display = 'none';
                checkoutBtn.disabled = false;
            }
        }

        function changeQty(id, change) {
            const item = cart.find(i => i.id === id);
            if(item) {
                item.quantity += change;
                if(item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                updateCartUI();
                if(currentUser) {
                    db.ref(`carts/${currentUser.uid}`).set(cart);
                }
            }
        }

        function loadUserCart() {
            if(!currentUser) return;
            db.ref(`carts/${currentUser.uid}`).once('value', snap => {
                if(snap.exists()) {
                    cart = snap.val();
                    updateCartUI();
                }
            });
        }

        function clearCart() {
            cart = [];
            updateCartUI();
            if(currentUser) {
                db.ref(`carts/${currentUser.uid}`).remove();
            }
        }

        // ==========================================
        // MINIMUM ORDER CHECK
        // ==========================================
        function checkMinimumOrder() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            return {
                meetsRequirement: subtotal >= MINIMUM_ORDER,
                subtotal: subtotal,
                shortBy: Math.max(0, MINIMUM_ORDER - subtotal)
            };
        }

        function validateCartForCheckout() {
            const check = checkMinimumOrder();
            
            if (!check.meetsRequirement) {
                showMinimumOrderWarning(check.shortBy);
                return false;
            }
            return true;
        }

        function showMinimumOrderWarning(shortBy) {
            showToast(`Add items worth ₹${shortBy} more to reach minimum order (₹${MINIMUM_ORDER})`, 'warning');
        }

        // ==========================================
        // CART HANDLING WITH AUTH CHECK
        // ==========================================
        function handleCartClick() {
            if (cart.length > 0) {
                showPage('cartPage');
            }
        }

        function handleCheckoutClick() {
            if (!validateCartForCheckout()) {
                return;
            }
            
            if (!currentUser) {
                pendingAction = () => showPage('checkoutPage');
                showAuthModal();
            } else {
                showPage('checkoutPage');
            }
        }

        // ==========================================
        // LOCATION & MAPS
        // ==========================================
        let map = null;
        let marker = null;

        function getLocation() {
            const btn = event.currentTarget;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Locating...';
            
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const { latitude, longitude } = pos.coords;
                        document.getElementById('lat').value = latitude;
                        document.getElementById('lng').value = longitude;
                        
                        document.getElementById('map-container').style.display = 'block';
                        
                        if(!map) {
                            map = L.map('map').setView([latitude, longitude], 16);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        }
                        
                        if(marker) map.removeLayer(marker);
                        marker = L.marker([latitude, longitude]).addTo(map);
                        
                        fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=en`)
                            .then(r => r.json())
                            .then(data => {
                                const address = `${data.locality || ''}, ${data.city || ''}, ${data.principalSubdivision || ''}`;
                                document.getElementById('addressText').value = address;
                                btn.innerHTML = '<i class="fas fa-check"></i> Location Set';
                            });
                    },
                    err => {
                        alert('Location access denied');
                        btn.innerHTML = '<i class="fas fa-location-arrow"></i> Use My Current Location';
                    }
                );
            }
        }

        // ==========================================
        // OTP VERIFICATION
        // ==========================================
        let generatedOTP = null;
        let isVerified = false;

        function sendOTP() {
            const email = document.getElementById('verifyEmail').value;
            const msg = document.getElementById('otpMsg');
            
            if(!email) {
                msg.textContent = 'No email found';
                msg.style.color = 'red';
                return;
            }

            generatedOTP = Math.floor(100000 + Math.random() * 900000);
            msg.textContent = 'Sending OTP...';
            msg.style.color = '#0984e3';

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, otp: generatedOTP })
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                    msg.textContent = `OTP sent to ${email}`;
                    msg.style.color = 'green';
                    document.getElementById('otpSection').style.display = 'flex';
                    document.getElementById('sendOtpBtn').style.display = 'none';
                } else {
                    msg.textContent = 'Email service busy. Test OTP: ' + generatedOTP;
                    msg.style.color = 'orange';
                    document.getElementById('otpSection').style.display = 'flex';
                    document.getElementById('sendOtpBtn').style.display = 'none';
                }
            });
        }

        function verifyOTP() {
            const code = document.getElementById('otpCode').value;
            const msg = document.getElementById('otpMsg');
            
            if(code == generatedOTP) {
                isVerified = true;
                document.getElementById('otpSection').style.display = 'none';
                document.getElementById('verifiedBadge').style.display = 'flex';
                msg.textContent = '';
                checkOrderValidity();
            } else {
                msg.textContent = 'Incorrect OTP code';
                msg.style.color = 'red';
            }
        }

        // ==========================================
        // RAZORPAY PAYMENT INTEGRATION
        // ==========================================
        async function processRazorpayPayment(amount, orderId) {
            return new Promise(async (resolve, reject) => {
                try {
                    document.getElementById('payment-loading').classList.add('show');
                    
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'create_razorpay_order',
                            amount: amount * 100
                        })
                    });
                    
                    const orderData = await response.json();
                    
                    if (orderData.error) throw new Error(orderData.error);
                    
                    const options = {
                        key: razorpayKeyId,
                        amount: orderData.amount,
                        currency: orderData.currency,
                        name: 'Kanoja',
                        description: 'Food Order Payment',
                        order_id: orderData.id,
                        handler: function(response) {
                            resolve({
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature
                            });
                        },
                        prefill: {
                            name: currentUser.displayName || 'Customer',
                            email: currentUser.email,
                            contact: document.getElementById('contactPhone').value || ''
                        },
                        theme: { color: '#ff6b35' },
                        modal: {
                            ondismiss: function() {
                                reject(new Error('Payment cancelled by user'));
                            }
                        }
                    };
                    
                    const razorpay = new Razorpay(options);
                    razorpay.open();
                    
                } catch (error) {
                    reject(error);
                } finally {
                    document.getElementById('payment-loading').classList.remove('show');
                }
            });
        }

        function selectPaymentMethod(method) {
            paymentMethod = method;
            document.getElementById('payment-cod').classList.toggle('selected', method === 'cod');
            document.getElementById('payment-card').classList.toggle('selected', method === 'card');
        }

        // ==========================================
        // CHECKOUT & ORDERS (FIXED VERSION)
        // ==========================================
        document.getElementById('contactPhone').addEventListener('input', checkOrderValidity);

        function checkOrderValidity() {
            const phone = document.getElementById('contactPhone').value.trim();
            const btn = document.getElementById('placeOrderBtn');
            
            if(phone.length >= 10 || isVerified) {
                btn.disabled = false;
                btn.textContent = 'Place Order';
            } else {
                btn.disabled = true;
                btn.textContent = 'Enter Phone or Verify Email to Order';
            }
        }

        async function placeOrder() {
            if (!currentUser) {
                pendingAction = placeOrder;
                showAuthModal();
                return;
            }

            const phone = document.getElementById('contactPhone').value.trim();
            const address = document.getElementById('addressText').value;
            const lat = document.getElementById('lat').value;
            const lng = document.getElementById('lng').value;

            if(!address) {
                showToast('Please provide delivery address', 'warning');
                return;
            }

            if(!phone && !isVerified) {
                showToast('Please provide phone number or verify email', 'warning');
                return;
            }

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const deliveryFee = 40;
            const total = subtotal + deliveryFee;
            
            let paymentDetails = {};

            if (paymentMethod === 'card') {
                try {
                    const paymentResult = await processRazorpayPayment(total);
                    paymentDetails = {
                        paymentMethod: 'Card/UPI (Razorpay)',
                        paymentStatus: 'Paid',
                        razorpay_payment_id: paymentResult.razorpay_payment_id,
                        razorpay_order_id: paymentResult.razorpay_order_id
                    };
                } catch (error) {
                    showToast('Payment failed: ' + error.message, 'error');
                    return;
                }
            } else {
                paymentDetails = {
                    paymentMethod: 'Cash on Delivery',
                    paymentStatus: 'Pending'
                };
            }

            const orderId = db.ref('orders').push().key;
            const timestamp = Date.now();

            const order = {
                orderId: orderId,
                userId: currentUser.uid,
                userEmail: currentUser.email,
                userName: currentUser.displayName || 'User',
                phone: phone || 'Verified via Email',
                address: address,
                location: { 
                    lat: lat || null, 
                    lng: lng || null 
                },
                items: cart.map(item => ({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    image: item.image
                })),
                subtotal: subtotal,
                deliveryFee: deliveryFee,
                total: total,
                status: 'Placed',
                ...paymentDetails,
                createdAt: timestamp,
                updatedAt: timestamp,
                estimatedDeliveryTime: timestamp + (45 * 60 * 1000)
            };

            const placeOrderBtn = document.getElementById('placeOrderBtn');
            const originalText = placeOrderBtn.innerHTML;
            placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing Order...';
            placeOrderBtn.disabled = true;

            try {
                await db.ref(`orders/${orderId}`).set(order);
                
                await db.ref(`notifications/${currentUser.uid}`).push({
                    title: 'Order Placed Successfully',
                    body: `Your order #${orderId.slice(-6)} has been placed. Total: ₹${total}`,
                    timestamp: Date.now(),
                    read: false,
                    type: 'order_placed',
                    orderId: orderId
                });
                
                await db.ref(`users/${currentUser.uid}/orders/${orderId}`).set({
                    orderId: orderId,
                    total: total,
                    status: 'Placed',
                    createdAt: timestamp
                });
                
                playNotificationSound();
                
                cart = [];
                await db.ref(`carts/${currentUser.uid}`).remove();
                updateCartUI();
                
                showPage('thankYouPage');
                showToast('Order placed successfully!', 'success');
                
                db.ref('analytics/orders').push({
                    orderId: orderId,
                    userId: currentUser.uid,
                    amount: total,
                    timestamp: timestamp
                });
                
            } catch (error) {
                console.error('Order placement error:', error);
                showToast('Error placing order: ' + error.message, 'error');
                placeOrderBtn.innerHTML = originalText;
                placeOrderBtn.disabled = false;
            }
        }

        let currentOrderFilter = 'all';

        function filterOrders(filter) {
            currentOrderFilter = filter;
            
            document.querySelectorAll('.order-filter').forEach(el => {
                el.classList.remove('active');
            });
            event.target.classList.add('active');
            
            loadOrders();
        }

        function loadOrders() {
            if(!currentUser) {
                document.getElementById('ordersListContainer').innerHTML = 
                    '<p style="text-align: center; color: var(--light-text); margin-top: 50px;">Please login to view your orders</p>';
                return;
            }
            
            db.ref('orders')
                .orderByChild('userId')
                .equalTo(currentUser.uid)
                .on('value', snap => {
                    const container = document.getElementById('ordersListContainer');
                    
                    if(!snap.exists()) {
                        container.innerHTML = '<p style="text-align: center; color: var(--light-text); margin-top: 50px;">No orders yet</p>';
                        return;
                    }

                    const orders = [];
                    snap.forEach(child => {
                        orders.push({ 
                            key: child.key, 
                            ...child.val() 
                        });
                    });
                    
                    orders.sort((a, b) => (b.createdAt || 0) - (a.createdAt || 0));

                    let filteredOrders = orders;
                    if (currentOrderFilter === 'active') {
                        filteredOrders = orders.filter(o => 
                            ['Placed', 'Preparing', 'Out for Delivery'].includes(o.status)
                        );
                    } else if (currentOrderFilter === 'delivered') {
                        filteredOrders = orders.filter(o => o.status === 'Delivered');
                    } else if (currentOrderFilter === 'cancelled') {
                        filteredOrders = orders.filter(o => o.status === 'Cancelled');
                    }

                    if (filteredOrders.length === 0) {
                        container.innerHTML = `<p style="text-align: center; color: var(--light-text); margin-top: 50px;">No ${currentOrderFilter} orders</p>`;
                        return;
                    }

                    container.innerHTML = filteredOrders.map(o => {
                        const date = o.createdAt ? new Date(o.createdAt).toLocaleDateString() : 'N/A';
                        const time = o.createdAt ? new Date(o.createdAt).toLocaleTimeString() : 'N/A';
                        const itemPreview = o.items ? 
                            o.items.map(i => `${i.quantity}x ${i.name}`).join(', ').substring(0, 50) + (o.items.length > 1 ? '...' : '') 
                            : 'No items';

                        const statusClass = o.status ? `status-${o.status.toLowerCase().replace(/ /g, '')}` : '';

                        return `
                            <div class="order-card" onclick="showOrderDetail('${o.key}')">
                                <div class="order-header">
                                    <span class="order-id">#${o.key.slice(-6)}</span>
                                    <span class="order-status ${statusClass}">${o.status || 'Placed'}</span>
                                </div>
                                <div class="order-items-preview">
                                    <i class="fas fa-utensils"></i> ${itemPreview}
                                </div>
                                <div class="order-footer">
                                    <span class="order-total">₹${o.total || 0}</span>
                                    <span class="order-date">${date} ${time}</span>
                                </div>
                            </div>
                        `;
                    }).join('');
                });
        }

        function showOrderDetail(orderId) {
            db.ref(`orders/${orderId}`).once('value', snap => {
                if (snap.exists()) {
                    const order = { key: snap.key, ...snap.val() };
                    
                    const status = order.status || 'Placed';
                    const items = order.items || [];
                    const subtotal = order.subtotal || items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    const deliveryFee = order.deliveryFee || 40;
                    const total = order.total || (subtotal + deliveryFee);
                    
                    let step1 = '', step2 = '', step3 = '', step4 = '';
                    if(status === 'Placed') step1 = 'active';
                    else if(status === 'Preparing') { step1 = 'passed'; step2 = 'active'; }
                    else if(status === 'Out for Delivery') { step1 = 'passed'; step2 = 'passed'; step3 = 'active'; }
                    else if(status === 'Delivered') { step1 = 'passed'; step2 = 'passed'; step3 = 'passed'; step4 = 'active'; }
                    else if(status === 'Cancelled') { step1 = 'active'; }
                    
                    const itemsList = items.map(item => `
                        <div class="order-item">
                            <span class="order-item-name">${item.quantity}x ${item.name}</span>
                            <span class="order-item-price">₹${item.price * item.quantity}</span>
                        </div>
                    `).join('');
                    
                    const modal = document.getElementById('orderDetailModal');
                    const card = document.getElementById('orderDetailCard');
                    
                    card.innerHTML = `
                        <div class="order-detail-header">
                            <h3>Order #${order.key.slice(-6)}</h3>
                            <button class="close-detail" onclick="closeOrderDetail()"><i class="fas fa-times"></i></button>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <span class="order-status ${status ? `status-${status.toLowerCase().replace(/ /g, '')}` : ''}">${status}</span>
                            <span style="margin-left: 10px; color: var(--light-text);">
                                ${order.createdAt ? new Date(order.createdAt).toLocaleString() : 'Date not available'}
                            </span>
                        </div>
                        
                        ${status !== 'Cancelled' ? `
                        <div class="track-bar" style="margin-bottom: 30px;">
                            <div class="track-step ${step1}"><div class="dot"></div><p>Placed</p></div>
                            <div class="track-step ${step2}"><div class="dot"></div><p>Prep</p></div>
                            <div class="track-step ${step3}"><div class="dot"></div><p>On Way</p></div>
                            <div class="track-step ${step4}"><div class="dot"></div><p>Delivered</p></div>
                        </div>
                        ` : `
                        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                            <i class="fas fa-times-circle"></i> This order has been cancelled
                        </div>
                        `}
                        
                        <h4>Order Items</h4>
                        <div class="order-items-list">
                            ${itemsList || '<p>No items found</p>'}
                        </div>
                        
                        <div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Subtotal</span>
                                <span>₹${subtotal}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Delivery Fee</span>
                                <span>₹${deliveryFee}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2em; margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--border-color);">
                                <span>Total</span>
                                <span>₹${total}</span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h4>Delivery Address</h4>
                            <p style="color: var(--light-text); margin: 5px 0;">${order.address || 'Address not available'}</p>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <div style="flex: 1; padding: 10px; background: #f8f9fa; border-radius: 10px; text-align: center;">
                                <i class="fas fa-credit-card" style="color: var(--primary-color);"></i>
                                <p style="margin: 5px 0 0; font-size: 0.85em;">${order.paymentMethod || 'Cash on Delivery'}</p>
                                <p style="margin: 0; font-size: 0.8em; color: var(--light-text);">${order.paymentStatus || 'Pending'}</p>
                            </div>
                            <div style="flex: 1; padding: 10px; background: #f8f9fa; border-radius: 10px; text-align: center;">
                                <i class="fas fa-phone" style="color: var(--primary-color);"></i>
                                <p style="margin: 5px 0 0; font-size: 0.85em;">${order.phone || 'N/A'}</p>
                            </div>
                        </div>
                        
                        ${order.estimatedDeliveryTime && status === 'Out for Delivery' ? `
                        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 10px;">
                            <i class="fas fa-clock" style="color: #1976d2;"></i>
                            Estimated Delivery: ${new Date(order.estimatedDeliveryTime).toLocaleTimeString()}
                        </div>
                        ` : ''}
                        
                        ${status === 'Delivered' ? `
                        <button class="btn-main" onclick="reorder('${order.key}')" style="margin-top: 20px; background: linear-gradient(135deg, #00b894, #00cec9);">
                            <i class="fas fa-redo-alt"></i> Reorder
                        </button>
                        ` : ''}
                    `;
                    
                    modal.classList.add('active');
                }
            });
        }

        function closeOrderDetail() {
            document.getElementById('orderDetailModal').classList.remove('active');
        }

        function reorder(orderId) {
            db.ref(`orders/${orderId}`).once('value', snap => {
                if (snap.exists()) {
                    const order = snap.val();
                    
                    if (!currentUser) {
                        pendingAction = () => reorder(orderId);
                        showAuthModal();
                        return;
                    }
                    
                    if (order.items && Array.isArray(order.items)) {
                        cart = order.items.map(item => ({
                            id: item.id,
                            name: item.name,
                            price: item.price,
                            image: item.image || 'https://via.placeholder.com/100',
                            quantity: item.quantity
                        }));
                        
                        updateCartUI();
                        showPage('cartPage');
                        showToast('Items added to cart. Please review before placing order.', 'success');
                        
                        db.ref(`carts/${currentUser.uid}`).set(cart);
                    } else {
                        showToast('Unable to reorder: No items found', 'error');
                    }
                }
            });
        }

        // ==========================================
        // NOTIFICATIONS WITH SOUND
        // ==========================================
        function listenForNotifications() {
            if(!currentUser) return;
            
            db.ref(`notifications/${currentUser.uid}`).orderByChild('timestamp').limitToLast(1)
                .on('child_added', snap => {
                    const notif = snap.val();
                    
                    const timeDiff = Date.now() - notif.timestamp;
                    if (timeDiff < 60000) {
                        playNotificationSound();
                        
                        if (Notification.permission === 'granted') {
                            new Notification('Kanoja Order Update', {
                                body: notif.body,
                                icon: 'https://cdn-icons-png.flaticon.com/512/3075/3075977.png'
                            });
                        }
                    }
                    
                    loadNotifications();
                });
        }

        function loadNotifications() {
            if(!currentUser) {
                document.getElementById('notificationListContainer').innerHTML = 
                    '<p style="text-align: center; color: var(--light-text); margin-top: 50px;">Please login to view notifications</p>';
                return;
            }
            
            db.ref(`notifications/${currentUser.uid}`).orderByChild('timestamp').limitToLast(20)
                .once('value', snap => {
                    const container = document.getElementById('notificationListContainer');
                    
                    if(!snap.exists()) {
                        container.innerHTML = '<p style="text-align: center; color: var(--light-text); margin-top: 50px;">No notifications</p>';
                        return;
                    }

                    const notifs = [];
                    snap.forEach(child => {
                        notifs.push({ key: child.key, ...child.val() });
                    });
                    notifs.sort((a, b) => b.timestamp - a.timestamp);

                    container.innerHTML = notifs.map(n => {
                        const time = new Date(n.timestamp).toLocaleString();
                        let icon = 'fa-info-circle';
                        if(n.title.includes('Placed')) icon = 'fa-check-circle';
                        if(n.title.includes('Delivery') || n.status === 'Out for Delivery') icon = 'fa-shipping-fast';
                        if(n.title.includes('Delivered')) icon = 'fa-box-open';

                        return `
                            <div class="notification-card ${!n.read ? 'unread' : ''}" onclick="markNotificationRead('${n.key}')">
                                <div class="notif-icon"><i class="fas ${icon}"></i></div>
                                <div class="notif-content">
                                    <h4>${n.title}</h4>
                                    <p>${n.body}</p>
                                    <span class="notif-time">${time}</span>
                                </div>
                            </div>
                        `;
                    }).join('');
                });
        }

        function markNotificationRead(notifKey) {
            db.ref(`notifications/${currentUser.uid}/${notifKey}`).update({
                read: true
            });
        }

        if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
            Notification.requestPermission();
        }

        // ==========================================
        // PROFILE PAGE FUNCTIONS
        // ==========================================
        function loadProfile() {
            if (!currentUser) return;
            
            document.getElementById('profileDisplayName').textContent = currentUser.displayName || 'User';
            document.getElementById('profileEmail').textContent = currentUser.email || '';
            document.getElementById('profileFullName').value = currentUser.displayName || '';
            document.getElementById('profileEmailInput').value = currentUser.email || '';
            
            db.ref(`users/${currentUser.uid}/profile`).once('value', snap => {
                if (snap.exists()) {
                    const profile = snap.val();
                    document.getElementById('profilePhone').value = profile.phone || '';
                    document.getElementById('profileAddress').value = profile.address || '';
                }
            });
        }

        function saveProfile() {
            if (!currentUser) {
                showAuthModal();
                return;
            }
            
            const phone = document.getElementById('profilePhone').value;
            const address = document.getElementById('profileAddress').value;
            
            db.ref(`users/${currentUser.uid}/profile`).set({
                phone: phone,
                address: address,
                updatedAt: Date.now()
            }).then(() => {
                showToast('Profile updated successfully!');
            }).catch(err => {
                showToast('Error updating profile: ' + err.message);
            });
        }

        function changePassword() {
            if (!currentUser) {
                showAuthModal();
                return;
            }
            
            auth.sendPasswordResetEmail(currentUser.email)
                .then(() => {
                    showToast('Password reset email sent! Check your inbox.');
                })
                .catch(err => {
                    showToast('Error: ' + err.message);
                });
        }

        // ==========================================
        // SETTINGS PAGE FUNCTIONS
        // ==========================================
        function openPrivacyPolicy() {
            window.open('#', '_blank');
        }

        function openTerms() {
            window.open('#', '_blank');
        }

        function contactSupport() {
            window.location.href = 'mailto:support@kanoja.com';
        }

        // ==========================================
        // UTILITIES
        // ==========================================
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? '#2d3436' : (type === 'warning' ? '#f39c12' : '#d63031');
            const icon = type === 'success' ? 'fa-check-circle' : (type === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
            
            toast.style.cssText = `
                position: fixed;
                bottom: 100px;
                left: 50%;
                transform: translateX(-50%);
                background: ${bgColor};
                color: white;
                padding: 15px 25px;
                border-radius: 50px;
                font-size: 0.9em;
                font-weight: 500;
                z-index: 1000;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                animation: slideUp 0.3s ease;
            `;
            toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(20px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Initialize everything
        document.addEventListener('DOMContentLoaded', () => {
            loadData();
            initializeSearch();
            loadBannerSettings();
            
            selectPaymentMethod('cod');
            
            const soundEnabled = localStorage.getItem('soundAlerts');
            if (soundEnabled !== null) {
                soundAlertsEnabled = soundEnabled === 'true';
                document.getElementById('soundAlerts').checked = soundAlertsEnabled;
            }
            
            document.getElementById('soundAlerts').addEventListener('change', function(e) {
                soundAlertsEnabled = e.target.checked;
                localStorage.setItem('soundAlerts', soundAlertsEnabled);
            });
        });
    </script>
</body>
</html>
