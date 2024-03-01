<?php 
  session_start();
  if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) { 

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="img/login_img/NU_shield.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Login Page | NU-L Reservation</title>
</head>
<body>

    <!----------------------- Main Container -------------------------->

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <!----------------------- Login Container -------------------------->

        <div class="row border rounded-5 p-3 bg-white shadow box-area">

            <!--------------------------- Left Box ----------------------------->

            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #103cbe;">
                <div class="featured-image mb-3">
                    <img src="img/login_img/NU_shield.svg" class="img-fluid" style="width: 250px;">
                </div>
                <p class=" fs-2" style="color:rgb(255, 251, 0);font-family: 'Courier New', Courier, monospace; font-weight: 600;">Education that works</p>
                <small class="text-white text-wrap text-center" style="width: 17rem;font-family: 'Courier New', Courier, monospace;">NU-L Reservation is the official booking website of NU Laguna</small>
            </div> 

            <!-------------------- ------ Right Box ---------------------------->

            <div class="col-md-6 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h2>Log In</h2>
                        <p>with your NU e-mail</p>
                    </div>
                    <form action="login_logout_controller/auth.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Email address" required value="<?php if(isset($_GET['email']))echo(htmlspecialchars($_GET['email'])) ?>" name="email">
                    </div>
                    <div class="input-group mb-1">
                        <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" name="password" required>
                    </div>
                    <div class="input-group mb-5 d-flex justify-content-between">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="formCheck">
                            <label for="formCheck" class="form-check-label text-secondary"><small>Remember Me</small></label>
                        </div>
                        <div class="forgot">
                            <small><a href="#">Forgot Password?</a></small>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        
                        <button class="btn btn-lg btn-primary w-100 fs-6" >Login</button>
                   
                    </div>
                </form>
                    <div class="row">
                        <small>Don't have an account? <a href="#" onclick="toggleSignUp()">Sign Up</a></small>
                    </div>
                </div>
            </div>

<!-- Signup Form -->
<div id="signup-form" class="col-md-6 right-box" style="display: none;">
    <div class="row align-items-center">
        <div class="header-text mb-4">
            <h2>Sign Up</h2>
            <p>Create a new account</p>
        </div>
        <div class="input-group mb-3">
            <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Username">
        </div>
        <div class="input-group mb-3">
            <input type="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email address" pattern="[a-zA-Z0-9._%+-]+@nu-laguna.students.ph" title="Enter a valid @nu-laguna.students.ph email address" required>
        </div>
        <div class="input-group mb-3">
            <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password">
        </div>
        
        <div class="input-group mb-3">
            <button class="btn btn-lg btn-primary w-100 fs-6" onclick="signUp()">Sign Up</button>
        </div>
        <div class="row">
            <small>Already have an account? <a href="#" onclick="toggleLogin()">Log In</a></small>
        </div>
    </div>
</div>

        </div>
    </div>

    <script>
        function toggleSignUp() {
            const loginForm = document.querySelector('.right-box');
            const signUpForm = document.getElementById('signup-form');

            loginForm.style.display = 'none';
            signUpForm.style.display = 'block';
        }

        function toggleLogin() {
            const loginForm = document.querySelector('.right-box');
            const signUpForm = document.getElementById('signup-form');

            loginForm.style.display = 'block';
            signUpForm.style.display = 'none';
        }

        function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('formCheck').checked;

            // Save login information in local storage if "Remember Me" is checked
            if (rememberMe) {
                localStorage.setItem('loginEmail', email);
                localStorage.setItem('loginPassword', password);
                localStorage.setItem('rememberMe', 'true');
            } else {
                localStorage.removeItem('loginEmail');
                localStorage.removeItem('loginPassword');
                localStorage.removeItem('rememberMe');
            }

            // Perform login logic here
            console.log('Login Information:', { email, password, rememberMe });
        }

        // Populate login form with saved data if "Remember Me" was checked
        window.onload = function () {
            const rememberMe = localStorage.getItem('rememberMe');
            if (rememberMe === 'true') {
                const email = localStorage.getItem('loginEmail');
                const password = localStorage.getItem('loginPassword');
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const rememberMeCheckbox = document.getElementById('formCheck');

                emailInput.value = email;
                passwordInput.value = password;
                rememberMeCheckbox.checked = true;
            }
        };
    </script>
</body>
</html>

<?php 
}else{
    header("Location: login.php");
}
 ?>