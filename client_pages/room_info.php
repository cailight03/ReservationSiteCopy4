<?php
include '../config/connection.php';

session_start();




// Fetch room data based on the selected room ID
if (isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Fetch room data based on the selected room ID
    $roomInfoQuery = "SELECT room_name, room_description, room_img_path, category_id FROM rooms WHERE id = $roomId";
    $roomInfoResult = $connection->query($roomInfoQuery);

    if ($roomInfoResult) {
        $roomInfoData = $roomInfoResult->fetch_assoc();
        $roomName = $roomInfoData['room_name'];
        $roomDescription = $roomInfoData['room_description'];
        $roomImgPath = $roomInfoData['room_img_path'];
        $pageTitle = "$roomName";
        $categoryId =  $roomInfoData["category_id"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Room Information'; ?></title>
    <link rel="icon" type="image/svg+xml" href="../img/login_img/NU_shield.svg">
    <link rel="stylesheet" href="..\css\UI_Pages.css">
   
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    

    <style>
    .highlight-range {
        background-color: #e0f7fa; /* Set your desired highlight color */
    }
    
       .required {
        color: red;
        margin-left: 3px;
    }

    .custom-select {
    height: 17rem;
    min-height: 3rem; /* Adjust this value as needed */
}

.custom-select2 {
    height: 9rem;
    min-height: 3rem; /* Adjust this value as needed */
}




.custom-select option:disabled {
    color: red;
    cursor: not-allowed;
}

.custom-select2  option:disabled {
    color: red;
    cursor: not-allowed;
}
</style>



</head>
<body>
<!-- Navbar -->
<?php include "navbar.php"; ?>



<section class="container gallery-container ">

<div class="pb-3">
        <a href="javascript:history.go(-1);" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Back
        </a>
</div>

    <h2 class="room-info-header">Room Information</h2>
    

   


<div id="roomCarousel" class="carousel slide" data-bs-theme="dark">
    <div class="carousel-inner">
        <?php

         // Define the path to the room images folder
    $roomImagesPath = "$roomImgPath";

    // Get a list of image files in the folder
    $images = glob($roomImagesPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        // Display room images in the carousel
        foreach ($images as $index => $image) {
            echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '">';
            echo '<img src="' . $image . '" class="d-block w-100" alt="Room Image ' . ($index + 1) . '">';
            echo '</div>';
        }
        ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

    <div class="container room-info-container">
        <div class="row">
            <div class="col-sm-8">
                <h2><?php echo $roomName; ?></h2>
                <p class="text-left"><?php echo $roomDescription; ?></p>
            </div>
            <div class="col-sm-4 text-end" >
                <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Reserve Now!
</button>


            </div>

            <div class="alert-container" style="display: none;"></div>
           

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

<!-- Form Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1"  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Request Form</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <!--  REQUEST FORM -->
            <form id="myForm" action="../vendor/send-to-signatory.php" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmission(event)">

            <input type="hidden" name="room_id" value="<?php echo isset($roomId) ? $roomId : ''; ?>">
            <input type="hidden" name="category_id" value="<?php echo isset($categoryId) ? $categoryId : ''; ?>">
<input type="hidden" name="room_name" value="<?php echo isset($roomName) ? $roomName : ''; ?>">
    <div class="row">
    <div class="col-6 mb-3">
       
    <label for="fullName" class="form-label"> Full Name <span class="required">*</span></label>
        <input  class="form-control" id="fullName" name="fullName" required>
    </div>
    <div class="col-6 mb-3">
        <label for="email" class="form-label">E-mail <span class="required">*</span></label>
     <input  type="email" class="form-control" id="email" name="email" required >
      </div>
      <div class="mb-3">
    <label for="usertype" class="form-label">Select User Type: <span class="required">*</span></label>
    <select class="form-select" id="usertype" name="userType" required onchange="toggleCollegeDiv(this.value)">
        <option value="" disabled selected>Select One</option>
        <option value="student">Student</option>
        <option value="employee">Employee</option>
        <option value="admin">Admin</option>
    </select>
</div>

<div class="mb-3" id="collegeDiv">
    <label for="college" class="form-label">College<span class="required">*</span></label>
    <select class="form-select" aria-label="Default select example" id="college" name="college" required>
        <option value="" disabled selected>Select One</option>
        <?php
        // Fetch colleges from the database
        $query = "SELECT name FROM colleges";
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $collegeName = $row['name'];
                echo "<option value='$collegeName'>$collegeName</option>";
            }
        } else {
            echo "<option disabled>No colleges found</option>";
        }
        ?>
    </select>
</div>

<div class="mb-3" id="departmentDiv" style="display:none;">
    <label for="department" class="form-label">Department<span class="required">*</span></label>
    <input type="text" class="form-control" id="department" name="college">
</div>


<div class="mb-3" id="orgDiv">
    <label for="org" class="form-label">Organization</label>
    <select class="form-select" aria-label="Default select example" id="org" name="org" required>
        <option  selected>None</option>
        <?php
        // Fetch colleges from the database
        $query = "SELECT name FROM organizations";
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $orgName = $row['name'];
                echo "<option value='$orgName'>$orgName</option>";
            }
        } else {
            echo "<option disabled>No colleges found</option>";
        }
        ?>
    </select>
</div>
<div class="mb-3" id="adviserEmailContainer">
        <label for="adviserEmail" class="form-label">Adviser's E-mail <span class="required">*</span></label>
     <input  class="form-control" id="adviserEmail" name="adviserEmail" required >
      </div>

        
    
    <hr>
    <h4>Activity Details</h4>
    <div class="mb-3">
    <label for="activityType" class="form-label">Activity Type<span class="required">*</span></label>
    <select class="form-select" aria-label="Default select example" id="activityType" name="activityType" required>
        <option value="" disabled selected>Select One</option>
        <option value="Course Activity">Course Activity</option>
        <option value="Org Activity">Org Activity</option>
        <option value="Event">Event</option>
    </select>
</div>
    <div class="mb-3">
        <label for="activityName" class="form-label">Activity Name <span class="required">*</span></label>
        <input type="text" class="form-control" id="activityName" name="activityName" required>
    </div>
    <div class="mb-3">
        <label for="numOfAttendees" class="form-label">No. of Attendees <span class="required">*</span></label>
        <input type="number" class="form-control" id="numOfAttendees" name="numOfAttendees" required min="1"  value="1">
    </div>
    <div class="mb-3">
        <label for="datepicker" class="form-label">Date(s) <span class="required">*</span></label>
        <input class="form-control" type="text" id="datepicker" name="reservation-date1" required>
        <small class="text-secondary">Select the same date twice for one day. Select two different dates for multiple days.</small>
    </div>

    <input type="hidden" id="reservation-date" name="reservation-date" />


    <!-- time slot -->
   
    <!-- time slot for single date -->
    
<div id="timeSlot" class="mb-3">
    <label class="form-label" for="timeSlotSingle" id="timeSlotSingleLabel">Select Time:</label>
    <select id="timeSlotSingle" class="form-control custom-select" multiple required></select>
    <small class="text-secondary" id="small-text">Click for one time slot. Click and drag for multiple time slots.</small>
    <br>
    <label class="form-label" for="selectedTimeSingle" id="selectedTimeSingleLabel">Selected Time Range: <span class="required">*</span></label>
    <input type="text" id="selectedTimeSingle" class="form-control" readonly name="time-slot" required>
</div>


<!-- time slots for multiple dates -->
<div id="timeSlotMultiDateContainer" style="display: none;">
    <!-- Dynamically generated time slot inputs will be added here -->
</div>
<input type="hidden" id="hiddenTimeRanges" name="time-slot" />


   
 
    <div class="mb-3">
        <label for="speakerName" class="form-label">Speaker's Name</label>
        <input type="text" class="form-control" id="speakerName" name="speakerName">
    </div>
    <hr>
    <h4>Items Needed</h4>
    <div class="row">
        <div class="col-6 mb-3">
            <label for="items" class="form-label">Select Items</label>
            <select class="form-select" aria-label="Default select example" id="items" onchange="toggleOthersInput()" name="selectedItem">
                <option disabled selected>Choose Item</option>
                <option value="table">Table</option>
                <option value="chairs">Chairs</option>
                <option value="bulletin-board">Bulletin Board</option>
                <option value="sound-system">Sound System</option>
                <option value="flag">Flag</option>
                <option value="others">Others</option>
            </select>
        </div>
        <div class="col-6 mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <div class="d-flex">
                <div class="row">
                <div class="col-8">
    <input type="number" class="form-control pr-3" id="quantity" name="quantity" min="1"  value="1">
</div>
                    <div class="col-3">
                        <button type="button" class="btn btn-primary addBtn">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3" id="othersInputContainer" style="display: none;">
        <label for="othersInput" class="form-label">Specify Other Item</label>
        <input type="text" class="form-control" id="othersInput" name="othersInput">
    </div>
    <div class="mb-3">
        <p>Selected Items:</p>
        <ul id="selectedItemsList"></ul>
    </div>

    <input class="d-none" name="selectedItems" id="selectedItemsInput" />

    <div class="mb-3">
    <label for="fileUpload" class="form-label">Upload Photo of School ID <span class="required">*</span></label>
    <input type="file" class="form-control" id="fileUpload" name="fileUpload" accept="image/*" required>
</div>

    <div class="mb-3">
    <label for="remarks" class="form-label">Remarks</label>
    <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>

</div>
<div class="text-end">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary" >Submit</button>
</div>
</form>

        </div>
      </div>
    </div>
  </div>
</div>





<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Include flatpickr and Bootstrap at the end of the body -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        // Get the room_id from the PHP script
        const roomId = <?php echo $roomId; ?>;

        // Fetch booked dates for the specific room
        const bookedDates = await getBookedDates(roomId);

        // Initialize flatpickr
        flatpickr("#datepicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            minDate: new Date().fp_incr(4),
            disable: [
                {
                    from: "today",
                    to: "today",
                },
                // Include your booked dates
                ...bookedDates,
            ],
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length >= 1) {
                    // Valid date range selected
                    // Extract individual dates from the range
                    const dateIntervals = getDatesBetween(selectedDates[0], selectedDates[selectedDates.length - 1]);

                    // Update the hidden input with the date intervals
                    document.getElementById('reservation-date').value = dateIntervals.join(',');

                    // Display the appropriate time slot input based on the number of selected dates
                    if (dateIntervals.length === 1) {
                        removeMultiDateInputs ()
                        $('#timeSlotSingle').show();
                        $('#selectedTimeSingle').show();
                        $('#selectedTimeSingleLabel').show();
                        $('#timeSlotSingleLabel').show();
                        $('#small-text').show();
                        $('#timeSlotSingle').attr('required', true);
                        $('#selectedTimeSingle').attr('required', true);
                        $('#timeSlotMultiDateContainer').hide();
                        populateMultiDateTimeSlots('timeSlotSingle', dateIntervals[0]);
                    } else {
                        createMultiDateInputs(dateIntervals);
                        $('#timeSlotSingle').hide();
                        $('#selectedTimeSingle').hide();
                        $('#small-text').hide();
                        $('#timeSlotSingle').removeAttr('required');
                        $('#selectedTimeSingle').removeAttr('required');
                        $('#timeSlotMultiDateContainer').show();
                        dateIntervals.forEach(date => {
                            populateMultiDateTimeSlots(`timeSlot-${date}`, date);
                            addLabelForDate(`timeSlot-${date}`, date);
                            addSelectedTimeRangeAfterContainer(`timeSlot-${date}`);
                        });
                        $('#selectedTimeSingleLabel').hide();
                        $('#timeSlotSingleLabel').hide();
                    }

                    // Use a different method to open the modal
                    $('#exampleModal').modal('show');
                } else {
                    alert('Please select a valid date range.');
                }
            }
        });

    } catch (error) {
        console.error('Error fetching and initializing:', error);
    }
});

function removeMultiDateInputs () {
    const hiddenTimeRangesInput = document.getElementById('hiddenTimeRanges');
const multiDateInputs = document.querySelectorAll('[id^="selectedTime-"]');
multiDateInputs.forEach(input => input.remove())
// Clear the hidden time ranges when the date is changed
hiddenTimeRangesInput.value = '';

}



function addSelectedTimeRangeAfterContainer(containerId) {
    // Create a div element for the selected time range
    const selectedTimeRangeDiv = document.createElement('div');
    selectedTimeRangeDiv.className = 'mb-3';
    selectedTimeRangeDiv.innerHTML = '<label for="selectedTime" class="form-label">Selected Time Range: <span class="required">*</span></label>' +
        '<input type="text" id="selectedTime-' + containerId + '" class="form-control" readonly name="time-slot" required>';

    // Append the div after the container
    document.getElementById(containerId).insertAdjacentElement('afterend', selectedTimeRangeDiv);
}


function getDatesBetween(startDate, endDate) {
    const dates = [];
    let currentDate = new Date(startDate);

    while (currentDate <= endDate) {
        currentDate.setDate(currentDate.getDate() + 1);
        dates.push(currentDate.toISOString().split('T')[0]); // Format as YYYY-MM-DD
    }

    return dates;
}

// Function to get booked dates from get_booked_dates.php
async function getBookedDates(roomId) {
    try {
        const response = await fetch(`controller/get_booked_dates.php?room_id=${roomId}`);
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


function combineTimeRanges(ranges) {
    const startTime = ranges[0].split('-')[0];
    const endTime = ranges[ranges.length - 1].split('-')[1];

    return `${startTime}-${endTime}`;
}




// Function to populate time slots for a specific date
async function populateMultiDateTimeSlots(selectId, date) {
    try {
        const roomId = <?php echo $roomId; ?>;
        const response = await fetch(`controller/get_booked_time_slots.php?room_id=${roomId}&date=${date}`);
        if (!response.ok) {
            throw new Error(`Failed to fetch booked time slots for ${date}`);
        }
        const bookedTimeSlots = await response.json();
        const selectElement = document.getElementById(selectId);
        const selectedTimeInput = document.getElementById(`selectedTime-${selectId}`);
        selectElement.innerHTML = '';
        const startHour = 7;
        const endHour = 21;
        const interval = 30;

        for (let hour = startHour; hour < endHour; hour++) {
            for (let minute = 0; minute < 60; minute += interval) {
                const formattedHour = hour % 12 || 12;
                const ampm = hour < 12 ? 'AM' : 'PM';
                const nextHour = (hour + Math.floor((minute + interval) / 60)) % 12 || 12;
                const nextAmpm = (hour + Math.floor((minute + interval) / 60)) < 12 ? 'AM' : 'PM';
                const option = document.createElement('option');
                const timeSlot = `${formattedHour}:${String(minute).padStart(2, '0')} ${ampm}-${nextHour}:${String((minute + interval) % 60).padStart(2, '0')} ${nextAmpm}`;

                option.value = timeSlot;
                option.text = timeSlot;
                option.disabled = bookedTimeSlots.includes(timeSlot);

                selectElement.add(option);
            }
        }

        // Update the selected time input when a time slot is chosen
        document.getElementById('timeSlotSingle').addEventListener('change', updateHiddenTimeRanges);
        



        selectElement.addEventListener('change', updateMultiSelectedTime);
        

    function updateHiddenTimeRanges() {
    const multiDateInputs = document.querySelectorAll('[id^="selectedTime-"]');
    const hiddenTimeRangesInput = document.getElementById('hiddenTimeRanges');
    const selectedTimeSingle = document.getElementById('selectedTimeSingle');

    

    if (multiDateInputs.length > 0) {
        // Extract selected time ranges from each input for multiple dates
        const selectedTimeRanges = Array.from(multiDateInputs).map(input => input.value);
        hiddenTimeRangesInput.value = selectedTimeRanges.join(',');
    } else {
        
        // Extract selected time range from the single date input
        hiddenTimeRangesInput.value = selectedTimeSingle.value;
    }
    console.log(multiDateInputs.length);
    console.log('Hidden Time Ranges:', hiddenTimeRangesInput.value); 
}

        function updateMultiSelectedTime() {
    const selectedOptions = Array.from(selectElement.selectedOptions);
    const selectedRanges = selectedOptions.map(option => option.value);
    const combinedRange = combineTimeRanges(selectedRanges);
    const selectedTimeSingle = document.getElementById('selectedTimeSingle');

    // Check if the selectedTimeInput element is available
    if (selectedTimeInput) {
        selectedTimeInput.value = combinedRange;
        updateHiddenTimeRanges();
        console.log('Selected Time Range:', combinedRange);
    } else {
        selectedTimeSingle.value = combinedRange;
       console.log('Selected Time Range:', combinedRange);
    }
}
    } catch (error) {
        console.error(`Error populating time slots for ${date}:`, error);
    }
}

function createMultiDateInputs(dateIntervals) {
    const container = document.getElementById('timeSlotMultiDateContainer');
    container.innerHTML = '';
    dateIntervals.forEach(date => {
        const timeSlotId = `timeSlot-${date}`;
        const timeSlotInput = document.createElement('select');
        timeSlotInput.id = timeSlotId;
        timeSlotInput.className = 'form-control custom-select2 mb-3';
        timeSlotInput.multiple = true;
        timeSlotInput.required = true;
        container.appendChild(timeSlotInput);
    });
}

function addLabelForDate(selectId, date) {
    const container = document.getElementById('timeSlotMultiDateContainer');

    // Create label element
    const label = document.createElement('label');
    label.htmlFor = selectId;
    label.className = 'form-label';
    label.textContent = `Select time for ${date}`;

    // Find the select element based on its ID
    const selectElement = document.getElementById(selectId);

    // Insert the label before the select element
    container.insertBefore(label, selectElement);
}
</script>









<!-- form script -->

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var addButton = document.querySelector(".addBtn");
    addButton.addEventListener("click", addItemToList);
  });

  function toggleOthersInput() {
    var othersInputContainer = document.getElementById("othersInputContainer");
    var selectedItem = document.getElementById("items").value;

    // Toggle the visibility of the input based on the selected item
    if (selectedItem === "others") {
      othersInputContainer.style.display = "block";
    } else {
      othersInputContainer.style.display = "none";
    }
  }

  function addItemToList() {
    var selectedItem = document.getElementById("items").value;
    var quantity = document.getElementById("quantity").value;
    var othersInputValue = document.getElementById("othersInput").value;

    // Check if the selected item is "Choose Item" or if the quantity is empty
    if (selectedItem === "Choose Item" || quantity.trim() === "") {
      alert("Please select a valid item and quantity.");
      return;
    }

    // Use the value from othersInput if "others" is selected
    var displayText = selectedItem === "others" ? othersInputValue : selectedItem;

    if (selectedItem === "others" && othersInputValue.trim() === "") {
      alert("Please specify the other item.");
      return;
    }

    var selectedItemsList = document.getElementById("selectedItemsList");
    var listItem = document.createElement("li");

    // Append quantity information
    listItem.textContent = displayText + " " + quantity;

    // Add data attribute to store the selected item
    listItem.setAttribute("data-item", displayText);

    // Add delete button (x) to the list item
    var deleteButton = document.createElement("span");
    deleteButton.innerHTML = "&times;"; // HTML character code for 'x'
    deleteButton.style.cursor = "pointer";
    deleteButton.addEventListener("click", function () {
      selectedItemsList.removeChild(listItem);
      updateHiddenInput(); // Update selectedItemsInput after removal
    });

    listItem.appendChild(deleteButton);
    selectedItemsList.appendChild(listItem);

    // Clear the input fields after adding the item to the list
    document.getElementById("items").value = "Choose Item";
    document.getElementById("quantity").value = "1";
    document.getElementById("othersInput").value = "";

    // Hide the others input container after adding the item
    document.getElementById("othersInputContainer").style.display = "none";

    // Update the hidden input field with the selected items JSON
    updateHiddenInput();
  }

  function updateHiddenInput() {
    var selectedItems = [];
    var listItems = document.querySelectorAll("#selectedItemsList li");
    
    listItems.forEach(function (item) {
      var itemName = item.getAttribute("data-item");

      // Extract numeric quantity (exclude non-numeric characters)
      var itemQuantity = item.textContent.trim().split(" ").pop().replace(/\D/g, '');

      selectedItems.push({ item: itemName, quantity: itemQuantity });
    });

    // Update the hidden input field with the JSON string
    document.getElementById("selectedItemsInput").value = JSON.stringify(selectedItems);
  }
</script>


<script>
    // Function to handle form submission
    function handleFormSubmission(event) {
        // Prevent the form from submitting in the traditional way
        event.preventDefault();

        var fullName = document.getElementById('fullName').value;
    var speakerName = document.getElementById('speakerName').value;

    if (!validateFullName(fullName)) {
        alert('Please enter a valid full name with both first and last names separated by a space.');
        return false;
    }

    if (speakerName.trim() !== '' && !validateFullName(speakerName)) {
        alert('Please enter a valid speaker\'s name with both first and last names separated by a space, or leave it blank.');
        return false;
    }

    var numOfAttendees = parseInt(document.getElementById('numOfAttendees').value);
    var maxAttendees = parseInt(document.getElementById('numOfAttendees').getAttribute('max'));

    if (numOfAttendees > maxAttendees) {
        alert('The number of attendees exceeds the maximum allowed.');
        return false;
    }

        var modal = document.getElementById('exampleModal');
        var bootstrapModal = bootstrap.Modal.getInstance(modal);
        bootstrapModal.hide();

        // Show the loading spinner modal
        var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'), { backdrop: 'static', keyboard: false });
        loadingModal.show();

        // Perform an AJAX submission of the form
        var formData = new FormData(document.getElementById('myForm'));

        // Fetch to the server endpoint for form submission
        fetch('../vendor/send-to-signatory.php', {
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
                // Dismiss the modal
        
            } else {
                alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">' + data + '</div>';
            }
            alertContainer.style.display = 'block';

            // Dismiss the loading spinner modal using Bootstrap modal method
            loadingModal.hide();

            // Reopen the main modal using JavaScript
            document.getElementById('exampleModal').removeAttribute('aria-hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            var alertContainer = document.querySelector('.alert-container');
            alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">Failed to submit request. Please try again.</div>';
            alertContainer.style.display = 'block';

            // Dismiss the loading spinner modal using Bootstrap modal method
            loadingModal.hide();

            // Reopen the main modal using JavaScript
            document.getElementById('exampleModal').removeAttribute('aria-hidden');
        });
    }
</script>
<script>
          function validateFullName(name) {
            var fullNamePattern = /^[A-Z][a-z]{1,}\s[A-Z][a-z]{1,}$/;
            
            return fullNamePattern.test(name);
        }

    </script>



<script>
    document.getElementById('usertype').addEventListener('change', function () {
    var selectedOption = this.value;
    var adviserEmailContainer = document.getElementById('adviserEmailContainer');
    var adviserEmailInput = document.getElementById('adviserEmail');

    if (selectedOption === 'employee' || selectedOption === 'admin') {
        adviserEmailContainer.style.display = 'none';
        adviserEmailInput.removeAttribute('required');
    } else {
        adviserEmailContainer.style.display = 'block';
        adviserEmailInput.setAttribute('required', 'required');
    }
});
</script>




<!-- time slot -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Call the function to populate time slots on page load
    populateTimeSlots();
});

function populateTimeSlots() {
    const startHour = 7; // 7:00 AM
    const endHour = 21; // 9:00 PM
    const interval = 30; // 30 minutes interval

    const selectElement = document.getElementById('timeSlotSingle');
    const selectedTimeInput = document.getElementById('selectedTimeSingle');
    const roomId = <?php echo isset($roomId) ? $roomId : 'null'; ?>; // Pass the room ID from PHP
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

        fetch(`controller/get_booked_time_slots.php?room_id=${roomId}&date=${selectedDate}`)
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
</script>

<script>
    // Assuming categoryId is obtained from the server-side code or another source
var categoryId = <?php echo $categoryId; ?>; // Example value, replace this with the actual categoryId

// Function to set the maximum number based on the category id
function setMaxNumberOfAttendees(categoryId) {
    var numOfAttendeesInput = document.getElementById("numOfAttendees");
    switch(categoryId) {
        case 1:
            numOfAttendeesInput.setAttribute("max", "40");
            break;
        case 2:
            numOfAttendeesInput.setAttribute("max", "20");
            break;
        case 3:
            numOfAttendeesInput.setAttribute("max", "100");
            break;
        default:
            // Handle any other cases if needed
            break;
    }
}

// Call the function with the categoryId to set the maximum number
setMaxNumberOfAttendees(categoryId);

</script>

<script>
    function toggleCollegeDiv(userType) {
        var collegeDiv = document.getElementById("collegeDiv");
        var departmentDiv = document.getElementById("departmentDiv");

        if (userType === "admin") {
            collegeDiv.style.display = "none";
            document.getElementById("college").removeAttribute("required");
            departmentDiv.style.display = "block";
            document.getElementById("department").setAttribute("required", "required");
        } else {
            collegeDiv.style.display = "block";
            document.getElementById("college").setAttribute("required", "required");
            departmentDiv.style.display = "none";
            document.getElementById("department").removeAttribute("required");
        }
    }
</script>

</body>
</html>