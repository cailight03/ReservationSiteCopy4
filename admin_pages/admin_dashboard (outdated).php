<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel | Dashboard</title>

     <!--font awesome-->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

     <!--Iconsout CSS-->
     <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

    <!--css file-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dashboard (outdated).css"/>
  </head>

<body id="body-pd" class="body-pd">
<?php include 'navbarV2.php'; ?>  
<main id="main-content">
<section class="container">
    <section class="content">
      <main>
        <div class="head-title">
          <div class="left">
            <h1>Dashboard</h1>
            <ul class="breadcrumb">
              <li>
                <a href="#">Dashboard</a>
              </li>
              <i class="fas fa-chevron-right"></i>
              <li>
                <a href="#" class="active">Home</a>
              </li>
            </ul>
          </div>

          <a href="#" class="download-btn">
            <i class="fas fa-cloud-download-alt"></i>
            <span class="text">Download Report</span>
          </a>
        </div>

        <div class="box-info">
          <li>
            <i class="fas fa-calendar-check"></i>
            <span class="text">
              <h3>1.5K</h3>
              <p>Total Reservations</p>
            </span>
          </li>
          <li>
            <i class="fas fa-people-group"></i>
            <span class="text">
              <h3>1M</h3>
              <p>Most Reserved</p>
            </span>
          </li>
          <li>
            <i class="fas fa-people-group"></i>
            <span class="text">
              <h3>1M</h3>
              <p>Least Reserved</p>
            </span>
          </li>
          <li>
            <i class="fas fa-exclamation-triangle"></i>
            <span class="text">
              <h3>26</h3>
              <p>Pending Approvals</p>
            </span>
          </li>
        </div>

        <div class="table-data">
          <div class="order">
            <div class="head">
              <h3>Recent Activity</h3>
              <i class="fas fa-search"></i>
              <i class="fas fa-filter"></i>
            </div>

            <table>
              <thead>
                <tr>
                  <th>User</th>
                  <th>Reservation Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <img src="profile.png" alt="" />
                    <p>User Name</p>
                  </td>
                  <td>07-05-2023</td>
                  <td><span class="status pending">Pending</span></td>
                </tr>
                <tr>
                  <td>
                    <img src="profile.png" alt="" />
                    <p>User Name</p>
                  </td>
                  <td>07-05-2023</td>
                  <td><span class="status pending">Pending</span></td>
                </tr>
                <tr>
                  <td>
                    <img src="profile.png" alt="" />
                    <p>User Name</p>
                  </td>
                  <td>07-05-2023</td>
                  <td><span class="status process">Process</span></td>
                </tr>
                <tr>
                  <td>
                    <img src="profile.png" alt="" />
                    <p>User Name</p>
                  </td>
                  <td>07-05-2023</td>
                  <td><span class="status process">Process</span></td>
                </tr>
                <tr>
                  <td>
                    <img src="profile.png" alt="" />
                    <p>User Name</p>
                  </td>
                  <td>07-05-2023</td>
                  <td><span class="status complete">Complete</span></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="todo">
            <div class="head">
              <h3>Upcoming Reservations</h3>
              <i class="fas fa-plus"></i>
              <i class="fas fa-filter"></i>
            </div>

            <ul class="todo-list">
              <li class="not-completed">
                <p>Aquatic Zumba at Aquatic Center</p>
                <i class="fas fa-ellipsis-vertical"></i>
              </li>
              <li class="not-completed">
                <p>Inter-barangay Championship at Hoops Center</p>
                <i class="fas fa-ellipsis-vertical"></i>
              </li>
              <li class="completed">
                <p>Camping at Inspire Gym</p>
                <i class="fas fa-ellipsis-vertical"></i>
              </li>
              <li class="completed">
                <p>Sparring at Cafeteria</p>
                <i class="fas fa-ellipsis-vertical"></i>
              </li>
              <li class="completed">
                <p>Tongits Battle at Room 302</p>
                <i class="fas fa-ellipsis-vertical"></i>
              </li>
            </ul>
          </div>
          </main>
    </section>
    <script>   
  function toggleNav() {
    document.querySelector('.body-pd').classList.toggle('move-right');
    // Existing code to show/hide the sidebar
  }

  function openNav() {
    document.getElementById("nav-bar").style.width = "250px";
    document.getElementById("body-pd").classList.add("move-right");
  }

  function closeNav() {
    document.getElementById("nav-bar").style.width = "0";
    document.getElementById("body-pd").classList.remove("move-right");
  }

  // Add event listener to header-toggle
  document.getElementById("header-toggle").addEventListener("click", toggleNav);
</script>
  </body>
</html>
