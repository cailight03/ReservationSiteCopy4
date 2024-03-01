<?php
include '../config/connection.php';

session_start();


// Fetch vehicle data based on the selected vehicle ID
if (isset($_GET['vehicle_id'])) {
    $vehicleId = $_GET['vehicle_id'];

    // Fetch vehicle data based on the selected vehicle ID
    $vehicleInfoQuery = "SELECT vehicle_name, vehicle_description, vehicle_img_path FROM vehicles WHERE id = $vehicleId";
    $vehicleInfoResult = $connection->query($vehicleInfoQuery);

    if ($vehicleInfoResult) {
        $vehicleInfoData = $vehicleInfoResult->fetch_assoc();
        $vehicleName = $vehicleInfoData['vehicle_name'];
        $vehicleDescription = $vehicleInfoData['vehicle_description'];
        $vehicleImgPath = $vehicleInfoData['vehicle_img_path'];
        $pageTitle = "$vehicleName";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Vehicle Information'; ?></title>
    <link rel="icon" type="image/svg+xml" href="../img/login_img/NU_shield.svg">
    <link rel="stylesheet" href="..\css\UI_Pages.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

   <style>
    .required {
        color: red;
        margin-left: 3px;
    }

    .custom-select {
    height: 17rem;
    min-height: 3rem; /* Adjust this value as needed */
}

.custom-select option:disabled {
    color: red;
}
   </style>


</head>
<body>
    <!-- Navbar -->
    <?php include "navbar.php"; ?>

    <section class="container gallery-container">
        <div class="pb-3">
            <a href="javascript:history.go(-1);" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <h2 class="vehicle-info-header">Vehicle Information</h2>

        <div id="vehicleCarousel" class="carousel slide" data-bs-theme="dark">
            <div class="carousel-indicators">
                <?php
                // Get a list of image files in the folder
                $images = glob($vehicleImgPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

                // Display indicators
                foreach ($images as $index => $image) {
                    echo '<button type="button" data-bs-target="#vehicleCarousel" data-bs-slide-to="' . $index . '" class="' . ($index === 0 ? 'active' : '') . '"></button>';
                }
                ?>
            </div>

            <div class="carousel-inner">
                <?php
                // Display vehicle images in the carousel
                foreach ($images as $index => $image) {
                    echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '">';
                    echo '<img src="' . $image . '" class="d-block w-100 rounded-4" style="max-width: 100%; height: 864px;" alt="Vehicle Image ' . ($index + 1) . '">';
                    echo '</div>';
                }
                ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="container vehicle-info-container">
            <div class="row">
                <div class="col-sm-8">
                    <h2><?php echo $vehicleName; ?></h2>
                    <p class="text-left"><?php echo $vehicleDescription; ?></p>
                </div>
                <div class="col-sm-4 text-end">
                      <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleModal">
  Reserve Now!
</button>


            </div>

            <div class="alert-container" style="display: none;"></div>
           

        </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="vehicleModal" tabindex="-1"  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="vehicleModalLabel">Request Form</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <!--  REQUEST FORM -->
            <form id="myForm" action="../vendor/vehicle_sendmail.php" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmission(event)">

            <input type="hidden" name="vehicle_id" value="<?php echo isset($vehicleId) ? $vehicleId : ''; ?>">
<input type="hidden" name="vehicle_name" value="<?php echo isset($vehicleName) ? $vehicleName : ''; ?>">
    <div class="row">
    <div class="col-6 mb-3">
       
    <label for="fullName" class="form-label"> Full Name <span class="required">*</span></label>
        <input  class="form-control" id="fullName" name="fullName" required>
    </div>
    <div class="col-6 mb-3">
        <label for="userEmail" class="form-label">E-mail <span class="required">*</span></label>
     <input  type="email" class="form-control" id="userEmail" name="userEmail" required >
      </div>
      
        <div class="mb-3">
            <label for="office" class="form-label">Office <span class="required">*</span></label>
            <select class="form-select" aria-label="Default select example" id="office" name="office" required>
                <option disabled selected >Select One</option>
                <option value="SCS">SCS</option>
                <option value="SAS">SAS</option>
                <option value="SEA">SEA</option>
                <option value="SABM">SABM</option>
            </select>
        </div>
        
    
    <hr>
    <h4>Request Details</h4>
   
    <div class="mb-3">
        <label for="purpose" class="form-label">Purpose <span class="required">*</span></label>
        <input type="text" class="form-control" id="purpose" name="purpose" required>
    </div>
    <div class="mb-3">
        <label for="numOfPassengers" class="form-label">No. of Passengers <span class="required">*</span></label>
        <input type="number" class="form-control" id="numOfPassengers" name="numOfPassengers" required min="1"  value="1" max="15">
    </div>
    <div class="mb-3">
        <label for="datepicker" class="form-label">Date(s) <span class="required">*</span></label>
        <input class="form-control" type="text" id="datepicker" name="reservation-date" required>
        
    </div>


    <!-- time slot -->
   
    <div class="mb-3">
      <label for="timeSlot">Select Time:</label>
      <select id="timeSlot" class="form-control custom-select" multiple  required > </select>
      <small class="text-secondary">Click for one time slot. Click and drag for multiple time slots.</small>
      <br>
      <label for="selectedTime">Selected Time Range: <span class="required">*</span></label>
      <input type="text" id="selectedTime" class="form-control" readonly name="time-slot" required>
     
      
    </div>
 
    <div class="mb-3">
        <label for="destination" class="form-label">Destination <span class="required">*</span></label>
        <input type="text" class="form-control" id="destination" name="destination" required>
    </div>
    <hr>
    <div class="mb-3">
    <label for="fileUpload" class="form-label">Upload Photo of School ID <span class="required">*</span></label>
    <input type="file" class="form-control" id="fileUpload" name="fileUpload" accept="image/*" required>
</div>

    
<div class="text-end">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary">Submit</button>
</div>
</form>

        </div>
      </div>

      

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        // Get the room_id from the PHP script
        const vehicleId = <?php echo $vehicleId; ?>;

        // Fetch booked dates for the specific room
        const bookedDates = await getBookedDates(vehicleId);

        // Log the booked dates to the console
        console.log('Booked Dates:', bookedDates);

        // Initialize flatpickr
        flatpickr("#datepicker", {
            mode: "single",
            dateFormat: "Y-m-d",
            minDate: new Date().fp_incr(4),
            disable: [
                {
                    from: "today",
                    to: "today",
                },
                // Include your booked dates
                ...bookedDates,
            ]
        });
    } catch (error) {
        console.error('Error fetching and initializing:', error);
    }
});
// Function to get booked dates from get_booked_dates.php
async function getBookedDates(vehicleId) {
    try {
        const response = await fetch(`controller/vehicle_booked_dates.php?vehicle_id=${vehicleId}`);
        if (!response.ok) {
            throw new Error('Failed to fetch booked dates');
        }
        const bookedDates = await response.json();
        return bookedDates;
    } catch (error) {
        console.error('Error fetching booked dates:', error);
        return [];
    }
}

    

document.addEventListener("DOMContentLoaded", function () {
    // Call the function to populate time slots on page load
    populateTimeSlots();
});

function populateTimeSlots() {
    const startHour = 7; // 7:00 AM
    const endHour = 21; // 9:00 PM
    const interval = 30; // 30 minutes interval

    const selectElement = document.getElementById('timeSlot');
    const selectedTimeInput = document.getElementById('selectedTime');
    const vehicleId = <?php echo isset($vehicleId) ? $vehicleId : 'null'; ?>; // Pass the room ID from PHP
    const datePicker = document.getElementById('datepicker');

    selectElement.addEventListener('change', updateSelectedTime);

    function updateSelectedTime() {
        const selectedOptions = Array.from(selectElement.selectedOptions);
        const selectedRanges = selectedOptions.map(option => option.value);
        const combinedRange = combineTimeRanges(selectedRanges);

        selectedTimeInput.value = combinedRange;
    }

    function combineTimeRanges(ranges) {
        const startTime = ranges[0].split('-')[0];
        const endTime = ranges[ranges.length - 1].split('-')[1];

        return `${startTime}-${endTime}`;
    }

    // Fetch booked time slots only when a date is selected
    function getBookedTimeSlots() {
    const selectedDate = datePicker.value;

    if (selectedDate) {
        // Clear the selected options when the date changes
        selectElement.selectedIndex = -1;
        selectedTimeInput.value = '';

        fetch(`controller/vehicle_booked_time_slots.php?vehicle_id=${vehicleId}&date=${selectedDate}`)
            .then(response => response.json())
            .then(bookedTimeSlots => {
                console.log('Booked Time Slots:', bookedTimeSlots); // Print booked time slots to the console
                disableBookedTimeSlots(bookedTimeSlots);
            })
            .catch(error => console.error('Error fetching booked time slots:', error));
    }
}


    // Disable booked time slots in the select element
    function disableBookedTimeSlots(bookedTimeSlots) {
        const options = selectElement.options;

        for (let i = 0; i < options.length; i++) {
            const optionValue = options[i].value;

            // Disable the option if it is in the bookedTimeSlots array
            options[i].disabled = bookedTimeSlots.includes(optionValue);
        }
    }

    datePicker.addEventListener('change', getBookedTimeSlots);

    for (let hour = startHour; hour < endHour; hour++) {
        for (let minute = 0; minute < 60; minute += interval) {
            const formattedHour = hour % 12 || 12; // Convert to 12-hour format
            const ampm = hour < 12 ? 'AM' : 'PM';
            const nextHour = (hour + Math.floor((minute + interval) / 60)) % 12 || 12;
            const nextAmpm = (hour + Math.floor((minute + interval) / 60)) < 12 ? 'AM' : 'PM';
            const option = document.createElement('option');
            const timeSlot = `${formattedHour}:${String(minute).padStart(2, '0')} ${ampm}-${nextHour}:${String((minute + interval) % 60).padStart(2, '0')} ${nextAmpm}`;

            option.value = timeSlot;
            option.text = timeSlot;

            selectElement.add(option);
        }
    }
}

function handleFormSubmission(event) {
        // Prevent the form from submitting in the traditional way
        event.preventDefault();

        var fullName = document.getElementById('fullName').value;
    

    if (!validateFullName(fullName)) {
        alert('Please enter a valid full name with both first and last names separated by a space.');
        return false;
    }

    var modal = document.getElementById('vehicleModal');
        var bootstrapModal = bootstrap.Modal.getInstance(modal);
        bootstrapModal.hide();

        // Show the loading spinner modal
        var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'), { backdrop: 'static', keyboard: false });
        loadingModal.show();

        // Perform an AJAX submission of the form
        var formData = new FormData(document.getElementById('myForm'));

        // Fetch to the server endpoint for form submission
        fetch('../vendor/vehicle_send_signatory1.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Display the Bootstrap alert based on the response from the server
            var alertContainer = document.querySelector('.alert-container');
            if (data.includes('Your request has been submitted.')) {
                alertContainer.innerHTML = '<div class="alert alert-success" role="alert">' + data + '</div>';

                // Clear the form after successful submission
                document.getElementById('myForm').reset();
            } else {
                alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">' + data + '</div>';
            }
            alertContainer.style.display = 'block';

            // Dismiss the loading spinner modal using Bootstrap modal method
            loadingModal.hide();

            // Reopen the main modal using JavaScript
            document.getElementById('vehicleModal').removeAttribute('aria-hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            var alertContainer = document.querySelector('.alert-container');
            alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">Failed to submit request. Please try again.</div>';
            alertContainer.style.display = 'block';

            // Dismiss the loading spinner modal using Bootstrap modal method
            loadingModal.hide();

            // Reopen the main modal using JavaScript
            document.getElementById('vehicleModal').removeAttribute('aria-hidden');
        });
    }
</script>
<script>
          function validateFullName(name) {
            var fullNamePattern = /^[A-Z][a-z]{1,}\s[A-Z][a-z]{1,}$/;
            
            return fullNamePattern.test(name);
        }

    </script>





</body>
</html>
