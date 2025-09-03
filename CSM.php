<?php
include('database.php');


$alert = "";
if (isset($_POST['add_contract'])) {
    $contract_id = $conn->real_escape_string($_POST['contract_id']);
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $status = $conn->real_escape_string($_POST['status']);
    $sla = $conn->real_escape_string($_POST['sla_compliance']);

    $sql = "INSERT INTO contracts (contract_id,  client_name, start_date, end_date, status, sla_compliance) 
            VALUES ('$contract_id', '$client_name', '$start_date', '$end_date', '$status', '$sla')";
    if ($conn->query($sql) === TRUE) {
           $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: ' Successful!',
                          text: 'add contact Successful',
                          confirmButtonColor: '#0072ff'
                        }).then(()=>{ window.location.href='csm.php'; });
                      </script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
// Count Active Contracts
$activeQuery = $conn->query("SELECT COUNT(*) AS total_active FROM contracts WHERE status='Active'");
$activeRow = $activeQuery->fetch_assoc();
$activeContracts = $activeRow['total_active'];
if (isset($_POST['add_contract'])) {

    $contract_id = $_POST['contract_id'];
    $client_name = $_POST['client_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    // Insert into contracts
    $sql = "INSERT INTO contracts (contract_id, client_name, start_date, end_date, status)
            VALUES ('$contract_id', '$client_name', '$start_date', '$end_date', '$status')";

    if ($conn->query($sql) === TRUE) {

        //  Insert into recent_activity
        $activity = "New contract added: " . $contract_id;
        $conn->query("INSERT INTO recent_activity (activity, status) 
                      VALUES ('$activity', 'Success')");

      $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: ' Successful!',
                          text: 'add contact Successful',
                          confirmButtonColor: '#0072ff'
                        }).then(()=>{ window.location.href='csm.php'; });
                      </script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// ✅ Update Contract
if (isset($_POST['update_contract'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $status = $conn->real_escape_string($_POST['status']);
    $sla = $conn->real_escape_string($_POST['sla_compliance']);

    $sql = "UPDATE contracts 
            SET client_name='$client_name', start_date='$start_date', end_date='$end_date', 
                status='$status', sla_compliance='$sla'
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        // log
        $activity = "Contract updated: ID $id";
        $conn->query("INSERT INTO recent_activity (activity, status) VALUES ('$activity', 'Updated')");
         $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: ' update!',
                          text: 'updated Successful',
                          confirmButtonColor: '#dede0eff'
                        }).then(()=>{ window.location.href='csm.php'; });
                      </script>";
       
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// ✅ Delete Contract
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM contracts WHERE id='$id'");

    // log
    $activity = "Contract deleted: ID $id";
    $conn->query("INSERT INTO recent_activity (activity, status) VALUES ('$activity', 'Deleted')");
    $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: 'Deleted Successful!',
                          text: 'Welcome, admin!',
                          confirmButtonColor: '#00ff51ff'
                        }).then(()=>{ window.location.href='csm.php'; });
                      </script>";


}

include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CORE3 Customer Relationship & Business Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
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

        /* Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card {
            background-color: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        .dark-mode .card {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.75rem 0 rgba(58, 59, 69, 0.2);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Select Section */
        .Select-section {
            background-color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .Select-section1 {
            display: flex;
            text-align: center;
            justify-content: space-between;
        }

        .form input,
        .form select {
            width: 280px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            background-color: white;
        }

        .dark-mode .form input,
        .dark-mode .form select {
            background-color: #2a3a5a;
            border-color: #3a4b6e;
            color: var(--text-light);
        }

        .dark-mode .Select-section {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        /* Table Section */
        .table-section {
            background-color: white;
            text-align: center;
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
            margin-top: 1rem;
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

        .modal-dialog {
            max-width: 500px;
            position: fixed;
            left: 50%;
            top: 0;
            transform: translate(-50%, -50%);
            width: 500px;
            color: var(--text-light);
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
                <h1>Contract & SLA Monitoring</h1>
            </div>
            <div class="theme-toggle-container">
                <span class="theme-label">Dark Mode</span>
                <label class="theme-switch">
                    <input type="checkbox" id="themeToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Contracts</h3>
                <div class="stat-value" id="Total Users">
                    <?php
                    $totalContractsQuery = $conn->query("SELECT COUNT(*) as total FROM contracts");
                    $totalContracts = mysqli_fetch_assoc($totalContractsQuery);
                    echo $totalContracts['total'];
                    ?>
                </div>
                <div class="stat-label">


                </div>
            </div>

            <div class="card">
                <h3>Active Contracts</h3>
                <div class="stat-value" id="Active Contracts">
                    <div class="stat-value"><?php echo $activeContracts; ?></div>

                </div>
                <div class="stat-label"></div>
            </div>

            <div class="card">
                <h3>Expiring Soon</h3>
                <div class="stat-value" id="Pending Request">0</div>
                <div class="stat-label">Loading data...</div>
            </div>

            <div class="card">
                <h3>SLA Compliance</h3>
                <div class="stat-value" id="System Alert">0</div>
                <div class="stat-label">Loading data...</div>
            </div>
        </div>

        <div class="Select-section">
            <h3 class="mb-4">Add New Contract</h3>
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label for="contract_id" class="form-label">Contract ID</label>
                    <input type="text" class="form-control" id="contract_id" name="contract_id"
                        placeholder="Contract ID" required>
                </div>
                <div class="col-md-6">
                    <label for="client_name" class="form-label">Client Name</label>
                    <input type="text" class="form-control" id="client_name" name="client_name"
                        placeholder="Client Name" required>
                </div>
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="">Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Expired">Expired</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sla_compliance" class="form-label">SLA Compliance</label>
                    <select class="form-select" id="sla_compliance" name="sla_compliance" required>
                        <option value="">SLA Compliance</option>
                        <option value="Compliant">Compliant</option>
                        <option value="Non-Compliant">Non-Compliant</option>
                    </select>
                </div>
                <div class="col-12 text-center mt-3">
                    <button type="submit" name="add_contract" class="btn btn-primary px-4">
                        Add Contract
                    </button>
                </div>
            </form>
        </div>


        <!-- update -->
        <?php
        if (isset($_GET['edit'])) {
            $id = intval($_GET['edit']);
            $editResult = $conn->query("SELECT * FROM contracts WHERE id='$id'");
            $editRow = $editResult->fetch_assoc();
            ?>

        <?php } ?>





        <div class="table-section">
            <h3>Contracts List</h3>
            <table id="contractsTable" class="table-selection">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Contract Code</th>
                        <th>Client</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>SLA Compliance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM contracts ORDER BY start_date DESC");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['contract_id']}</td>
                                <td>{$row['client_name']}</td>
                                <td>{$row['start_date']}</td>
                                <td>{$row['end_date']}</td>
                                <td>{$row['status']}</td>
                    <td>{$row['sla_compliance']}</td>
                    <td>
                        <button type='button' 
                                class='btn btn-sm btn-warning editBtn' 
                                data-id='{$row['id']}' 
                                data-contract_id='{$row['contract_id']}' 
                                data-client='{$row['client_name']}' 
                                data-start='{$row['start_date']}' 
                                data-end='{$row['end_date']}' 
                                data-status='{$row['status']}' 
                                data-sla='{$row['sla_compliance']}'
                                data-bs-toggle='modal' data-bs-target='#editModal'>
                            Edit
                        </button>

                        <a href='?delete={$row['id']}' class='btn btn-sm btn-danger' 
                           onclick=\"return confirm('Are you sure you want to delete this contract?');\">Delete</a>
                    </td>
                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No contracts found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Contract Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Contract</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row g-3">
                            <input type="hidden" name="id" id="edit_id">

                            <div class="col-md-6">
                                <label class="form-label">Client Name</label>
                                <input type="text" class="form-control" name="client_name" id="edit_client" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="edit_start" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="edit_end" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="edit_status" required>
                                    <option value="Active">Active</option>
                                    <option value="Expired">Expired</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SLA Compliance</label>
                                <select class="form-select" name="sla_compliance" id="edit_sla" required>
                                    <option value="Compliant">Compliant</option>
                                    <option value="Non-Compliant">Non-Compliant</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update_contract" class="btn btn-success">Update
                                Contract</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('edit_id').value = this.dataset.id;
                    document.getElementById('edit_client').value = this.dataset.client;
                    document.getElementById('edit_start').value = this.dataset.start;
                    document.getElementById('edit_end').value = this.dataset.end;
                    document.getElementById('edit_status').value = this.dataset.status;
                    document.getElementById('edit_sla').value = this.dataset.sla;
                });
            });
        </script>

        <!-- Remove this duplicate block, as the same code exists above. -->
</body>

</html>
<script>
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_client').value = this.dataset.client;
            document.getElementById('edit_start').value = this.dataset.start;
            document.getElementById('edit_end').value = this.dataset.end;
            document.getElementById('edit_status').value = this.dataset.status;
            document.getElementById('edit_sla').value = this.dataset.sla;
        });
    });
</script>

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
</script>

<!-- ✅ Add Bootstrap JS here -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <?php if (!empty($alert))
    echo $alert; ?>
</body>

</html>