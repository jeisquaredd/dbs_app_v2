<?php
require_once('classes/database.php');
session_start();
$con = new database();


$sweetAlertConfig = ""; // Initialize SweetAlert script variable

if (isset($_POST['register'])) {

  echo 'form submitted';

    // Getting the account information
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash the password

    // Getting the personal information
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];

    // Save the user data in the Users table
    $userID = $con->signupUser($firstname, $lastname, $username, $email, $password);
echo 'form submitted 2';
    if ($userID) {
      echo 'User ID: ' . $userID;
        // Registration successful, set SweetAlert script
        $sweetAlertConfig = "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful',
                    text: 'Your account has been created successfully!',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            </script>";
    } else {
      $sweetAlertConfig = "
        <script>
    Swal.fire({
        icon: 'error',
        title: 'Registration Failed',
        text: 'Sorry, there was an error signing up. Please try again.',
        confirmButtonText: 'OK'
    });
</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <link rel="stylesheet" href="./package/dist/sweetalert2.css">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4 text-center">Admin Registration</h2>
    <form id="registrationForm" method="POST" action="" class="bg-white p-4 rounded shadow-sm">
      <div class="mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter your first name" required>
        <div class="invalid-feedback">First name is required.</div>
      </div>
      <div class="mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter your last name" required>
        <div class="invalid-feedback">Last name is required.</div>
      </div>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
        <div class="invalid-feedback">Username is required.</div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
        <div class="invalid-feedback">Email is required and must be valid.</div>
      </div>
      
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        <div class="invalid-feedback">Password must be at least 6 characters long, include an uppercase letter, a number, and a special character.</div>      
      </div>
      <button type="submit" id="registerButton" name="register" class="btn btn-primary w-100">Register</button>

    </form>
  </div>
  
  <script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
  <script src="./package/dist/sweetalert2.js"></script>
  <?php echo $sweetAlertConfig; ?>
  <script>
  // Function to validate individual fields
  function validateField(field, validationFn) {
    field.addEventListener('input', () => {
      if (validationFn(field.value)) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
      }
    });
  }

  let isUsernameAvailable = false;
  let isEmailAvailable = false;

  function toggleRegisterButton() {
  const registerButton = document.getElementById('registerButton');
  if (isUsernameAvailable && isEmailAvailable) {
    registerButton.disabled = false;
  } else {
    registerButton.disabled = true;
  }
}

  // Validation functions for each field
  const isNotEmpty = (value) => value.trim() !== '';
  const isPasswordValid = (value) => {
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
    return passwordRegex.test(value);
  };

  const isEmailValid = (value) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(value);
  };

  // Real-time username validation using AJAX
  const checkUsernameAvailability = (usernameField) => {
  usernameField.addEventListener('input', () => {
    const username = usernameField.value.trim();
    if (username === '') {
      usernameField.classList.remove('is-valid');
      usernameField.classList.add('is-invalid');
      usernameField.nextElementSibling.textContent = 'Username is required.';
      isUsernameAvailable = false;
      toggleRegisterButton();
      return;
    }
    fetch('ajax/check_username.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `username=${encodeURIComponent(username)}`
    })
    .then((response) => response.json())
    .then((data) => {
      if (data.exists) {
        usernameField.classList.remove('is-valid');
        usernameField.classList.add('is-invalid');
        usernameField.nextElementSibling.textContent = 'Username is already taken.';
        isUsernameAvailable = false;
      } else {
        usernameField.classList.remove('is-invalid');
        usernameField.classList.add('is-valid');
        usernameField.nextElementSibling.textContent = '';
        isUsernameAvailable = true;
      }
      toggleRegisterButton();
    })
    .catch((error) => {
      console.error('Error:', error);
      isUsernameAvailable = false;
      toggleRegisterButton();
    });
  });
};

const checkEmailAvailability = (emailField) => {
  emailField.addEventListener('input', () => {
    const email = emailField.value.trim();
    if (email === '') {
      emailField.classList.remove('is-valid');
      emailField.classList.add('is-invalid');
      emailField.nextElementSibling.textContent = 'Email is required.';
      isEmailAvailable = false;
      toggleRegisterButton();
      return;
    }
    // Validate email format first
    if (!isEmailValid(email)) {
      emailField.classList.remove('is-valid');
      emailField.classList.add('is-invalid');
      emailField.nextElementSibling.textContent = 'Email is not valid.';
      isEmailAvailable = false;
      toggleRegisterButton();
      return;
    }
    fetch('ajax/check_email.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `email=${encodeURIComponent(email)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        emailField.classList.remove('is-valid');
        emailField.classList.add('is-invalid');
        emailField.nextElementSibling.textContent = 'Email is already taken.';
        isEmailAvailable = false;
      } else {
        emailField.classList.remove('is-invalid');
        emailField.classList.add('is-valid');
        emailField.nextElementSibling.textContent = '';
        isEmailAvailable = true;
      }
      toggleRegisterButton();
    })
    .catch(err => {
      console.error('Error:', err);
      isEmailAvailable = false;
      toggleRegisterButton();
    });
  });
};

  // Get form fields
  const firstName = document.getElementById('first_name');
  const lastName = document.getElementById('last_name');
  const username = document.getElementById('username');
  const password = document.getElementById('password');
  const email = document.getElementById('email');

  // Attach real-time validation to each field
  validateField(firstName, isNotEmpty);
  validateField(lastName, isNotEmpty);
  validateField(email, isEmailValid);
  validateField(password, isPasswordValid);
  checkUsernameAvailability(username);
  checkEmailAvailability(email);

  // Form submission validation
  document.getElementById('registrationForm').addEventListener('submit', function (e) {
   // e.preventDefault(); // Prevent form submission for validation

    let isValid = true;
    let isUsernameValid = false;
    let isEmailValid = false;

    // Validate all fields on submit
    [firstName, lastName, username, email, password].forEach((field) => {
      if (!field.classList.contains('is-valid')) {
        field.classList.add('is-invalid');
        isValid = false;
      }
    });

    // If all fields are valid, submit the form
    if (isValid) {
      this.submit();
    }
  });
</script>


</body>
</html>