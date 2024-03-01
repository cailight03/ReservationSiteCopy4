if ($categoryId == 2) {
        if (($userType == 'student' || $userType == 'employee') && $activityType == 'Course Activity') {
            // Fetch email from colleges where name = 'NU Laguna Reservation'
            $query = "SELECT name, email FROM colleges WHERE name = 'NU Laguna Reservation'";
        } elseif (($userType == 'student' || $userType == 'employee') && ($activityType == 'Org Activity' || $activityType == 'Event')) {
            // Fetch email from colleges where name = 'SDAO'
            $query = "SELECT name, email FROM colleges WHERE name = 'SDAO'";
        } elseif ($userType == 'admin' && $activityType == 'Course Activity') {
            // Fetch email from colleges where name = 'Sir Rich'
            $query = "SELECT name, email FROM colleges WHERE name = 'SAD'";
        } elseif ($userType == 'admin' && ($activityType == 'Org Activity' || $activityType == 'Event')) {
            // Fetch email from colleges where name = 'NU Laguna Reservation'
            $query = "SELECT name, email FROM colleges WHERE name = 'NU Laguna Reservation'";
        } else {
            // Handle other cases or provide a default recipient email
            $recipientEmail = 'default@email.com';
        }
    } else {
        if (($userType == 'student' || $userType == 'employee') && $activityType == 'Course Activity') {
            // Fetch email from colleges where name = 'NU Laguna Reservation'
            $query = "SELECT name, email FROM colleges WHERE name = 'Academic Director'";
        } elseif (($userType == 'student' || $userType == 'employee') && ($activityType == 'Org Activity' || $activityType == 'Event')) {
            // Fetch email from colleges where name = 'SDAO'
            $query = "SELECT name, email FROM colleges WHERE name = 'NU Laguna Reservation'";
        } elseif ($userType == 'admin' && $activityType == 'Course Activity') {
            // Fetch email from colleges where name = 'Sir Rich'
            $query = "SELECT name, email FROM colleges WHERE name = 'Physical Facilities'";
        } elseif ($userType == 'admin' && ($activityType == 'Org Activity' || $activityType == 'Event')) {
            // Fetch email from colleges where name = 'NU Laguna Reservation'
            $query = "SELECT name, email FROM colleges WHERE name = 'SAD'";
        } else {
            // Handle other cases or provide a default recipient email
            $recipientEmail = 'default@email.com';
        }
    }
    
    if (isset($query)) {
        $result = mysqli_query($connection, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $recipientEmail = $row['email'];
            $recipientName = $row['name'];
        } else {
            // Handle query error
            $recipientEmail = 'default@email.com'; // Provide a default recipient email
        }
    }