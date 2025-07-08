<?php

include '../components/connect.php';

if (!isset($_SESSION)) {
   session_start();
}

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit(); // Add an exit statement after redirecting
}

function validateName($name)
{
   // Add any validation rules for name here
   // For example, check if it's not empty
   if (empty($name)) {
      return false;
   }
   return true;
}

function validatePassword($password, $confirmPassword)
{
   // Add any validation rules for password here
   // For example, check if the password and confirm password match
   if ($password !== $confirmPassword) {
      return false;
   }
   return true;
}

if (isset($_POST['submit'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
   $password = sha1($_POST['pass']);
   $password = filter_var($password, FILTER_SANITIZE_SPECIAL_CHARS);
   $confirmPassword = sha1($_POST['cpass']);
   $confirmPassword = filter_var($confirmPassword, FILTER_SANITIZE_SPECIAL_CHARS);

   // Validate name
   if (!validateName($name)) {
      $message[] = 'Please enter a valid username!';
   }

   // Validate password
   if (!validatePassword($password, $confirmPassword)) {
      $message[] = 'Passwords do not match!';
   }

   if (empty($message)) {
      $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
      $select_admin->execute([$name]);

      if ($select_admin->rowCount() > 0) {
         $message[] = 'Username already exists!';
      } else {
         $insert_admin = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
         $insert_admin->execute([$name, $confirmPassword]);
         $message[] = 'New admin registered!';
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' https://images.example.com; font-src 'self' https://fonts.googleapis.com; script-src 'self' https://trusted-scripts.com;">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php' ?>

   <!-- register admin section starts  -->

   <section class="form-container">

      <form action="" method="POST">
         <h3>New Admin</h3>
         <input type="text" name="name" maxlength="20" required placeholder="Enter your username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="pass" maxlength="20" required placeholder="Enter your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="password" name="cpass" maxlength="20" required placeholder="Confirm your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
         <input type="submit" value="Register now" name="submit" class="btn">
      </form>

   </section>

   <!-- register admin section ends -->

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>