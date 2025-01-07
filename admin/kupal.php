<?php
// Start the session
session_start();
include "config.php";

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: adminlogin.php");
    exit;
}

// Logout functionality
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("location: adminlogin.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary-color: #4f46e5;
            --primary-light: #818cf8;
            --secondary-color: #1e293b;
            --accent-color: #06b6d4;
            --text-color: #f8fafc;
            --background-light: #f1f5f9;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --transition-speed: 0.3s;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--background-light);
        }

        .sidebar {
            width: 280px;
            background: #2c3e50;
            color: var(--text-color);
            padding: 24px;
            transition: all var(--transition-speed) ease;
            position: relative;
            z-index: 100;
        }

        .sidebar.collapsed {
            width: 88px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-top: -10px;
            margin-bottom: 30px;
            padding: 8px;
            border-radius: 12px;
            transition: background-color var(--transition-speed);
        }

        .logo-container:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .logo-container img {
            width: 40px;
            height: 40px;
            margin-right: 16px;
            border-radius: 8px;
        }

        .logo-container .website-name {
            font-size: 1.2rem;
            font-weight: 600;
            flex-shrink: 0;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .sidebar.collapsed .website-name {
            display: none;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 8px;
        }

        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all var(--transition-speed);
            position: relative;
            overflow: hidden;
        }

        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .nav-links a.active {
            background-color: var(--primary-light);
        }

        .nav-links i {
            width: 24px;
            margin-right: 16px;
            font-size: 1.2rem;
        }

        .sidebar.collapsed .nav-links span {
            display: none;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .toggle-btn {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--secondary-color);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all var(--transition-speed);
        }

        .toggle-btn:hover {
            background-color: var(--background-light);
        }

        .admin-profile {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all var(--transition-speed);
        }

        .admin-profile:hover {
            background-color: var(--background-light);
        }

        .admin-profile img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            margin-right: 12px;
            object-fit: cover;
        }

        .admin-profile span {
            font-weight: 500;
            color: var(--secondary-color);
        }

        .dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background-color: white;
            box-shadow: var(--card-shadow);
            border-radius: 12px;
            padding: 8px;
            min-width: 160px;
            display: none;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown.active {
            display: block;
        }

        .dropdown a {
            display: block;
            padding: 8px 16px;
            color: var(--secondary-color);
            text-decoration: none;
            border-radius: 8px;
            transition: all var(--transition-speed);
        }

        .dropdown a:hover {
            background-color: var(--background-light);
        }

        .content-section {
            padding: 32px;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .toggle-btnn {
            background: none;
            margin-left: 5px;
            border: none;
            cursor: pointer;
            font-size: 20px;
        }

        .toggle-btnn:focus {
            outline: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .content-section.active {
            display: block;
        }

        .section-title {
            font-size: 1.875rem;
            font-weight: 600;
            margin-bottom: 24px;
            color: var(--secondary-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            background-color: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
        }

        .stat-card h3 {
            color: var(--secondary-color);
            font-size: 1.25rem;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .card {
            background-color: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            transition: transform var(--transition-speed);
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card h3 {
            color: var(--secondary-color);
            font-size: 1.25rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card h3 i {
            color: var(--primary-color);
        }

        .card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Styles for the booking management section */
        #booking {
            padding: 25px;
        }

        /* Table Container */
        .table-container {
            overflow-x: auto;
            margin-top: 10px;
            width: 100%;
            max-width: calc(100% - 30px);
            cursor: pointer;
            /* Subtract the sidebar width (280px) and some margin */
        }

        /* Table Styling */
        .bookingTable {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
            /* Ensure that the table has a minimum width to require scrolling */
            table-layout: fixed;
        }

        .bookingTable th {
            padding: 5px 9px;
            text-align: center;
            font-size: .90em;
            border: 1px solid #ddd;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bookingTable td {
            padding: 5px;
            text-align: center;
            font-size: .90em;
            border: 1px solid #ddd;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;

        }

        .bookingTable th {
            background-color: var(--primary-color);
            color: white;
        }

        .bookingTable tbody tr:hover {
            background-color: #f0f0f0;
        }

        .action-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .btn-edit,
        .btn-delete {
            padding: 5px 10px;
            border: none;
            margin: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-edit {
            background-color: #007bff;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-edit:hover {
            color: #2563eb;
            background: #dbeafe;
        }

        .btn-delete:hover {
            color: #dc2626;
            background: #fee2e2;
        }

        .service-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            gap: 20px;
        }

        .service-containerr {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px;
            gap: 20px;
        }

        .add-booking-btn {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .add-booking-btn:hover {
            background-color: var(--primary-light);
        }

        .search-form {
            display: flex;
            align-items: center;
        }

        .search-form input[type="search"] {
            padding: 8px;
            font-size: 16px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        .search-form button {
            padding: 8px 15px;
            font-size: 16px;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 20px;
            background-color: var(--primary-color);
            border-radius: 0 5px 5px 0;
        }

        .search-form button:hover {
            background-color: var(--primary-light);
        }

        .pagination {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-right: 28px;
            gap: 1rem;
        }

        .pagination button {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pagination button:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .status {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status.active {
            background-color: #dcfce7;
            color: #166534;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            z-index: 10000;
            align-items: center;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            color: #1e293b;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: .1rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        /* label {
            display: block;
            margin-bottom: 0.2rem;
            color: #475569;
            font-weight: 500;
        } */

        input,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #1e293b;
            transition: border-color 0.2s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .submit-btn {
            background-color: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            margin-top: 1rem;
            transition: background-color 0.2s;
        }

        .submit-btn:hover {
            background-color: #1d4ed8;
        }

        .momdal {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
        }

        .momdal-content {
            background-color: #fff;
            margin: 1% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .momdal .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .mdal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .mdal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 300px;
        }

        .mdal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .btn-secondary {
            background-color: gray;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-danger {
            background-color: red;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-secondary:hover,
        .btn-danger:hover {
            opacity: 0.9;
        }

        .service-thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .mdaal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
        }

        .mdaal-content {
            background-color: #fff;
            margin: 1% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .mdaal .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .mdaal img {
            display: block;
            margin-bottom: 10px;
        }

        .modalll {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            /* Center the modal */
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modalll.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modalll-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modalll.active .modalll-content {
            transform: translateY(0);
        }

        span.close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        span.close:hover {
            color: #333;
        }

        .modalll-content.form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="Images/log.png" alt="Logo">
            <span class="website-name">EL PERGENTINA</span>
        </div>
        <ul class="nav-links">
            <li><a href="#dashboard" class="active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="#booking"><i class="fas fa-calendar-check"></i><span>Accommodation</span></a></li>
            <li><a href="#customers"><i class="fas fa-users"></i><span>Users</span></a></li>
            <li><a href="#services"><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
            <li><a href="#amenities"><i class="fas fa-spa"></i><span>Amenities</span></a></li>
            <li><a href="#reports"><i class="fas fa-chart-bar"></i><span>Reports</span></a></li>
            <li><a href="#announcements"><i class="fas fa-bullhorn"></i><span>Announcements</span></a></li>
            <li><a href="#activity"><i class="fas fa-running"></i><span>Activity</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="toggle-btn">
                <i class="fas fa-bars"></i>
            </div>
            <H3 style="margin-left:10px;">EL PERGENTINA BEACH HOLIDAY RESORT-MANAGER PANEL</H3>
            <div class="admin-profile">
                <img src="Images/profile.jpg" alt="Admin">

                <span>Manager</span>
                <button id="toggleDropdown" class="toggle-btnn">
                    <i class="fas fa-chevron-down" id="arrowIcon"></i>
                </button>
                <div class="dropdown">
                    <a href="#profile"><i class="fas fa-user"></i> Profile</a>
                    <a href="?logout=true" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
        <?php
        require_once "config.php";

        // Total Users
        $sql_users = "SELECT COUNT(*) AS total_users FROM usertable";
        $result_users = mysqli_query($link, $sql_users);
        if ($result_users) {
            $row_users = mysqli_fetch_assoc($result_users);
            $total_users = $row_users['total_users'];
        } else {
            $total_users = "Error: " . mysqli_error($link);
        }

        // Total Bookings
        $sql_bookings = "SELECT COUNT(*) AS total_bookings FROM bookings";
        $result_bookings = mysqli_query($link, $sql_bookings);
        if ($result_bookings) {
            $row_bookings = mysqli_fetch_assoc($result_bookings);
            $total_bookings = $row_bookings['total_bookings'];
        } else {
            $total_bookings = "Error: " . mysqli_error($link);
        }

        // Total Services
        $sql_services = "SELECT COUNT(*) AS total_services FROM services";
        $result_services = mysqli_query($link, $sql_services);
        if ($result_services) {
            $row_services = mysqli_fetch_assoc($result_services);
            $total_services = $row_services['total_services'];
        } else {
            $total_services = "Error: " . mysqli_error($link);
        }
        $sql_revenue = "SELECT SUM(total_price) AS total_revenue FROM bookings";
        $result_revenue = mysqli_query($link, $sql_revenue);
        if ($result_revenue) {
            $row_revenue = mysqli_fetch_assoc($result_revenue);
            $total_revenue = $row_revenue['total_revenue'] ?: 0; // Default to 0 if NULL
        } else {
            $total_revenue = "Error: " . mysqli_error($link);
        }

        $sql_pending_bookings = "SELECT COUNT(*) AS total_pending FROM bookings WHERE status = 'Pending'";
        $result_pending_bookings = mysqli_query($link, $sql_pending_bookings);

        if ($result_pending_bookings) {
            $row_pending_bookings = mysqli_fetch_assoc($result_pending_bookings);
            $total_pending = $row_pending_bookings['total_pending'];
        } else {
            $total_pending = "Error: " . mysqli_error($link);
        }
        ?>


        <div id="dashboard" class="content-section active">
            <h2 class="section-title">Dashboard Overview</h2>
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <div class="value">
                        <?php echo htmlspecialchars($total_bookings); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>Users</h3>
                    <div class="value">
                        <?php echo htmlspecialchars($total_users); ?>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>Revenue</h3>
                    <div class="value">
                        <?php echo htmlspecialchars($total_revenue); ?>

                    </div>
                </div>

                <div class="stat-card">
                    <h3>Active Services</h3>
                    <div class="value">
                        <?php echo htmlspecialchars($total_services); ?>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Inactive Services</h3>
                    <div class="value">
                        <?php echo htmlspecialchars($total_services); ?>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Pending Accommodation</h3>
                    <div class="value">
                        <?php echo htmlspecialchars($total_pending); ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="booking" class="content-section">
            <h2 class="section-title">Accommodation Management</h2>

            <?php

            require_once "config.php";
            if (isset($_POST['search'])) {
                // Correctly escaping user input and applying htmlspecialchars
                $valueToSearch = htmlspecialchars(mysqli_real_escape_string($link, $_POST['valueToSearch']));
                $query = "SELECT * FROM bookings WHERE full_name LIKE '%" . $valueToSearch . "%'";
                $result = filterRecord($query);
            } else {
                $query = "SELECT * FROM bookings";
                $result = filterRecord($query);
            }

            function filterRecord($query)
            {
                include("config.php");
                $filter_result = mysqli_query($link, $query);  // Use $link for the DB connection
                return $filter_result;
            }

            ?>

            <div class="service-container">
                <a class="add-booking-btn" href="addBooking.php">Add New Booking</a>
                <form action="index.php#booking" method="POST" class="search-form">
                    <input type="search" name="valueToSearch" placeholder="Search name">
                    <button type="submit" class="signupbtn" name="search">Search</button>
                </form>
            </div>

            <!-- Booking Table with Scrollable Bottom -->
            <div class="table-container">
                <table class="bookingTable" id="bookingTable1">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Accommodation</th>
                            <th>Guests</th>
                            <th>CheckIn</th>
                            <th>CheckOut</th>
                            <th>Price</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['accommodation_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['guest']); ?></td>
                                <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['check_out_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                                <td><span class="status active"><?php echo $row['status'] ?></span></td>
                                <td>
                                    <button class="btn-edit"
                                        type="button"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-fullname="<?php echo htmlspecialchars($row['full_name']); ?>"
                                        data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                        data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                        data-type="<?php echo htmlspecialchars($row['accommodation_type']); ?>"
                                        data-guest="<?php echo htmlspecialchars($row['guest']); ?>"
                                        data-checkin="<?php echo htmlspecialchars($row['check_in_date']); ?>"
                                        data-checkout="<?php echo htmlspecialchars($row['check_out_date']); ?>"
                                        data-price="<?php echo htmlspecialchars($row['total_price']); ?>"
                                        data-method="<?php echo htmlspecialchars($row['payment_method']); ?>"
                                        data-paystatus="<?php echo htmlspecialchars($row['payment_status']); ?>"
                                        data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                        onclick="openModal(this)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination1">
                <button id="prevBtn" onclick="previousPage()" disabled>Previous</button>
                <button id="nextBtn" onclick="nextPage()">Next</button>
            </div>
        </div>


        <div id="customers" class="content-section">
            <h2 class="section-title">Users Database</h2>
            <?php

            require_once "config.php";
            if (isset($_POST['searchh'])) {
                // Correctly escaping user input and applying htmlspecialchars
                $valueToSearchh = htmlspecialchars(mysqli_real_escape_string($link, $_POST['valueToSearchh']));
                $queryy = "SELECT * FROM usertable WHERE name LIKE '%" . $valueToSearchh . "%'";
                $resultt = filterRecordd($queryy);
            } else {
                $queryy = "SELECT * FROM usertable";
                $resultt = filterRecordd($queryy);
            }

            function filterRecordd($queryy)
            {
                include("config.php");
                $filter_resultt = mysqli_query($link, $queryy);  // Use $link for the DB connection
                return $filter_resultt;
            }

            ?>
            <div class="service-containerr">
                <form action="index.php#customers" method="POST" class="search-form">
                    <input type="search" name="valueToSearchh" placeholder="Search name">
                    <button type="submit" class="signupbtn" name="searchh">Search</button>
                </form>
            </div>

            <!-- Booking Table with Scrollable Bottom -->
            <div class="table-container">
                <table class="bookingTable" id="bookingTable2">
                    <thead>
                        <tr>
                            <th style="padding:15px 20px;">Name</th>
                            <th style="padding:15px 20px;">Email</th>
                            <th style="padding:15px 20px;">Status</th>
                            <th style="padding:15px 20px;">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($roww = mysqli_fetch_assoc($resultt)) { ?>
                            <tr>
                                <td style="padding:13px 20px;"><?php echo htmlspecialchars($roww['name']); ?></td>
                                <td style="padding:13px 20px;"><?php echo htmlspecialchars($roww['email']); ?></td>
                                <td style="padding:13px 20px;"><?php echo htmlspecialchars($roww['status']); ?></td>
                                <td style="padding:13px 20px;"><?php echo htmlspecialchars($roww['created_at']); ?></td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination2">
                <button id="prevBtn" onclick="previousPage()" disabled>Previous</button>
                <button id="nextBtn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <div id="services" class="content-section">
            <h2 class="section-title">Service Management</h2>
            <?php

            require_once "config.php";
            if (isset($_POST['ssearch'])) {
                // Correctly escaping user input and applying htmlspecialchars
                $vvalueToSearch = htmlspecialchars(mysqli_real_escape_string($link, $_POST['vvalueToSearch']));
                $qquery = "SELECT * FROM services WHERE name LIKE '%" . $vvalueToSearch . "%'";
                $rresult = ffilterRecord($qquery);
            } else {
                $qquery = "SELECT * FROM services";
                $rresult = ffilterRecord($qquery);
            }

            function ffilterRecord($qquery)
            {
                include("config.php");
                $filter_rresult = mysqli_query($link, $qquery);  // Use $link for the DB connection
                return $filter_rresult;
            }

            ?>
            <div class="service-container">
                <button class="add-booking-btn" onclick="openAddModal()">Add New Service
                </button>
                <form action="index.php#services" method="POST" class="search-form">
                    <input type="search" name="vvalueToSearch" placeholder="Search name">
                    <button type="submit" class="signupbtn" name="ssearch">Search</button>
                </form>
            </div>
            <div class="table-container">
                <table class="bookingTable" id="bookingTable3">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Service Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($rresult)) { ?>
                            <tr>
                                <td>
                                    <img src="../Images/<?php echo htmlspecialchars($row['image']); ?>"
                                        alt="<?php echo htmlspecialchars($row['name']); ?>"
                                        class="service-thumbnail">
                                </td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <span class="status <?php echo $row['status'] == 1 ? 'active' : 'inactive'; ?>">
                                        <?php echo $row['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-edit"
                                        type="button"
                                        data-d="<?php echo $row['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                        data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                        data-image="<?php echo htmlspecialchars($row['image']); ?>"
                                        data-status="<?php echo $row['status']; ?>"
                                        onclick="openEditModal(this)">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button onclick="confirmDeletee(<?php echo $row['id']; ?>)" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination3">
                <button id="prevBtn" onclick="previousPage()" disabled>Previous</button>
                <button id="nextBtn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <div id="amenities" class="content-section">
            <h2 class="section-title">Amenities</h2>
            <div class="card">
                <h3><i class="fas fa-building"></i> Facility Amenities</h3>
                <p>Manage and monitor all available amenities and their status.</p>
            </div>
        </div>

        <div id="reports" class="content-section">
            <h2 class="section-title">Analytics & Reports</h2>
            <div class="card">
                <h3><i class="fas fa-chart-line"></i> Performance Metrics</h3>
                <p>Access detailed reports and analytics about your business performance.</p>
            </div>
        </div>

        <div id="announcements" class="content-section">
            <h2 class="section-title">Announcements</h2>
            <?php
            require_once "config.php";
            if (isset($_POST['searc'])) {
                // Correctly escaping user input and applying htmlspecialchars
                $valueToSearc = htmlspecialchars(mysqli_real_escape_string($link, $_POST['valueToSearc']));
                $quer = "SELECT * FROM announcement WHERE name LIKE '%" . $valueToSearc . "%'";
                $resul = filterRecor($quer);
            } else {
                $quer = "SELECT * FROM announcement";
                $resul = filterRecor($quer);
            }

            function filterRecor($quer)
            {
                include("config.php");
                $filter_resul = mysqli_query($link, $quer);  // Use $link for the DB connection
                return $filter_resul;
            }

            ?>
            <div class="service-container">
                <button class="add-booking-btn" onclick="openAddModa()">Add New Announcement
                </button>
            </div>
            <div class="table-container">
                <table class="bookingTable" id="bookingTable4">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ro = mysqli_fetch_assoc($resul)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ro['name']); ?></td>
                                <td><?php echo htmlspecialchars($ro['date']); ?></td>
                                <td><?php echo htmlspecialchars($ro['description']); ?></td>
                                <td>
                                    <button class="btn-edit"
                                        type="button"
                                        data-id="<?php echo $ro['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($ro['name']); ?>"
                                        data-date="<?php echo htmlspecialchars($ro['date']); ?>"
                                        data-description="<?php echo htmlspecialchars($ro['description']); ?>"
                                        onclick="openEditMode(this)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirmDeleted(<?php echo $ro['id']; ?>)" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination" id="pagination4">
                <button id="prevBtn" onclick="previousPage()" disabled>Previous</button>
                <button id="nextBtn" onclick="nextPage()">Next</button>
            </div>

        </div>

        <div id="activity" class="content-section">
            <h2 class="section-title">Activity Center</h2>
            <div class="card">
                <h3><i class="fas fa-history"></i> Recent Activities</h3>
                <p>Monitor and track all system activities and user interactions.</p>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Edit Book Accommodation</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="editServiceForm" action="edit_booking.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <input type="hidden" name="id" id="serviceId">
                        <label for="accommodationType">Accommodation Type</label>
                        <select id="accommodationType" required name="accommodation_type">
                            <option value="" hidden>Select type</option>
                            <option value="Villa Type">Villa Type</option>
                            <option value="Plex Type">Plex Type</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="guests">Number of Guests</label>
                        <input type="number" id="guests" min="1" required name="guest">
                    </div>
                    <div class="form-group">
                        <label for="checkin">Check-in Date</label>
                        <input type="date" id="checkin" required name="checkin">
                    </div>
                    <div class="form-group">
                        <label for="checkout">Check-out Date</label>
                        <input type="date" id="checkout" required name="checkout">
                    </div>
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" required name="fullname">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" required name="email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" required name="phone">
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" readonly value="0" name="price">
                    </div>
                    <div class="form-group">
                        <label for="payment">Payment Method</label>
                        <select id="payment" required name="payment_method">
                            <option value="" hidden>Select payment method</option>
                            <option value="credit">Credit Card</option>
                            <option value="debit">Debit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="gcash">Gcash</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="paystatus">Payment Status</label>
                        <select id="paystatus" required name="payment_status">
                            <option value="" hidden>Select payment status</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">UnPaid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" required name="status">
                            <option value="" hidden>Select payment status</option>
                            <option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Save Booking</button>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="mdal" style="display: none;">
        <div class="mdal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this Item?</p>
            <div class="mdal-actions">
                <button id="cancelBtn" class="btn-secondary">Cancel</button>
                <button id="confirmDeleteBtn" class="btn-danger">Delete</button>
            </div>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="addServiceModal" class="modalll">
        <div class="modalll-content">
            <span class="close" onclick="closeModall()">&times;</span>
            <h2>Add New Service</h2>
            <form id="addServiceForm" action="add_service.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Service Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Service Image</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Add Service</button>
            </form>
        </div>
    </div>

    <!-- Add announcement Modal -->

    <div id="addAnnouncementModal" class="modalll">
        <div class="modalll-content">
            <span class="close" onclick="closeModa()">&times;</span>
            <h2>Add New Announcement</h2>
            <form id="addServiceForm" action="add_announcement.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Announcement Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Add Announcement</button>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="editServiceModal" class="mdaal">
        <div class="mdaal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Service</h2>
            <form id="editServiceForm" action="edit_service.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="serviceID">
                <div class="form-group">
                    <label>Service Name</label>
                    <input type="text" name="name" id="serviceName" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="serviceDescription" required></textarea>
                </div>
                <div class="form-group">
                    <label>Service Image</label>
                    <img id="serviceImagePreview" src="" alt="Service Image" style="width: 100px; height: 70px;">
                    <input type="file" name="image">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="serviceStatus">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Save Service</button>
            </form>
        </div>
    </div>

    <!-- Edit Announcemnet Modal -->
    <div id="editAnnounceModal" class="mdaal">
        <div class="mdaal-content">
            <span class="close" onclick="loseEditModal()">&times;</span>
            <h2>Edit Announcement</h2>
            <form id="editServiceForm" action="edit_announce.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="serviceI">
                <div class="form-group">
                    <label>Announcement Name</label>
                    <input type="text" name="name" id="serviceNamed" required>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" name="date" id="serviceDate" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="serviceDescriptions" required></textarea>
                </div>

                <button type="submit" class="submit-btn">Save Announcement</button>
            </form>
        </div>
    </div>

    <script>
        // First table pagination
        // Reusable pagination function
        function initializePagination(tableId, paginationId, rowsPerPage) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const rows = table.getElementsByTagName('tr');
            const prevBtn = document.querySelector(`#${paginationId} #prevBtn`);
            const nextBtn = document.querySelector(`#${paginationId} #nextBtn`);

            let currentPage = 1;
            const totalPages = Math.ceil((rows.length - 1) / rowsPerPage); // -1 for header row

            function showPage(page) {
                const start = (page - 1) * rowsPerPage + 1; // +1 to skip header
                const end = start + rowsPerPage;

                // Always show header row
                rows[0].style.display = '';

                // Show/hide data rows
                for (let i = 1; i < rows.length; i++) {
                    rows[i].style.display = (i >= start && i < end) ? '' : 'none';
                }

                prevBtn.disabled = page === 1;
                nextBtn.disabled = page === totalPages;
            }

            // Add event listeners
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    showPage(currentPage);
                }
            });

            // Show initial page
            showPage(currentPage);
        }

        // Initialize both tables when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // First table: 3 rows per page
            initializePagination('bookingTable1', 'pagination1', 3);

            // Second table: 5 rows per page
            initializePagination('bookingTable2', 'pagination2', 5);
            initializePagination('bookingTable3', 'pagination3', 3);
            initializePagination('bookingTable4', 'pagination4', 4);
        });
        // Toggle sidebar
        const toggleBtn = document.querySelector('.toggle-btn');
        const sidebar = document.querySelector('.sidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        // Admin profile dropdown
        const adminProfile = document.querySelector('.admin-profile');
        const dropdown = document.querySelector('.dropdown');

        adminProfile.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            dropdown.classList.remove('active');
        });

        // Check if there's a hash in the URL and update the active class accordingly
        window.addEventListener('load', function() {
            const hash = window.location.hash; // Get the hash from the URL
            if (hash) {
                const targetId = hash.substring(1); // Remove '#' from the hash to get the section ID
                const targetLink = document.querySelector(`.nav-links a[href="${hash}"]`);
                const targetSection = document.getElementById(targetId);

                // Remove active class from all nav links and sections
                document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));

                // Add active class to the corresponding nav link and section
                if (targetLink) {
                    targetLink.classList.add('active');
                }
                if (targetSection) {
                    targetSection.classList.add('active');
                }
            }
        });

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = link.getAttribute('href').substring(1);

                document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));

                link.classList.add('active');
                document.getElementById(targetId).classList.add('active');
            });
        });

        function openModal(button) {
            document.getElementById('modalOverlay').classList.add('active');

            const id = button.getAttribute("data-id");
            const fullname = button.getAttribute("data-fullname");
            const email = button.getAttribute("data-email");
            const phone = button.getAttribute("data-phone");
            const type = button.getAttribute("data-type");
            const guest = button.getAttribute("data-guest");
            const checkin = button.getAttribute("data-checkin");
            const checkout = button.getAttribute("data-checkout");
            const price = button.getAttribute("data-price");
            const method = button.getAttribute("data-method");
            const paystatus = button.getAttribute("data-paystatus");
            const status = button.getAttribute("data-status");

            // Populate modal fields
            document.getElementById("serviceId").value = id;
            document.getElementById("accommodationType").value = type;
            document.getElementById("guests").value = guest;
            document.getElementById("checkin").value = checkin;
            document.getElementById("checkout").value = checkout;
            document.getElementById("fullName").value = fullname;
            document.getElementById("email").value = email;
            document.getElementById("phone").value = phone;
            document.getElementById("price").value = price;
            document.getElementById("payment").value = method;
            document.getElementById("paystatus").value = paystatus;
            document.getElementById("status").value = status;
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('modalOverlay').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            // Function to open the modal
            window.confirmDelete = function(id) {
                deleteModal.style.display = 'flex';


                // Handle confirm button click
                confirmDeleteBtn.onclick = function() {
                    deleteService(id);
                };
            };
            window.confirmDeletee = function(id) {
                deleteModal.style.display = 'flex';


                // Handle confirm button click
                confirmDeleteBtn.onclick = function() {
                    deleteServicee(id);
                };
            };
            window.confirmDeleted = function(id) {
                deleteModal.style.display = 'flex';


                // Handle confirm button click
                confirmDeleteBtn.onclick = function() {
                    deleteServiced(id);
                };
            };

            // Function to close the modal
            cancelBtn.onclick = function() {
                deleteModal.style.display = 'none';
            };

            // Function to delete the service (send request to backend)
            function deleteService(id) {
                // Example AJAX request (replace with your own implementation)
                fetch(`delete_booking.php?id=${id}`, {
                        method: 'GET',
                    })
                    .then(response => response.text())
                    .then(data => {
                        deleteModal.style.display = 'none';
                        // Reload or update the page if necessary
                        location.reload();
                    })
                    .catch(error => {
                        alert('Error deleting booking!');
                    });
            }

            function deleteServicee(id) {
                // Example AJAX request (replace with your own implementation)
                fetch(`delete_service.php?id=${id}`, {
                        method: 'GET',
                    })
                    .then(response => response.text())
                    .then(data => {
                        deleteModal.style.display = 'none';
                        // Reload or update the page if necessary
                        location.reload();
                    })
                    .catch(error => {
                        alert('Error deleting Services!');
                    });
            }

            function deleteServiced(id) {
                // Example AJAX request (replace with your own implementation)
                fetch(`delete_announce.php?id=${id}`, {
                        method: 'GET',
                    })
                    .then(response => response.text())
                    .then(data => {
                        deleteModal.style.display = 'none';
                        // Reload or update the page if necessary
                        location.reload();
                    })
                    .catch(error => {
                        alert('Error deleting Announcement!');
                    });
            }
        });

        document
            .querySelector('input[type="file"]')
            .addEventListener("change", function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // You could add an image preview here if desired
                        console.log("Image loaded");
                    };
                    reader.readAsDataURL(file);
                }
            });

        function openEditModal(button) {
            // Get data from the clicked button
            const id = button.getAttribute("data-d");
            const name = button.getAttribute("data-name");
            const description = button.getAttribute("data-description");
            const image = button.getAttribute("data-image");
            const status = button.getAttribute("data-status");

            // Populate modal fields
            document.getElementById("serviceID").value = id;
            document.getElementById("serviceName").value = name;
            document.getElementById("serviceDescription").value = description;
            document.getElementById("serviceImagePreview").src = "../images/" + image;
            document.getElementById("serviceStatus").value = status;

            // Show the modal
            const mdal = document.getElementById("editServiceModal");
            mdal.style.display = "block";
        }

        function closeEditModal() {
            // Hide the modal
            const mdal = document.getElementById("editServiceModal");
            mdal.style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            const mdal = document.getElementById("editServiceModal");
            if (event.target === mdal) {
                closeEditModal();
            }
        };

        function openEditMode(button) {
            // Get data from the clicked button
            const id = button.getAttribute("data-id");
            const name = button.getAttribute("data-name");
            const date = button.getAttribute("data-date");
            const description = button.getAttribute("data-description");

            // Populate modal fields
            document.getElementById("serviceI").value = id;
            document.getElementById("serviceNamed").value = name;
            document.getElementById("serviceDate").value = date;
            document.getElementById("serviceDescriptions").value = description;

            // Show the modal
            const mode = document.getElementById("editAnnounceModal");
            mode.style.display = "block";
        }

        function loseEditModal() {
            // Hide the modal
            const mode = document.getElementById("editAnnounceModal");
            mode.style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            const mode = document.getElementById("editAnnounceModal");
            if (event.target === mode) {
                loseEditModal();
            }
        };


        const mmodal = document.getElementById("addServiceModal");

        function openAddModal() {
            mmodal.classList.add("active");
        }

        function closeModall() {
            mmodal.classList.remove("active");
        }

        // Close modal when clicking outside
        mmodal.addEventListener("click", (e) => {
            if (e.target === mmodal) {
                closeModall();
            }
        });

        const moda = document.getElementById("addAnnouncementModal");

        function openAddModa() {
            moda.classList.add("active");
        }

        function closeModa() {
            moda.classList.remove("active");
        }

        // Close modal when clicking outside
        moda.addEventListener("click", (e) => {
            if (e.target === moda) {
                closeModa();
            }
        });
    </script>
</body>

</html>