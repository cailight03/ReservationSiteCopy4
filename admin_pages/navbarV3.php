<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NULR Sidebar V3</title>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="navbarV3.css">
</head>
<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle">
            <i class='bx bx-menu' id="header-toggle" onclick="toggleNav()"></i>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="#" class="nav_logo" id="logo">
                    <img src="images/nulogo.png" alt="Custom Logo" class="nav_logo-image" style="width: 21px; height: auto;">
                    <div class="nav_logo-text">
                        <span class="nav_logo-primary">NU Laguna</span>
                        <span class="nav_logo-secondary">Reservation</span>
                    </div>
                </a>
                <div class="nav_list">
                    <a href="reports.php" class="nav_link active">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="room_reservations.php" class="nav_link">
                        <i class='bx bx-calendar nav_icon'></i>
                        <span class="nav_name">Room Reservation</span>
                    </a>
                    <a href="vehicle_reservations.php" class="nav_link">
                        <i class='bx bx-notepad nav_icon'></i> 
                        <span class="nav_name">Vehicle Reservation</span>
                    </a>
                    <a href="manage_rooms.php" class="nav_link">
                        <i class='bx bx-door-open nav_icon'></i>
                        <span class="nav_name">Manage Rooms</span>
                    </a>
                    <a href="manage_vehicle.php" class="nav_link">
                        <i class='bx bx-car nav_icon'></i>
                        <span class="nav_name">Manage Vehicles</span>
                    </a>
                    <a href="email_config.php" class="nav_link">
                        <i class='bx bx-line-chart nav_icon'></i> 
                        <span class="nav_name">Email Configuration</span>
                    </a>
                    <a href="audit_trail.php" class="nav_link">
                        <i class='bx bx-file-find nav_icon'></i>
                        <span class="nav_name">Audit Trail</span>
                    </a>
                    <a href="#" class="nav_link">
                        <i class='bx bx-log-out nav_icon'></i>
                        <span class="nav_name">Sign Out</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
    <main>
        <!-- Your main content here -->
    </main>
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Are you sure you want to sign out?</p>
            <button id="confirmSignOut">Yes</button>
            <button class="close">No</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="burgerToggleV3.js"></script>
    <script>
        // Function to show modal
        function showModal() {
            var modal = document.getElementById('confirmModal');
            modal.style.display = "block";
        }

        // Function to close modal
        function closeModal() {
            var modal = document.getElementById('confirmModal');
            modal.style.display = "none";
        }

        // Add click event listener to Sign Out link
        document.querySelector('.nav_link[href="logout_admin.php"]').addEventListener('click', function(event) {
            event.preventDefault(); // prevent the default action of the link
            showModal();
        });

        // Close the modal when the close button or outside modal is clicked
        document.querySelectorAll('.close, #confirmModal').forEach(function(element) {
            element.addEventListener('click', function() {
                closeModal();
            });
        });

        // Prevent modal from closing when clicking inside modal content
        document.querySelector('.modal-content').addEventListener('click', function(event) {
            event.stopPropagation();
        });

        // Function to handle signing out
        document.getElementById('confirmSignOut').addEventListener('click', function() {
            window.location.href = 'logout_admin.php';
        });
    </script>
</body>
</html>
