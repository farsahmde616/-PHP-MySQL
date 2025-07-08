<?php

include 'components/connect.php';

if (!isset($_SESSION)) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:index.php');
};

if (isset($_POST['submit'])) {

   $address = $_POST['area'] . ', ' . $_POST['town'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_SPECIAL_CHARS);

   $update_address = $conn->prepare("UPDATE `users` set address = ? WHERE id = ?");
   $update_address->execute([$address, $user_id]);

   $message[] = 'address saved!';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'components/header_meta.php'; ?>
   <title>تحديث العنوان</title>
</head>

<body dir="rtl">
   <?php include 'components/user_header.php' ?>
   <section class="form-container">
      <form action="" method="post">
         <h3>عنوانك</h3>
         <input type="text" class="box" placeholder="محافظة" required maxlength="50" name="area">
         <input type="text" class="box" placeholder="اسم المديرية" required maxlength="50" name="town">
         <input type="text" class="box" placeholder="اسم المنطقه" required maxlength="50" name="city">
         <input type="text" class="box" placeholder="جوار" required maxlength="50" name="state">
         <input type="number" class="box" placeholder="ادخل رمز سري خاص بك" required max="999999" min="0" maxlength="6" name="pin_code">
         <input type="submit" value="save address" name="submit" class="btn">
      </form>

   </section>
   <?php include 'components/footer.php' ?>
</body>

</html>