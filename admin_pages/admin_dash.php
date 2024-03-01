<?php
include '../config/connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
    exit();
}





// Query to retrieve all approved booked time slots for all rooms and dates
$sql = "SELECT time_slot FROM reservations WHERE status = 'Approved'";
$result = mysqli_query($connection, $sql);

// Prepare data for JavaScript
$peak_time_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $timeSlotsArray = explode(',', $row['time_slot']);
    $peak_time_data = array_merge($peak_time_data, $timeSlotsArray);
}

// Split time ranges into individual 30-minute intervals
$interval = 1800; // 30 minutes in seconds
$peak_time_slots = [];

foreach ($peak_time_data as $range) {
    list($start, $end) = explode("-", $range);

    $startTime = strtotime($start);
    $endTime = strtotime($end);

    for ($current = $startTime; $current < $endTime; $current += $interval) {
        $peak_time_slots[] = date("g:i A", $current) . '-' . date("g:i A", $current + $interval);
    }
}

// Sort the time slots
usort($peak_time_slots, function ($a, $b) {
    $timeA = strtotime(explode('-', $a)[0]);
    $timeB = strtotime(explode('-', $b)[0]);
    return $timeA - $timeB;
});

// Convert PHP data to JSON for JavaScript
$peak_time_json = json_encode($peak_time_slots);

$sql = "SELECT room_name, COUNT(*) as count FROM reservations GROUP BY room_name ORDER BY count DESC LIMIT 5";
$result = mysqli_query($connection, $sql);

// Prepare data for JavaScript
$room_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $room_data[] = $row;
}

// Convert PHP data to JSON for JavaScript
$room_json = json_encode($room_data);

// Query to get reservations per day
$sqlReservationsPerDay = "SELECT date FROM reservations";
$resultReservationsPerDay = mysqli_query($connection, $sqlReservationsPerDay);

// Prepare data for JavaScript
$reservations_per_day_data = [];
while ($row = mysqli_fetch_assoc($resultReservationsPerDay)) {
    // Split dates into array
    $dates = explode(',', $row['date']);
    
    // Add each date to the data array
    foreach ($dates as $date) {
        $reservations_per_day_data[] = [
            'reservation_day' => $date
        ];
    }
}

// Convert PHP data to JSON for JavaScript
$reservations_per_day_json = json_encode($reservations_per_day_data);





// Query to get the count of canceled reservations
$sqlCanceled = "SELECT COUNT(*) AS canceled_count FROM reservations WHERE status = 'Cancelled'";
$resultCanceled = mysqli_query($connection, $sqlCanceled);
$rowCanceled = mysqli_fetch_assoc($resultCanceled);
$canceledCount = $rowCanceled['canceled_count'];

// Query to get the count of all reservations
$sqlTotal = "SELECT COUNT(*) AS total_count FROM reservations";
$resultTotal = mysqli_query($connection, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalCount = $rowTotal['total_count'];

// Calculate cancellation rate
$cancellationRate = ($canceledCount / $totalCount) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="../css/UI_Pages.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        canvas {
            max-width: 400px;
            width: auto;
            height: auto;
        }
    </style>
</head>
<body>

<!-- Include the navbar -->
<?php include 'navbarV3.php'; ?>

<section class="container">
    <h2 class="grid-header" id="header">Analytics</h2>

    <div class="row">
        
        <div class="col-4 text-center">
            <h3 class="chart-title">Most Booked Rooms</h3>
            <canvas id="departmentChart"></canvas>
        </div>

        <div class="col-4 text-center">
        <h3 class="chart-title">Reservations Per Day</h3>
            <center><canvas id="reservationsPerDayChart"></canvas></center>
        </div>
        
        <div class="col-4 text-center ">
        <h3 class="chart-title">Peak Time Reservations</h3>
            <center><canvas id="peakTimeChart" ></canvas></center>
        </div>
    </div>

    <!-- Add canvas for reservations per day chart -->
    <div class="row mt-4">
    
    </div>

    <div class="row">
        <div class="col">
            <h3 class="chart-title">Cancellation Rate: <?php echo number_format($cancellationRate, 2); ?>%</h3>
        </div>
    </div>
</section>

<script>
    // Use JavaScript to render the pie chart for room reservation
    var roomData = <?php echo $room_json; ?>;
    var ctx1 = document.getElementById('departmentChart').getContext('2d');
    var myPieChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: roomData.map(item => item.room_name),
            datasets: [{
                data: roomData.map(item => item.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ],
            }]
        },
    });

     // Convert JSON data to JavaScript object
     var reservationsPerDayData = <?php echo $reservations_per_day_json; ?>;
    // Prepare data for the chart
    var labels = [];
    reservationsPerDayData.forEach(function(item) {
        var reservationDay = item.reservation_day;
        if (!labels.includes(reservationDay)) {
            labels.push(reservationDay);
        }
    });
    var data = Array(labels.length).fill(0);
    reservationsPerDayData.forEach(function(item) {
        var reservationDay = item.reservation_day;
        var index = labels.indexOf(reservationDay);
        data[index]++;
    });

    // Render the chart
    var ctx = document.getElementById('reservationsPerDayChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Reservations per Day',
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

   // Retrieve peak time reservations data from PHP
   var peakTimeData = <?php echo $peak_time_json; ?>;
    
    // Create labels and data arrays for the chart
    var labels = peakTimeData.map(function(slot) {
        return slot.split('-')[0]; // Get the start time of each time slot
    });

    var data = peakTimeData.map(function(slot) {
        return Math.floor(Math.random() * 10); // Placeholder for the number of reservations
    });

    // Create the line chart
    var ctx = document.getElementById('peakTimeChart').getContext('2d');
    var peakTimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Peak Time Reservations',
                data: data,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    

       
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
