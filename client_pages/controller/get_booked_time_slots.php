<?php
include '../../config/connection.php';

// Validate the incoming parameters
if (isset($_GET['room_id']) && isset($_GET['date'])) {
    $roomId = $_GET['room_id'];
    $selectedDate = $_GET['date'];

    // Query to retrieve approved or pending booked time slots for the given room and date
    $bookedTimeSlotsQuery = "SELECT date, time_slot FROM reservations WHERE room_id = ? AND status IN ('Approved')";
    $stmt = $connection->prepare($bookedTimeSlotsQuery);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $stmt->bind_result($dates, $timeSlot);

    $bookedTimeSlots = array();

    // Fetch approved or pending booked time slots into an array
    while ($stmt->fetch()) {
        $datesArray = explode(',', $dates);
        $timeSlotsArray = explode(',', $timeSlot);

        // Combine dates and time slots into an associative array
        $combined = array_combine($datesArray, $timeSlotsArray);

        // Check if the selected date is in the array
        if (array_key_exists($selectedDate, $combined)) {
            $bookedTimeSlots[] = $combined[$selectedDate];
        }
    }

    $stmt->close();

    // Split time ranges into individual 30-minute intervals
    $interval = 1800; // 30 minutes in seconds
$result = array();

foreach ($bookedTimeSlots as $range) {
    list($start, $end) = explode("-", $range);

    $startTime = strtotime($start);
    $endTime = strtotime($end);

    for ($current = $startTime; $current < $endTime; $current += $interval) {
        $result[] = date("g:i A", $current) . '-' . date("g:i A", $current + $interval);
    }
}

// Sort the time slots
usort($result, function ($a, $b) {
    $timeA = strtotime(explode('-', $a)[0]);
    $timeB = strtotime(explode('-', $b)[0]);
    return $timeA - $timeB;
});

// Return the booked time slots as JSON
header('Content-Type: application/json');
echo json_encode($result);
} else {
    // Invalid or missing parameters
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid or missing parameters.';
}
?>
