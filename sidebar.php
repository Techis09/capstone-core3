<?php
// session & role check FIRST
session_start();

// if not logged in, kick back to login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit();
}

// if user role is NOT admin, redirect to user dashboard
if ($_SESSION['role'] !== "admin") {
    header("Location: user.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CORE-3</title>
      <style>
 :root {
            --sidebar-width: 250px;
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --tertiary-color: #f43a3aff;
            --dark-bg: #1a1a2e;
            --dark-card: #16213e;
            --text-light: #f8f9fa;
            --text-dark: #212529;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --border-radius: 0.35rem;
            --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow-x: hidden;
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }

        body.dark-mode {
            --secondary-color: var(--dark-bg);
            background-color: var(--secondary-color);
            color: var(--text-light);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #2c3e50;
            color: white;
            padding: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            transform: translateX(0);
        }

        a{
            text-align: center;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar .logo {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .logo img {
            max-width: 100%;
            height: auto;
        }

        .system-name {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar a {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid white;
        }

        .admin-feature {
            background-color: rgba(0, 0, 0, 0.1);
        }

        /* Main Content */
        .content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }

        .content.expanded {
            margin-left: 0;
        }

        /* Header */
        .header {
            background-color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dark-mode .header {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        .hamburger {
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .system-title {
            color: var(--primary-color);
            font-size: 1rem;
        }


        /* Table Section */
        .table-section {
            position: relative;
            background-color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .dark-mode .table-section {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .dark-mode th,
        .dark-mode td {
            border-bottom-color: #3a4b6e;
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table-section1 {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .table-head input,
        .table-head select {
            width: 300px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            margin-right: 1.5rem;
            background-color: white;
        }

        .dark-mode .table-head input,
        .dark-mode .table-head select {
            background-color: #2a3a5a;
            border-color: #3a4b6e;
            color: var(--text-light);
        }

        .table-button {
            position: absolute;
            right: 25px;
        }

        .btn {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }

        .toggle-table-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .toggle-table-btn:hover {
            background-color: #3a5bc7;
        }

        /* The Modal */
        .modal-section {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            position: absolute;
            right: 10rem;
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #ddd;
            width: 70%;
        }

        .dark-mode .modal-content {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        .add-form {
            margin-bottom: 1rem;
        }

        .add-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .add-form input,
        .add-form select,
        .add-form textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .dark-mode .add-form input,
        .dark-mode .add-form select {
            background-color: #2a3a5a;
            border-color: #3a4b6e;
            color: var(--text-light);
        }

        .btn-add {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-add:hover {
            background-color: #3a5bc7;
        }

        .btn-cancel {
            background-color: var(--tertiary-color);
            color: white;
        }

        .btn-cancel:hover {
            background-color: #e50d0dff;
        }

        .dark-mode td input,
        .dark-mode td select {
            background-color: #2a3a5a;
            border-color: #3a4b6e;
            color: var(--text-light);
        }

        .Eupdate {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            background-color: #4fcbdeff;
            color: white;
        }

        .Eupdate:hover {
            background-color: #15c8e3ff;
        }

        .Ecancel,
        .delete {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            background-color: var(--tertiary-color);
            color: white;
            text-decoration-line: none;
        }

        .Ecancel,
        .delete:hover {
            background-color: #e50d0dff;
        }

        .edit {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            background-color: #1a629dff;
            color: white;
            text-decoration-line: none;
        }

        .edit:hover {
            background-color: #0476d3ff;
        }



        /* Theme Toggle */
        .theme-toggle-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .theme-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .theme-switch input {
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
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--primary-color);
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
   
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="logo.png" alt="SLATE Logo">
        </div>
        <div class="system-name">CORE TRANSACTION 3</div>
        <a href="dashboard.php"><span style="margin-right:8px;">🏠</span>Dashboard</a>
        <a href="CRM2.php"><span style="margin-right:8px;">👥</span>Customer Relationship Management</a>
        <a href="CSM.php"><span style="margin-right:8px;">📄</span>Contract & SLA Monitoring</a>
        <a href="docs.php"><span style="margin-right:8px;">📁</span>E-Documentations & Compliance Manager</a>
        <a href="BIFA.php"><span style="margin-right:8px;">📊</span>Business Intelligence & Freight Analytics</a>
        <a href="admin-notif.php"><span style="margin-right:8px;">🔔</span>Customer Portal & Notification Hub</a>
        <a href="logout.php"><span style="margin-right:8px;">🚪</span>Logout</a>
    </div>

</body>
</html>