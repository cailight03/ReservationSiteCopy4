<?php
// Path: audit_trail.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$servername = 'localhost'; 
$username = 'root';
$password = '';
$dbname = 'reservationdb';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch audit trail logs from the database 
$sql = "SELECT a.timestamp, COALESCE(u.id, 'N/A') AS user_id, COALESCE(u.requestor, 'N/A') AS name, a.reservation_id, a.action 
        FROM audit_logs a
        LEFT JOIN reservations u ON a.user_id = u.id 
        ORDER BY a.timestamp DESC";
$result = $conn->query($sql);

// Error handling
if (!$result) {
    die("Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail Logs</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php include 'navbarV2.php'; ?>
    <div class="container-fluid">
        <h2 class="mt-4">Audit Logs</h2>
        <p class="text-muted">Monitor user activity and changes in reservations.</p>
        <table id="audit_logs" class="table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Reservation ID</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['user_id']."</td>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td>".$row['reservation_id']."</td>";
                        echo "<td>".$row['action']."</td>";
                        echo "<td>".$row['timestamp']."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No logs found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
        $('#audit_logs').DataTable({
            'paging': true, // Enable pagination
            'lengthMenu': [10, 25, 50, 75, 100], // Set the number of records per page options
            'pageLength': 10, // Set the default number of records per page
            'pagingType': "full_numbers" // Set the pagination control type
        });
    });
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
