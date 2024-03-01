<?php
include '../../config/connection.php';

// Function to split a time range into 30-minute intervals
function splitTimeRange($timeRange) {
    $intervals = array();
    list($start, $end) = explode("-", $timeRange);
    $startTime = strtotime($start);
    $endTime = strtotime($end);
    $interval = 1800; // 30 minutes in seconds

    for ($current = $startTime; $current < $endTime; $current += $interval) {
        $intervals[] = date("g:i A", $current) . '-' . date("g:i A", $current + $interval);
    }

    return $intervals;
}

// Validate the incoming parameters
if (isset($_GET['room_id']) && isset($_GET['date'])) {
    $roomId = $_GET['room_id'];
    $selectedDate = $_GET['date'];

    // Query to retrieve approved or pending booked time slots for the given room and date
    $bookedTimeSlotsQuery = "SELECT date, time_slot FROM reservations WHERE room_id = ? AND status IN ('pending', 'Approved')";
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

    // Split booked time slots into 30-minute intervals
    $bookedTimeSlotIntervals = array();
    foreach ($bookedTimeSlots as $bookedSlot) {
        $bookedTimeSlotIntervals = array_merge($bookedTimeSlotIntervals, splitTimeRange($bookedSlot));
    }

    // Add your custom time slot
    $customTimeSlot = "7:30 AM-12:30 PM";

    // Split custom time slot into 30-minute intervals
    $customTimeSlotIntervals = splitTimeRange($customTimeSlot);

    // Echo the split custom time slot array
    echo "Custom Time Slot Intervals: " . json_encode($customTimeSlotIntervals) . "<br>";

    // Echo the booked time slots array
    echo "Booked Time Slot Intervals: " . json_encode($bookedTimeSlotIntervals) . "<br>";

    // Echo a message for each interval indicating if it's booked or not
    echo "Availability: <br>";
    foreach ($customTimeSlotIntervals as $interval) {
        if (in_array($interval, $bookedTimeSlotIntervals)) {
            echo $interval . ' - Booked<br>';
        } else {
            echo $interval . ' - Available<br>';
        }
    }

} else {
    // Invalid or missing parameters
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid or missing parameters.';
}
?>