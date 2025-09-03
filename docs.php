<?php
include('database.php');
$alert = "";
if (isset($_POST['addDocument'])) {
    $title = $_POST['title'];
    $type = $_POST['doc_type'];

    // handle file upload
    $fileName = $_FILES['file']['name'];
    $fileTmp = $_FILES['file']['tmp_name'];
    $targetDir = "uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($fileName);
    move_uploaded_file($fileTmp, $targetFile);

    $sql = "INSERT INTO documents (title, type, filename) 
            VALUES ('$title', '$type', '$fileName')";
    $conn->query($sql);
    $message = "<div class='alert alert-success'>File uploaded successfully!</div>";
    $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: ' Upload!',
                          text: 'Upload Successful !',
                          confirmButtonColor: '#0f5af2ff'
                        }).then(()=>{ window.location.href='docs.php'; });
                      </script>";
    
}

// -------------------- UPDATE DOCUMENT --------------------
if (isset($_POST['updateDocument'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $type = $_POST['type'];

    $stmt = $conn->prepare("UPDATE documents SET title=?, type=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $type, $id);

    if ($stmt->execute()) {
         $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: ' update!',
                          text: 'updated Successful',
                          confirmButtonColor: '#dede0eff'
                        }).then(()=>{ window.location.href='docs.php'; });
                      </script>";
    } else {
        echo "Error: " . $conn->error;
    }
}




// -------------------- DELETE DOCUMENT --------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM documents WHERE id=$id");
    $alert = "<script>
                        Swal.fire({
                          icon: 'success',
                          title: 'Deleted Successful!',
                          text: 'Welcome, admin!',
                          confirmButtonColor: '#00ff51ff'
                        }).then(()=>{ window.location.href='docs.php'; });
                      </script>";

}
// -------------------- SEARCH & FILTER --------------------
$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';
$filter = isset($_POST['doc_type']) ? $conn->real_escape_string($_POST['doc_type']) : '';

$sql = "SELECT * FROM documents WHERE 1=1";

// if search keyword entered
if (!empty($search)) {
    $sql .= " AND (title LIKE '%$search%' OR type LIKE '%$search%')";
}

// if filter selected
if (!empty($filter)) {
    $sql .= " AND type='$filter'";
}

$sql .= " ORDER BY uploaded_on DESC";

$result = $conn->query($sql);
$conn->close();
include('sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CORE3 Customer Relationship & Business Control</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            transition: all 0.3s ease;
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

        /* Upload Documents */
        .upload-section {
            background-color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .dark-mode .upload-section {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        .uploadform {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .upload input,
        .upload select {
            width: 390px;
            padding: 0.4rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            margin-top: 0.5rem;
            background-color: white;
        }

        .dark-mode .upload input,
        .dark-mode .upload select {
            background-color: #2a3a5a;
            border-color: #3a4b6e;
            color: var(--text-light);
        }


        .btn {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-upload {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-upload:hover {
            background-color: #3a5bc7;
        }


        /* Search Documents*/
        .searchfilter {
            position: relative;
            background-color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .dark-mode .searchfilter {
            background-color: var(--dark-card);
            color: var(--text-light);
        }

        .searchform {
            display: flex;
        }

        .search-titleortype input {
            width: 600px;
            padding: 0.4rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            margin: 0.5rem 0.8rem 0 0;
        }

        .dark-mode .search-titleortype input {
            background-color: #2a3a5a;
            border-color: #3a4b6e;
            color: var(--text-light);
        }

        .filter-select select {
            width: 400px;
            padding: 0.4rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            margin: 0.5rem 0.8rem 0 0;
            background-color: white;
        }

        .dark-mode .filter-select select {
            background-color: #2a3a5a;
            border-color: #3a4b6e;
            color: var(--text-light);
        }

        .btn-search {
            background-color: var(--primary-color);
            color: white;
            width: 170px;
            margin-top: 0.5rem;
        }

        /* Table Section */
        .table-section {
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

        .modal-dialog {
            max-width: 500px;
            position: fixed;
            left: 50%;
            top: 0;
            transform: translate(-50%, -50%);
            width: 500px;
            color: var(--text-light);
        }

        .dark-mode .modal-header h5 {
            color: var(--secondary-color);
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
                <h1>E-Documentation & Compliance Manager</h1>
                <?php if (!empty($message))
                    echo $message; ?>
            </div>
            <div class="theme-toggle-container">
                <span class="theme-label">Dark Mode</span>
                <label class="theme-switch">
                    <input type="checkbox" id="themeToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="upload-section">
            <h3>Upload New Documents</h3>
            <form method="post" enctype="multipart/form-data" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="title" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="doc_type" required>
                            <option value="">Select Document Type</option>
                            <option value="Bill of Lading">Bill of Lading</option>
                            <option value="Invoice">Invoice</option>
                            <option value="Customs Clearance">Customs Clearance</option>
                            <option value="Compliance Certificate">Compliance Certificate</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="file" name="file" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" name="addDocument" class="btn btn-primary w-100">Add Document</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="searchfilter">
            <h3>Search & Filter Documents</h3>
            <form method="POST" class="searchform">
                <div class="search-titleortype">
                    <input type="text" name="search" placeholder="Search by Title or Type"
                        value="<?= isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '' ?>">
                </div>
                <div class="filter-select">
                    <select name="doc_type">
                        <option value="">Filter by Document Type</option>
                        <option value="Bill of Lading" <?= (isset($_POST['doc_type']) && $_POST['doc_type'] == "Bill of Lading") ? "selected" : "" ?>>Bill of Lading</option>
                        <option value="Invoice" <?= (isset($_POST['doc_type']) && $_POST['doc_type'] == "Invoice") ? "selected" : "" ?>>Invoice</option>
                        <option value="Customs Clearance" <?= (isset($_POST['doc_type']) && $_POST['doc_type'] == "Customs Clearance") ? "selected" : "" ?>>Customs Clearance</option>
                        <option value="Compliance Certificate" <?= (isset($_POST['doc_type']) && $_POST['doc_type'] == "Compliance Certificate") ? "selected" : "" ?>>Compliance Certificate
                        </option>
                    </select>
                </div>
                <div class="search-btn">
                    <button type="submit" class="btn btn-search">Search</button>
                </div>
            </form>
        </div>





        <div class="table-section">
            <h3 style="width: 100%;">Document Records</h3>
            <table id="employeesTable">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Filename</th>
                        <th scope="col">Uploaded On</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="employeesTableBody">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['title']; ?></td>
                            <td><?= $row['type']; ?></td>
                            <td><?= $row['filename']; ?></td>
                            <td><?= $row['uploaded_on']; ?></td>
                            <td><?= $row['status']; ?></td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="uploads/<?= $row['filename']; ?>" class="btn btn-sm btn-success"
                                        download>Download</a>
                                    <button class="btn btn-sm btn-warning p-2" data-bs-toggle="modal"
                                        data-bs-target="#editDoc<?= $row['id']; ?>">Edit</button>
                                    <a href="docs.php?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this document?')">Delete</a>
                                </div>

                            </td>
                        </tr>


                        <!-- Edit Modal -->
                        <div class="modal fade" id="editDoc<?= $row['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content" style="background: #1a1a2e;">
                                    <form method="post">
                                        <div class="modal-header">
                                            <h5 style="color:white">Edit Document</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <div class="mb-2">
                                                <label>Title</label>
                                                <input type="text" name="title" class="form-control"
                                                    value="<?= $row['title']; ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label>Type</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="">Select Document Type</option>
                                                    <option value="Bill of Lading" <?= $row['type'] == 'Bill of Lading' ? 'selected' : '' ?>>Bill of Lading</option>
                                                    <option value="Invoice" <?= $row['type'] == 'Invoice' ? 'selected' : '' ?>>
                                                        Invoice</option>
                                                    <option value="Customs Clearance" <?= $row['type'] == 'Customs Clearance' ? 'selected' : '' ?>>Customs Clearance</option>
                                                    <option value="Compliance Certificate" <?= $row['type'] == 'Compliance Certificate' ? 'selected' : '' ?>>Compliance Certificate</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="updateDocument"
                                                class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                </tbody>
            </table>
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
    </script>
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (!empty($alert))
        echo $alert; ?>
</body>

</html>