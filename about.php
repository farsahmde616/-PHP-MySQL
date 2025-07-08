<?php

include 'components/connect.php';

if (!isset($_SESSION)) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'components/header_meta.php'; ?>
   <title>عنا</title>
</head>

<body dir="rtl">

   <?php include 'components/user_header.php'; ?>

   <div class="heading">
      <h3>عنا</h3>
      <p><a href="index.php">الصفحة الرئيسية</a> <span> / عنا</span></p>
   </div>

   <section class="about">
      <div class="row">
         <div class="image">
            <img src="images/about-img.svg" alt="">
         </div>
         <div class="content">
            <h3>لماذا تختارنا؟</h3>
            <p>نحن نقدم أفضل خدمة طعام في المدينة. طعامنا لذيذ وصحي ونستخدم أفضل المكونات الطازجة.</p>
            <a href="menu.php" class="btn">انظر قائمة الطعام</a>
         </div>
      </div>
   </section>

   <section class="steps">
      <h1 class="title">كيف نعمل</h1>
      <div class="box-container">
         <div class="box">
            <img src="images/step-1.png" alt="">
            <h3>اختر طعامك</h3>
            <p>اختر من قائمة الطعام الواسعة لدينا</p>
         </div>
         <div class="box">
            <img src="images/step-2.png" alt="">
            <h3>توصيل سريع</h3>
            <p>نقوم بتوصيل طلبك بسرعة وأمان</p>
         </div>
         <div class="box">
            <img src="images/step-3.png" alt="">
            <h3>استمتع بالطعام</h3>
            <p>استمتع بوجبتك اللذيذة</p>
         </div>
      </div>
   </section>

   <?php include 'components/footer.php'; ?>


</body>

</html>