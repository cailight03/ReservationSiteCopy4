<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Add your styles or link to external stylesheets here -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"/>
    <!-- Boxicons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css"/>
    <!-- Your custom styles -->
    <link rel="stylesheet" href="navbarV2.css" />
	
	<!-- Signout Modal -->
	<style>
        /* Modal styles */
        .signoutModal {
          display: none; /* Hidden by default */
          position: fixed; /* Stay in place */
          z-index: 1; /* Sit on top */
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5); Dimmed background
        }

        /* Modal content box */
        .signoutModal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 30%; /* Could be more or less, depending on screen size */
            border-radius: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }

        /* Close button */
        .signoutClose {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .signoutClose:hover,
        .signoutClose:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Buttons */
        .signoutModal-content button {
            background-color: #2196F3; /* Blue */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        .signoutModal-content button.close {
            background-color: #f44336; /* Red */
        }
    </style>

    <title>NULR-navBarV2</title>

    <!-- Include your scripts at the end of the head -->
    <script src="sidenavbarScript.js"></script>

</head>
<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle" onclick="toggleNav()">
            <i class='bx bx-menu'></i>
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
            <a href="logout_admin.php" class="nav_link">
			    <i class='bx bx-log-out nav_icon'></i>
			    <span class="nav_name">Sign Out</span>
			</a>
        </nav>
    </div>
		<div id="confirmModal" class="modal">
		<div class="modal-content">
			<span class="close">&times;</span>
			<p>Are you sure you want to sign out?</p>
			<button id="confirmSignOut">Yes</button>
			<button class="close">No</button>
		</div>
	</div>
  </div>
<!-- Container Main start -->
<main>
  </main>
<!-- Container Main end -->

<!-- Add your scripts or link to external scripts here -->
<!-- jQuery and Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
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
