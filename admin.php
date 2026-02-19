<?php
// ============================================================
// KANOJA ADMIN PANEL - FIXED ORDER MANAGEMENT
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanoja Admin Panel - Order Management</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #ff6b35;
            --primary-dark: #e55a2b;
            --secondary-color: #FFF9F2;
            --text-color: #2d3436;
            --light-text: #636e72;
            --border-color: #dfe6e9;
            --white: #FFFFFF;
            --danger: #e74c3c;
            --success: #2ecc71;
            --warning: #f39c12;
            --info: #3498db;
            --sidebar-width: 280px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            color: var(--text-color);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background: var(--white);
            padding: 0 20px;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .mobile-toggle {
            font-size: 1.5em;
            cursor: pointer;
            color: var(--text-color);
        }

        .mobile-logo {
            font-weight: 700;
            font-size: 1.3em;
            color: var(--primary-color);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--white);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 25px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 50;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .logo {
            font-size: 1.6em;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo i { color: var(--primary-color); font-size: 1.3em; }

        .nav-links { list-style: none; }

        .nav-links li { margin-bottom: 8px; }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            color: var(--light-text);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            cursor: pointer;
            font-weight: 500;
        }

        .nav-item i { margin-right: 15px; width: 22px; text-align: center; font-size: 1.1em; }

        .nav-item:hover, .nav-item.active {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .logout-btn {
            margin-top: auto;
            color: var(--danger);
        }

        .logout-btn:hover { background: #fee2e2; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            background: #f8f9fa;
        }

        .section { display: none; animation: fadeIn 0.4s ease; }
        .section.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-title {
            font-size: 1.8em;
            margin-bottom: 25px;
            font-weight: 600;
        }

        /* Auth Overlay */
        #authOverlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: var(--white);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 420px;
            text-align: center;
        }

        .login-box h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 2em;
        }

        .login-box h2 {
            color: var(--text-color);
            margin-bottom: 30px;
            font-size: 1.3em;
            font-weight: 500;
        }

        #authError {
            color: var(--danger);
            margin-bottom: 20px;
            font-size: 0.9em;
            padding: 12px;
            background: #fee2e2;
            border-radius: 8px;
            display: none;
        }

        .login-box input {
            width: 100%;
            padding: 16px;
            margin-bottom: 15px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1em;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .login-box input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        /* Buttons */
        .btn-primary {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-secondary {
            padding: 10px 20px;
            background: white;
            color: var(--text-color);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9em;
        }

        .btn-secondary:hover {
            border-color: var(--primary-color);
            background: var(--secondary-color);
        }

        .btn-success {
            background: var(--success);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s;
        }

        .btn-success:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s;
        }

        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-info h3 {
            margin: 0;
            font-size: 2.2em;
            color: var(--text-color);
        }

        .stat-info p {
            margin: 5px 0 0;
            color: var(--light-text);
            font-size: 0.95em;
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            background: var(--secondary-color);
            color: var(--primary-color);
            border-radius: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.6em;
        }

        /* Banner Editor */
        .banner-section {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .banner-preview {
            margin: 25px 0;
            padding: 20px;
            background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .banner-preview-content {
            z-index: 2;
            padding: 20px;
        }

        .banner-preview h3 {
            font-size: 2.2em;
            margin: 0 0 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .banner-preview p {
            font-size: 1.2em;
            opacity: 0.95;
            margin: 0 0 15px;
        }

        .banner-min-order {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 30px;
            display: inline-block;
            font-size: 0.9em;
            backdrop-filter: blur(5px);
        }

        .banner-preview-icon {
            position: absolute;
            right: 0;
            bottom: -20px;
            opacity: 0.2;
            font-size: 12em;
            transform: rotate(10deg);
            pointer-events: none;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-family: inherit;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .color-picker-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-picker-group input[type="color"] {
            width: 60px;
            height: 60px;
            padding: 5px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
        }

        /* Orders Table */
        .table-container {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 600;
            color: var(--light-text);
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8f9fa;
        }

        tr:hover { background: #f8f9fa; }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
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

        .status-select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
            background: white;
            font-family: inherit;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            width: 140px;
        }

        .status-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .payment-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75em;
            font-weight: 600;
        }

        .payment-paid {
            background: #d4edda;
            color: #155724;
        }

        .payment-pending {
            background: #fff3cd;
            color: #856404;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-select {
            padding: 10px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            min-width: 150px;
        }

        .search-input {
            padding: 10px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            flex: 1;
            min-width: 200px;
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-add {
            background: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-add:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .admin-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
            transition: transform 0.3s;
        }

        .admin-card:hover { transform: translateY(-5px); }

        .admin-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .admin-card-body { padding: 20px; }

        .admin-card h4 { margin: 0 0 8px; font-size: 1.1em; }

        .admin-card p {
            color: var(--light-text);
            margin: 0;
            font-size: 0.9em;
        }

        .admin-card .price {
            color: var(--primary-color);
            font-weight: 700;
            margin-top: 8px;
            display: block;
            font-size: 1.2em;
        }

        .delete-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255,255,255,0.95);
            color: var(--danger);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            border: none;
            font-size: 1.2em;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }

        .delete-btn:hover {
            background: var(--danger);
            color: white;
            transform: scale(1.1);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 200;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal.active { display: flex; }

        .modal-content {
            background: var(--white);
            padding: 35px;
            border-radius: 20px;
            width: 100%;
            max-width: 550px;
            position: relative;
            animation: slideUp 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            background: none;
            border: none;
            font-size: 1.8em;
            color: var(--light-text);
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover { color: var(--danger); }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 30px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--success);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .cat-food { background: #fee2e2; color: #991b1b; }
        .cat-beverage { background: #d1fae5; color: #065f46; }
        .cat-snack { background: #fef3c7; color: #92400e; }
        .cat-tobacco { background: #e5e7eb; color: #374151; }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--success);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        .toast.error { background: var(--danger); }
        .toast.warning { background: var(--warning); }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-header { display: flex; }
            
            .sidebar {
                position: fixed;
                top: 0; bottom: 0;
                left: -300px;
                width: 280px;
                z-index: 60;
            }

            .sidebar.active { left: 0; }

            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }

            .main-content {
                padding: 80px 20px 20px;
                width: 100%;
            }

            .stats-grid { grid-template-columns: 1fr; }

            .menu-grid { grid-template-columns: 1fr; }

            .form-row {
                grid-template-columns: 1fr;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>

    <!-- AUTH OVERLAY -->
    <div id="authOverlay">
        <div class="login-box">
            <h1><i class="fas fa-bolt"></i> Kanoja Admin</h1>
            <h2>Admin Login</h2>
            <p id="authError"></p>
            <input type="email" id="adminEmail" placeholder="admin@kanoja.com">
            <input type="password" id="adminPassword" placeholder="Password">
            <button class="btn-primary" onclick="login()">Login to Dashboard</button>
        </div>
    </div>

    <!-- MOBILE HEADER -->
    <div class="mobile-header">
        <div class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="mobile-logo"><i class="fas fa-bolt"></i> Kanoja</div>
        <div style="width: 24px;"></div>
    </div>

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="logo"><i class="fas fa-bolt"></i> Admin Panel</div>
        
        <ul class="nav-links">
            <li class="nav-item active" onclick="showSection('dashboard')">
                <i class="fas fa-chart-pie"></i> Dashboard
            </li>
            <li class="nav-item" onclick="showSection('banner')">
                <i class="fas fa-images"></i> Hero Banner
            </li>
            <li class="nav-item" onclick="showSection('orders')">
                <i class="fas fa-receipt"></i> Orders
            </li>
            <li class="nav-item" onclick="showSection('menu')">
                <i class="fas fa-utensils"></i> Menu Items
            </li>
            <li class="nav-item" onclick="showSection('restaurants')">
                <i class="fas fa-store"></i> Restaurants
            </li>
        </ul>

        <div class="nav-item logout-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        
        <!-- DASHBOARD -->
        <section id="dashboard" class="section active">
            <h1 class="header-title">Dashboard Overview</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-revenue">₹0</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-orders">0</h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-pending">0</h3>
                        <p>Pending Orders</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-dishes">0</h3>
                        <p>Menu Items</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-hamburger"></i></div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-restaurants">0</h3>
                        <p>Restaurants</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-store"></i></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-today">0</h3>
                        <p>Today's Orders</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-cod">0</h3>
                        <p>COD Orders</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-money-bill"></i></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3 id="stat-online">0</h3>
                        <p>Online Payments</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-credit-card"></i></div>
                </div>
            </div>

            <!-- Recent Orders Preview -->
            <div class="banner-section">
                <h2 style="margin-bottom: 20px;">Recent Orders</h2>
                <div class="table-container" style="padding: 0;">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrdersPreview"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- HERO BANNER MANAGEMENT -->
        <section id="banner" class="section">
            <h1 class="header-title">Hero Banner Control</h1>
            
            <div class="banner-section">
                <h2 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-edit" style="color: var(--primary-color);"></i> 
                    Edit Hero Banner
                </h2>
                
                <div class="banner-preview" id="bannerPreview" style="background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);">
                    <div class="banner-preview-content">
                        <h3 id="previewHeadline">Hungry? We're Open!</h3>
                        <p id="previewSubheadline">Serving You from 9 PM to 4 PM</p>
                        <div class="banner-min-order" id="previewMinOrder">Min Order: ₹250</div>
                    </div>
                    <div class="banner-preview-icon">
                        <i class="fas fa-utensils" id="previewIcon"></i>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Headline</label>
                        <input type="text" id="bannerHeadline" placeholder="e.g., Hungry? We're Open!" value="Hungry? We're Open!">
                    </div>
                    
                    <div class="form-group">
                        <label>Subheadline</label>
                        <input type="text" id="bannerSubheadline" placeholder="e.g., Serving You from 9 PM to 4 PM" value="Serving You from 9 PM to 4 PM">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Background Color (Start)</label>
                        <div class="color-picker-group">
                            <input type="color" id="bannerColorStart" value="#2d3436">
                            <input type="text" class="form-control" id="bannerColorStartText" value="#2d3436">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Background Color (End)</label>
                        <div class="color-picker-group">
                            <input type="color" id="bannerColorEnd" value="#636e72">
                            <input type="text" class="form-control" id="bannerColorEndText" value="#636e72">
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Icon</label>
                        <select id="bannerIcon" class="form-control">
                            <option value="fa-utensils">🍽️ Utensils</option>
                            <option value="fa-hamburger">🍔 Hamburger</option>
                            <option value="fa-pizza-slice">🍕 Pizza</option>
                            <option value="fa-coffee">☕ Coffee</option>
                            <option value="fa-cocktail">🍸 Cocktail</option>
                            <option value="fa-smoking">🚬 Tobacco</option>
                            <option value="fa-cookie-bite">🍪 Cookie</option>
                            <option value="fa-bolt">⚡ Bolt</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Minimum Order Display</label>
                        <input type="text" id="bannerMinOrder" value="Min Order: ₹250">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px;">
                            <span>Banner Active</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="bannerActive" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </label>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn-primary" onclick="saveBannerSettings()" style="width: auto; padding: 14px 40px;">
                        <i class="fas fa-save"></i> Save Banner Settings
                    </button>
                    
                    <button class="btn-secondary" onclick="resetBannerPreview()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
        </section>

        <!-- ORDERS MANAGEMENT (FIXED) -->
        <section id="orders" class="section">
            <h1 class="header-title">Manage Orders</h1>
            
            <!-- Filter Bar -->
            <div class="filter-bar">
                <select class="filter-select" id="statusFilter" onchange="filterOrdersByStatus()">
                    <option value="all">All Orders</option>
                    <option value="Placed">Placed</option>
                    <option value="Preparing">Preparing</option>
                    <option value="Out for Delivery">Out for Delivery</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                
                <select class="filter-select" id="paymentFilter" onchange="filterOrdersByPayment()">
                    <option value="all">All Payments</option>
                    <option value="COD">Cash on Delivery</option>
                    <option value="Card">Card/UPI</option>
                </select>
                
                <input type="text" class="search-input" id="searchOrder" placeholder="Search by order ID or customer..." onkeyup="searchOrders()">
                
                <button class="btn-secondary" onclick="refreshOrders()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody"></tbody>
                </table>
                <div id="noOrdersMessage" style="text-align: center; padding: 40px; color: var(--light-text); display: none;">
                    No orders found
                </div>
            </div>
        </section>

        <!-- MENU -->
        <section id="menu" class="section">
            <div class="action-bar">
                <h1 class="header-title" style="margin: 0;">Menu Items</h1>
                <button class="btn-add" onclick="openModal('dishModal')">
                    <i class="fas fa-plus"></i> Add Dish
                </button>
            </div>
            
            <div class="menu-grid" id="dishesGrid"></div>
        </section>

        <!-- RESTAURANTS -->
        <section id="restaurants" class="section">
            <div class="action-bar">
                <h1 class="header-title" style="margin: 0;">Restaurants</h1>
                <button class="btn-add" onclick="openModal('restModal')">
                    <i class="fas fa-plus"></i> Add Restaurant
                </button>
            </div>
            
            <div class="menu-grid" id="restaurantsGrid"></div>
        </section>

    </main>

    <!-- ADD DISH MODAL -->
    <div class="modal" id="dishModal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('dishModal')">&times;</button>
            <h2 style="margin-bottom: 25px;">Add New Dish</h2>
            
            <div class="form-group">
                <label>Dish Name</label>
                <input type="text" id="dishName" placeholder="e.g., Butter Chicken">
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <select id="dishCategory">
                    <option value="food">Food</option>
                    <option value="beverage">Beverage</option>
                    <option value="snack">Snack</option>
                    <option value="tobacco">Tobacco</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Price (₹)</label>
                <input type="number" id="dishPrice" placeholder="299">
            </div>
            
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" id="dishImage" placeholder="https://example.com/image.jpg">
            </div>
            
            <button class="btn-primary" onclick="saveDish()">Save Dish</button>
        </div>
    </div>

    <!-- ADD RESTAURANT MODAL -->
    <div class="modal" id="restModal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('restModal')">&times;</button>
            <h2 style="margin-bottom: 25px;">Add Restaurant</h2>
            
            <div class="form-group">
                <label>Restaurant Name</label>
                <input type="text" id="restName" placeholder="e.g., Spice Garden">
            </div>
            
            <div class="form-group">
                <label>Rating (1-5)</label>
                <input type="number" id="restRating" min="1" max="5" step="0.1" placeholder="4.5">
            </div>
            
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" id="restImage" placeholder="https://example.com/image.jpg">
            </div>
            
            <button class="btn-primary" onclick="saveRestaurant()">Save Restaurant</button>
        </div>
    </div>

    <!-- ORDER DETAIL MODAL -->
    <div class="modal" id="orderDetailModal">
        <div class="modal-content" style="max-width: 600px;">
            <button class="close-modal" onclick="closeModal('orderDetailModal')">&times;</button>
            <h2 style="margin-bottom: 25px;">Order Details</h2>
            <div id="orderDetailContent"></div>
        </div>
    </div>

    <!-- Firebase -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>

    <script>
        // ==========================================
        // FIREBASE CONFIGURATION
        // ==========================================
        const firebaseConfig = <?php echo json_encode($firebaseConfig); ?>;
        
        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();
        const db = firebase.database();

        // ==========================================
        // AUTHENTICATION
        // ==========================================
        function login() {
            const email = document.getElementById('adminEmail').value;
            const password = document.getElementById('adminPassword').value;
            const errorEl = document.getElementById('authError');
            
            auth.signInWithEmailAndPassword(email, password)
                .then(() => {
                    document.getElementById('authOverlay').style.display = 'none';
                })
                .catch(err => {
                    errorEl.textContent = err.message;
                    errorEl.style.display = 'block';
                });
        }

        function logout() {
            auth.signOut();
        }

        auth.onAuthStateChanged(user => {
            if(user) {
                document.getElementById('authOverlay').style.display = 'none';
                initDashboard();
                loadBannerSettings();
            } else {
                document.getElementById('authOverlay').style.display = 'flex';
            }
        });

        // ==========================================
        // NAVIGATION
        // ==========================================
        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            
            document.getElementById(sectionId).classList.add('active');
            event.currentTarget.classList.add('active');
            
            if(sectionId === 'orders') {
                loadOrders();
            }
            
            if(window.innerWidth <= 768) toggleSidebar();
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        // ==========================================
        // DASHBOARD
        // ==========================================
        function initDashboard() {
            loadStats();
            loadOrders();
            loadDishes();
            loadRestaurants();
        }

        function loadStats() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            db.ref('orders').on('value', snap => {
                let revenue = 0;
                let orders = 0;
                let pending = 0;
                let todayOrders = 0;
                let codOrders = 0;
                let onlineOrders = 0;
                
                snap.forEach(child => {
                    const order = child.val();
                    const orderTotal = parseFloat(order.total || 0);
                    revenue += orderTotal;
                    orders++;
                    
                    if(order.status === 'Placed' || order.status === 'Preparing') {
                        pending++;
                    }
                    
                    if(order.createdAt && order.createdAt >= today.getTime()) {
                        todayOrders++;
                    }
                    
                    if(order.paymentMethod && order.paymentMethod.includes('Cash')) {
                        codOrders++;
                    } else {
                        onlineOrders++;
                    }
                });
                
                document.getElementById('stat-revenue').textContent = '₹' + revenue.toLocaleString();
                document.getElementById('stat-orders').textContent = orders;
                document.getElementById('stat-pending').textContent = pending;
                document.getElementById('stat-today').textContent = todayOrders;
                document.getElementById('stat-cod').textContent = codOrders;
                document.getElementById('stat-online').textContent = onlineOrders;
            });

            db.ref('dishes').on('value', snap => {
                document.getElementById('stat-dishes').textContent = snap.numChildren();
            });

            db.ref('restaurants').on('value', snap => {
                document.getElementById('stat-restaurants').textContent = snap.numChildren();
            });
        }

        // ==========================================
        // HERO BANNER
        // ==========================================
        function setupBannerListeners() {
            const inputs = [
                'bannerHeadline', 'bannerSubheadline', 'bannerColorStart', 
                'bannerColorEnd', 'bannerIcon', 'bannerMinOrder'
            ];
            
            inputs.forEach(id => {
                const element = document.getElementById(id);
                if(element) {
                    element.addEventListener('input', updateBannerPreview);
                    element.addEventListener('change', updateBannerPreview);
                }
            });
            
            document.getElementById('bannerColorStartText').addEventListener('input', function(e) {
                document.getElementById('bannerColorStart').value = e.target.value;
                updateBannerPreview();
            });
            
            document.getElementById('bannerColorEndText').addEventListener('input', function(e) {
                document.getElementById('bannerColorEnd').value = e.target.value;
                updateBannerPreview();
            });
        }

        function updateBannerPreview() {
            const headline = document.getElementById('bannerHeadline').value || 'Hungry? We\'re Open!';
            const subheadline = document.getElementById('bannerSubheadline').value || 'Serving You from 9 PM to 4 PM';
            const colorStart = document.getElementById('bannerColorStart').value || '#2d3436';
            const colorEnd = document.getElementById('bannerColorEnd').value || '#636e72';
            const iconClass = document.getElementById('bannerIcon').value || 'fa-utensils';
            const minOrder = document.getElementById('bannerMinOrder').value || 'Min Order: ₹250';
            
            document.getElementById('previewHeadline').textContent = headline;
            document.getElementById('previewSubheadline').textContent = subheadline;
            document.getElementById('previewMinOrder').textContent = minOrder;
            document.getElementById('previewIcon').className = `fas ${iconClass}`;
            document.getElementById('bannerPreview').style.background = `linear-gradient(135deg, ${colorStart} 0%, ${colorEnd} 100%)`;
            
            document.getElementById('bannerColorStartText').value = colorStart;
            document.getElementById('bannerColorEndText').value = colorEnd;
        }

        function resetBannerPreview() {
            document.getElementById('bannerHeadline').value = 'Hungry? We\'re Open!';
            document.getElementById('bannerSubheadline').value = 'Serving You from 9 PM to 4 PM';
            document.getElementById('bannerColorStart').value = '#2d3436';
            document.getElementById('bannerColorEnd').value = '#636e72';
            document.getElementById('bannerIcon').value = 'fa-utensils';
            document.getElementById('bannerMinOrder').value = 'Min Order: ₹250';
            document.getElementById('bannerActive').checked = true;
            
            updateBannerPreview();
        }

        function saveBannerSettings() {
            const bannerSettings = {
                headline: document.getElementById('bannerHeadline').value,
                subheadline: document.getElementById('bannerSubheadline').value,
                colorStart: document.getElementById('bannerColorStart').value,
                colorEnd: document.getElementById('bannerColorEnd').value,
                icon: document.getElementById('bannerIcon').value,
                minOrderDisplay: document.getElementById('bannerMinOrder').value,
                active: document.getElementById('bannerActive').checked,
                updatedAt: Date.now(),
                updatedBy: auth.currentUser?.email || 'admin'
            };
            
            db.ref('settings/banner').set(bannerSettings)
                .then(() => {
                    showToast('Banner settings saved successfully!', 'success');
                })
                .catch(err => {
                    showToast('Error saving banner: ' + err.message, 'error');
                });
        }

        function loadBannerSettings() {
            db.ref('settings/banner').once('value', snap => {
                if(snap.exists()) {
                    const settings = snap.val();
                    
                    document.getElementById('bannerHeadline').value = settings.headline || 'Hungry? We\'re Open!';
                    document.getElementById('bannerSubheadline').value = settings.subheadline || 'Serving You from 9 PM to 4 PM';
                    document.getElementById('bannerColorStart').value = settings.colorStart || '#2d3436';
                    document.getElementById('bannerColorEnd').value = settings.colorEnd || '#636e72';
                    document.getElementById('bannerIcon').value = settings.icon || 'fa-utensils';
                    document.getElementById('bannerMinOrder').value = settings.minOrderDisplay || 'Min Order: ₹250';
                    document.getElementById('bannerActive').checked = settings.active !== false;
                    
                    updateBannerPreview();
                }
            });
        }

        // ==========================================
        // ORDERS MANAGEMENT (FIXED VERSION)
        // ==========================================
        let allOrders = [];
        let filteredOrders = [];

        function loadOrders() {
            const tbody = document.getElementById('ordersTableBody');
            const noOrdersMsg = document.getElementById('noOrdersMessage');
            
            db.ref('orders').orderByChild('createdAt').on('value', snap => {
                allOrders = [];
                
                snap.forEach(child => {
                    const order = { id: child.key, ...child.val() };
                    allOrders.push(order);
                });
                
                // Sort by date (newest first)
                allOrders.sort((a, b) => (b.createdAt || 0) - (a.createdAt || 0));
                
                filteredOrders = [...allOrders];
                renderOrdersTable();
                
                // Update recent orders preview
                updateRecentOrdersPreview();
            });
        }

        function renderOrdersTable() {
            const tbody = document.getElementById('ordersTableBody');
            const noOrdersMsg = document.getElementById('noOrdersMessage');
            
            if (filteredOrders.length === 0) {
                tbody.innerHTML = '';
                noOrdersMsg.style.display = 'block';
                return;
            }
            
            noOrdersMsg.style.display = 'none';
            
            tbody.innerHTML = filteredOrders.map(order => {
                const items = order.items || [];
                const itemsPreview = items.map(i => `${i.quantity}x ${i.name}`).join(', ');
                const truncatedItems = itemsPreview.length > 50 ? itemsPreview.substring(0, 50) + '...' : itemsPreview;
                
                const customerName = order.userName || 'Guest';
                const customerPhone = order.phone || 'No phone';
                
                const paymentMethod = order.paymentMethod || 'Cash on Delivery';
                const paymentStatus = order.paymentStatus || 'Pending';
                const paymentClass = paymentStatus === 'Paid' ? 'payment-paid' : 'payment-pending';
                
                const statusClass = order.status ? `status-${order.status.toLowerCase().replace(/ /g, '')}` : 'status-placed';
                
                return `
                    <tr>
                        <td><code>#${order.id.slice(-6)}</code><br><small style="color: var(--light-text);">${new Date(order.createdAt).toLocaleString()}</small></td>
                        <td>
                            <strong>${customerName}</strong><br>
                            <small style="color: var(--light-text);">${customerPhone}</small>
                        </td>
                        <td title="${itemsPreview}">
                            ${truncatedItems || 'No items'}
                        </td>
                        <td style="font-weight: 700;">₹${order.total || 0}</td>
                        <td>
                            <span class="payment-badge ${paymentClass}">${paymentStatus}</span><br>
                            <small style="color: var(--light-text);">${paymentMethod}</small>
                        </td>
                        <td>
                            <select class="status-select" onchange="updateOrderStatus('${order.id}', this.value)" data-order-id="${order.id}">
                                <option value="Placed" ${order.status === 'Placed' ? 'selected' : ''}>Placed</option>
                                <option value="Preparing" ${order.status === 'Preparing' ? 'selected' : ''}>Preparing</option>
                                <option value="Out for Delivery" ${order.status === 'Out for Delivery' ? 'selected' : ''}>Out for Delivery</option>
                                <option value="Delivered" ${order.status === 'Delivered' ? 'selected' : ''}>Delivered</option>
                                <option value="Cancelled" ${order.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-secondary" style="padding: 8px 12px;" onclick="viewOrderDetails('${order.id}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-secondary" style="padding: 8px 12px;" onclick="printOrder('${order.id}')">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function updateOrderStatus(orderId, newStatus) {
            // Show loading state on the select
            const select = document.querySelector(`select[data-order-id="${orderId}"]`);
            const originalValue = select.value;
            select.disabled = true;
            
            // Get order details for notification
            db.ref(`orders/${orderId}`).once('value', snap => {
                const order = snap.val();
                
                if (!order) {
                    showToast('Order not found', 'error');
                    select.disabled = false;
                    return;
                }
                
                // Update status in database
                db.ref(`orders/${orderId}`).update({ 
                    status: newStatus,
                    statusUpdatedAt: Date.now(),
                    updatedAt: Date.now()
                })
                .then(() => {
                    showToast(`Order #${orderId.slice(-6)} status updated to ${newStatus}`, 'success');
                    
                    // Add notification for user if they exist
                    if (order.userId) {
                        db.ref(`notifications/${order.userId}`).push({
                            title: 'Order Status Updated',
                            body: `Your order #${orderId.slice(-6)} is now ${newStatus}`,
                            timestamp: Date.now(),
                            read: false,
                            type: 'order_update',
                            orderId: orderId
                        });
                    }
                    
                    // If status is Out for Delivery, add delivery tracking
                    if (newStatus === 'Out for Delivery') {
                        db.ref(`orders/${orderId}`).update({
                            outForDeliveryAt: Date.now(),
                            estimatedDeliveryTime: Date.now() + (30 * 60 * 1000) // 30 minutes
                        });
                    }
                    
                    // If status is Delivered, record delivery time
                    if (newStatus === 'Delivered') {
                        db.ref(`orders/${orderId}`).update({
                            deliveredAt: Date.now()
                        });
                    }
                })
                .catch(error => {
                    showToast('Error updating status: ' + error.message, 'error');
                    select.value = originalValue; // Revert on error
                })
                .finally(() => {
                    select.disabled = false;
                });
            });
        }

        function filterOrdersByStatus() {
            const statusFilter = document.getElementById('statusFilter').value;
            const paymentFilter = document.getElementById('paymentFilter').value;
            
            filteredOrders = allOrders.filter(order => {
                // Status filter
                if (statusFilter !== 'all' && order.status !== statusFilter) {
                    return false;
                }
                
                // Payment filter
                if (paymentFilter !== 'all') {
                    if (paymentFilter === 'COD' && !order.paymentMethod?.includes('Cash')) {
                        return false;
                    }
                    if (paymentFilter === 'Card' && order.paymentMethod?.includes('Cash')) {
                        return false;
                    }
                }
                
                return true;
            });
            
            renderOrdersTable();
        }

        function filterOrdersByPayment() {
            filterOrdersByStatus(); // Reuse the same function
        }

        function searchOrders() {
            const searchTerm = document.getElementById('searchOrder').value.toLowerCase().trim();
            
            if (!searchTerm) {
                filteredOrders = [...allOrders];
            } else {
                filteredOrders = allOrders.filter(order => {
                    const orderId = order.id.slice(-6).toLowerCase();
                    const customerName = (order.userName || '').toLowerCase();
                    const customerPhone = (order.phone || '').toLowerCase();
                    
                    return orderId.includes(searchTerm) || 
                           customerName.includes(searchTerm) || 
                           customerPhone.includes(searchTerm);
                });
            }
            
            renderOrdersTable();
        }

        function refreshOrders() {
            loadOrders();
            showToast('Orders refreshed', 'success');
        }

        function viewOrderDetails(orderId) {
            const order = allOrders.find(o => o.id === orderId);
            
            if (!order) {
                showToast('Order not found', 'error');
                return;
            }
            
            const itemsList = (order.items || []).map(item => `
                <tr>
                    <td>${item.quantity}x ${item.name}</td>
                    <td style="text-align: right;">₹${item.price}</td>
                    <td style="text-align: right;">₹${item.price * item.quantity}</td>
                </tr>
            `).join('');
            
            const content = `
                <div style="margin-bottom: 20px;">
                    <h3>Order #${order.id.slice(-6)}</h3>
                    <p><strong>Placed:</strong> ${new Date(order.createdAt).toLocaleString()}</p>
                    <p><strong>Status:</strong> <span class="status-badge status-${order.status?.toLowerCase().replace(/ /g, '')}">${order.status}</span></p>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4>Customer Details</h4>
                    <p><strong>Name:</strong> ${order.userName || 'Guest'}</p>
                    <p><strong>Email:</strong> ${order.userEmail || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${order.phone || 'N/A'}</p>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4>Delivery Address</h4>
                    <p>${order.address || 'No address provided'}</p>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4>Order Items</h4>
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsList}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align: right;"><strong>Subtotal:</strong></td>
                                <td style="text-align: right;">₹${order.subtotal || 0}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right;"><strong>Delivery Fee:</strong></td>
                                <td style="text-align: right;">₹${order.deliveryFee || 40}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                                <td style="text-align: right;"><strong>₹${order.total || 0}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h4>Payment Details</h4>
                    <p><strong>Method:</strong> ${order.paymentMethod || 'Cash on Delivery'}</p>
                    <p><strong>Status:</strong> <span class="payment-badge ${order.paymentStatus === 'Paid' ? 'payment-paid' : 'payment-pending'}">${order.paymentStatus || 'Pending'}</span></p>
                </div>
                
                ${order.razorpay_payment_id ? `
                <div style="margin-bottom: 20px;">
                    <h4>Razorpay Details</h4>
                    <p><strong>Payment ID:</strong> ${order.razorpay_payment_id}</p>
                    <p><strong>Order ID:</strong> ${order.razorpay_order_id}</p>
                </div>
                ` : ''}
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button class="btn-primary" onclick="updateOrderStatus('${order.id}', prompt('Enter new status:', '${order.status}'))" style="flex: 1;">
                        Update Status
                    </button>
                    <button class="btn-secondary" onclick="closeModal('orderDetailModal')" style="flex: 1;">
                        Close
                    </button>
                </div>
            `;
            
            document.getElementById('orderDetailContent').innerHTML = content;
            openModal('orderDetailModal');
        }

        function printOrder(orderId) {
            const order = allOrders.find(o => o.id === orderId);
            
            if (!order) {
                showToast('Order not found', 'error');
                return;
            }
            
            const printWindow = window.open('', '_blank');
            const itemsList = (order.items || []).map(item => `
                <tr>
                    <td>${item.quantity}x ${item.name}</td>
                    <td>₹${item.price}</td>
                    <td>₹${item.price * item.quantity}</td>
                </tr>
            `).join('');
            
            printWindow.document.write(`
                <html>
                <head>
                    <title>Order #${order.id.slice(-6)}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .section { margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                        th { background: #f5f5f5; }
                        .total { font-weight: bold; font-size: 1.2em; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Kanoja</h1>
                        <h2>Order Invoice</h2>
                        <p>Order #${order.id.slice(-6)}</p>
                        <p>Date: ${new Date(order.createdAt).toLocaleString()}</p>
                    </div>
                    
                    <div class="section">
                        <h3>Customer Details</h3>
                        <p><strong>Name:</strong> ${order.userName || 'Guest'}</p>
                        <p><strong>Phone:</strong> ${order.phone || 'N/A'}</p>
                        <p><strong>Address:</strong> ${order.address || 'N/A'}</p>
                    </div>
                    
                    <div class="section">
                        <h3>Order Items</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsList}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="section">
                        <p><strong>Subtotal:</strong> ₹${order.subtotal || 0}</p>
                        <p><strong>Delivery Fee:</strong> ₹${order.deliveryFee || 40}</p>
                        <p class="total"><strong>Total:</strong> ₹${order.total || 0}</p>
                    </div>
                    
                    <div class="section">
                        <h3>Payment Details</h3>
                        <p><strong>Method:</strong> ${order.paymentMethod || 'Cash on Delivery'}</p>
                        <p><strong>Status:</strong> ${order.paymentStatus || 'Pending'}</p>
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.print();
        }

        function updateRecentOrdersPreview() {
            const tbody = document.getElementById('recentOrdersPreview');
            if (!tbody) return;
            
            const recentOrders = allOrders.slice(0, 5);
            
            tbody.innerHTML = recentOrders.map(order => `
                <tr onclick="viewOrderDetails('${order.id}')" style="cursor: pointer;">
                    <td><code>#${order.id.slice(-6)}</code></td>
                    <td>${order.userName || 'Guest'}</td>
                    <td>₹${order.total || 0}</td>
                    <td><span class="status-badge status-${order.status?.toLowerCase().replace(/ /g, '')}">${order.status}</span></td>
                    <td>${new Date(order.createdAt).toLocaleTimeString()}</td>
                </tr>
            `).join('');
        }

        // ==========================================
        // MENU MANAGEMENT
        // ==========================================
        function loadDishes() {
            db.ref('dishes').on('value', snap => {
                const grid = document.getElementById('dishesGrid');
                grid.innerHTML = '';
                
                snap.forEach(child => {
                    const dish = child.val();
                    const card = document.createElement('div');
                    card.className = 'admin-card';
                    card.innerHTML = `
                        <img src="${dish.imageUrl}" alt="${dish.name}" onerror="this.src='https://via.placeholder.com/300'">
                        <button class="delete-btn" onclick="deleteItem('dishes', '${child.key}')">
                            <i class="fas fa-trash"></i>
                        </button>
                        <div class="admin-card-body">
                            <span class="category-badge cat-${dish.category}">${dish.category}</span>
                            <h4>${dish.name}</h4>
                            <span class="price">₹${dish.price}</span>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            });
        }

        function saveDish() {
            const name = document.getElementById('dishName').value;
            const category = document.getElementById('dishCategory').value;
            const price = parseFloat(document.getElementById('dishPrice').value);
            const image = document.getElementById('dishImage').value;

            if(!name || !price || !image) {
                showToast('Please fill all fields', 'warning');
                return;
            }

            db.ref('dishes').push({
                name,
                category,
                price,
                imageUrl: image,
                createdAt: Date.now()
            }).then(() => {
                closeModal('dishModal');
                clearDishForm();
                showToast('Dish added successfully', 'success');
            });
        }

        function clearDishForm() {
            document.getElementById('dishName').value = '';
            document.getElementById('dishPrice').value = '';
            document.getElementById('dishImage').value = '';
        }

        // ==========================================
        // RESTAURANT MANAGEMENT
        // ==========================================
        function loadRestaurants() {
            db.ref('restaurants').on('value', snap => {
                const grid = document.getElementById('restaurantsGrid');
                grid.innerHTML = '';
                
                snap.forEach(child => {
                    const rest = child.val();
                    const card = document.createElement('div');
                    card.className = 'admin-card';
                    card.innerHTML = `
                        <img src="${rest.imageUrl}" alt="${rest.name}" onerror="this.src='https://via.placeholder.com/300'">
                        <button class="delete-btn" onclick="deleteItem('restaurants', '${child.key}')">
                            <i class="fas fa-trash"></i>
                        </button>
                        <div class="admin-card-body">
                            <h4>${rest.name}</h4>
                            <p><i class="fas fa-star" style="color: #f1c40f;"></i> ${rest.rating}</p>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            });
        }

        function saveRestaurant() {
            const name = document.getElementById('restName').value;
            const rating = document.getElementById('restRating').value;
            const image = document.getElementById('restImage').value;

            if(!name || !rating || !image) {
                showToast('Please fill all fields', 'warning');
                return;
            }

            db.ref('restaurants').push({
                name,
                rating: parseFloat(rating),
                imageUrl: image,
                createdAt: Date.now()
            }).then(() => {
                closeModal('restModal');
                clearRestForm();
                showToast('Restaurant added successfully', 'success');
            });
        }

        function clearRestForm() {
            document.getElementById('restName').value = '';
            document.getElementById('restRating').value = '';
            document.getElementById('restImage').value = '';
        }

        // ==========================================
        // UTILITIES
        // ==========================================
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function deleteItem(collection, id) {
            if(confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                db.ref(`${collection}/${id}`).remove()
                    .then(() => showToast('Item deleted successfully', 'success'))
                    .catch(err => showToast('Error: ' + err.message, 'error'));
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Close modal on outside click
        window.onclick = (e) => {
            if(e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            setupBannerListeners();
        });
    </script>
</body>
</html>
