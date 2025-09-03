<?php
include('database.php');

$alert = "";
/* Add New Customer */
if (isset($_POST['add'])) {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $company = $conn->real_escape_string($_POST['company']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $status = $conn->real_escape_string($_POST['status']);
    $lastcontract = $conn->real_escape_string($_POST['created_at']);

    $sql = "INSERT INTO crm2 (customer_name, company, email, phone, status) 
            VALUES ('$customer_name', '$company', '$email', '$phone', '$status')";

    if ($conn->query($sql) === TRUE) {
       $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: ' Successful!',
                          text: 'Add new employee Successful !',
                          confirmButtonColor: '#0ff25bff'
                        }).then(()=>{ window.location.href='crm2.php'; });
                      </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

/* Update Customer */
if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $company = $conn->real_escape_string($_POST['company']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "UPDATE crm2
            SET customer_name='$customer_name', 
                company='$company', 
                email='$email', 
                phone='$phone',
                status='$status'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
       $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: ' Successful!',
                          text: 'Update Successful !',
                          confirmButtonColor: '#0f5af2ff'
                        }).then(()=>{ window.location.href='crm2.php'; });
                      </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

/* Delete Customer */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM crm2 WHERE id=$id");
    $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: 'Deleted Successful!',
                          text: '',
                          confirmButtonColor: '#00ff51ff'
                        }).then(()=>{ window.location.href='crm2.php'; });
                      </script>";

}

/* Fetch Customers */
$result = $conn->query("SELECT * FROM CRM2 ORDER BY created_at DESC");

/* Search Customer */


$conn->close();
?>
<?php include('sidebar.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CORE3 Customer Relationship & Business Control</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="content" id="mainContent">
        <div class="header">
            <div class="hamburger" id="hamburger">☰</div>
            <div>
                <h1>Customer Relationship Management</h1>
            </div>
            <div class="theme-toggle-container">
                <span class="theme-label">Dark Mode</span>
                <label class="theme-switch">
                    <input type="checkbox" id="themeToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="table-section">
            <div class="table-section1">
                <div class="table-head">
                    <input type="search" class="control" id="searchInput" placeholder="Search customer...">
                </div>

                <div class="table-head">
                    <select class="select" id="filterStatus">
                        <option value="">Filter by Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <display class="table-button">
                    <button id="addM" class="btn toggle-table-btn">Add New Employee</button>
            </div>
            <table id="CustomersTable">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Last Contact</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']): ?>
                        <tr>
                            <form method="POST">
                                <td><input type="hidden" name="customer_name"
                                        value="<?= $row['customer_name']; ?>"><?= $row['customer_name']; ?></td>
                                <td><input type="text" name="company" value="<?= $row['company']; ?>" required></td>
                                <td><input type="email" name="email" value="<?= $row['email']; ?>" required></td>
                                <td><input type="text" name="phone" value="<?= $row['phone']; ?>"></td>
                                <td><select class="form-select" name="status">
                                        <option value="Active">Active</option>
                                        <option value="Prospect">Prospect</option>
                                        <option value="Inactive">Inactive</option>
                                        <?= $row['status']; ?></td>
                                <td><?= $row['created_at']; ?></td>
                                <td>
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="Eupdate" name="update">Update</button>
                                    <a href="CRM2.php" class="Ecancel">Cancel</a>
                                </td>
                            </form>
                        </tr>
                    <?php else: ?>
                        <tbody id="customerData">
                            <td><?= $row['customer_name']; ?></td>
                            <td><?= $row['company']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['phone']; ?></td>
                            <td><?= $row['status']; ?></td>
                            <td><?= $row['created_at']; ?></td>
                            <td class="actions" style="text-align: center;">
                                <a href="CRM2.php?edit=<?= $row['id']; ?>" class="edit">Edit</a> |
                                <a href="CRM2.php?delete=<?= $row['id']; ?>" onclick="return confirm('Delete this record?')"
                                    class="delete">Delete</a>
                            </td>
                        </tbody>
                    <?php endif; ?>
                <?php endwhile; ?>
            </table>

            <div class="modal-section" id="addmodal">
                <div class="modal-content">
                    <form class="modal-content1" method="POST">
                        <div class="modal-header">
                            <h3 class="modal-title" id="addCustomerModalLabel">Add New Customer</h3>
                        </div>
                        <div class="modal-body">
                            <div class="add-form">
                                <label>Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                            <div class="add-form">
                                <label>Company</label>
                                <input type="text" class="form-control" name="company" required>
                            </div>
                            <div class="add-form">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="add-form">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                            <div class="add-form">
                                <label>Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Prospect">Prospect</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="cancel" class="btn btn-cancel">Cancel</button>
                            <button type="submit" name="add" class="btn btn-add">Add Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        const checkbox = document.getElementById("themeToggle");

        if (localStorage.getItem("darkMode") === "enabled") {
            document.body.classList.add("dark-mode");
            checkbox.checked = true;
        }

        checkbox.addEventListener("change", () => {
            if (checkbox.checked) {
                document.body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "enabled");
            } else {
                document.body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "disabled");
            }
        });

        document.getElementById('hamburger').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        });

        /* modal */
        var modal = document.getElementById("addmodal");
        var btn = document.getElementById("addM");
        var span = document.getElementsByClassName("cancel")[0];
        btn.onclick = function () {
            modal.style.display = "block";
        }
        cancel.onclick = function () {
            modal.style.display = "none";
        }
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        /* search */
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (!empty($alert))
        echo $alert; ?>
</body>

</html>