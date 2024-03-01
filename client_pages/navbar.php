<?php
// Check if the user is logged in
$userFirstName = isset($_SESSION['firstName']) ? $_SESSION['firstName'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        /* Default styles for the navbar */
        .custom-navbar {
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #ccc; 
        }

        /* Sticky styles for smaller screens */
        @media (max-width: 992px) {
            .custom-navbar {
                position: sticky;
                top: 0;
                background-color: white; /* Adjust background color as needed */
                z-index: 1000; /* Ensure it's above other elements */
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg custom-navbar ">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><img src="../img/navbar_img/National_University_seal.png" alt="">NU Laguna Reservation</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="user-text">
                            Hello! <?php echo $userFirstName; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../admin_pages/admin_dashboard.php"><i class="bi bi-person" style="padding-right:10px;"></i>Admin Dashboard</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../login_logout_controller/logout.php"><i class="bi bi-door-closed-fill door-icon"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
    
</body>
</html>
