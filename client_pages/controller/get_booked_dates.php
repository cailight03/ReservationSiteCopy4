<?php
include '../../config/connection.php';

if (isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Query to retrieve approved or pending booked time slots for the given room
    $bookedTimeSlotsQuery = "SELECT date, time_slot FROM reservations WHERE room_id = ? AND status IN ('Pending','Approved')";
    $stmt = $connection->prepare($bookedTimeSlotsQuery);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $stmt->bind_result($dates, $timeSlot);

    // Array to store all compiled time slots for each date
    $allCompiledTimeSlotsByDate = [];

    // Array to store dates with count 28
    $bookedDates = [];

    // Fetch approved or pending booked time slots
    while ($stmt->fetch()) {
        $datesArray = explode(',', $dates);
        $timeSlotsArray = explode(',', $timeSlot);

        // Combine dates and time slots into an associative array
        $combined = array_combine($datesArray, $timeSlotsArray);

        foreach ($combined as $selectedDate => $timeRange) {
            // Process the time range for each selected date
            $interval = 1800; // 30 minutes in seconds

            list($start, $end) = explode("-", $timeRange);
            $startTime = strtotime($start);
            $endTime = strtotime($end);

            // Generate individual 30-minute intervals for the compiled time range
            for ($current = $startTime; $current < $endTime; $current += $interval) {
                $allCompiledTimeSlotsByDate[$selectedDate][] = date("g:iA", $current) . '-' . date("g:iA", $current + $interval);
            }
        }
    }

    // Sort all compiled time slots for each date
    foreach ($allCompiledTimeSlotsByDate as &$timeSlots) {
        usort($timeSlots, function ($a, $b) {
            $timeA = strtotime(explode('-', $a)[0]);
            $timeB = strtotime(explode('-', $b)[0]);
            return $timeA - $timeB;
        });
    }

    // Check count and add date to bookedDates array if count is 28
    foreach ($allCompiledTimeSlotsByDate as $date => $compiledTimeSlots) {
        if (count($compiledTimeSlots) === 28) {
            $bookedDates[] = $date;
        }
    }

    // Output the booked dates
    echo  json_encode($bookedDates);

    $stmt->close();
} else {
    // Handle missing room ID parameter
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Missing room ID parameter']);
}
?>
