<?php

include 'components/connect.php';

if (!isset($_SESSION)) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'components/header_meta.php'; ?>
   <title>الطلبات</title>
</head>

<body dir="rtl">

   <?php include 'components/user_header.php'; ?>

   <div class="heading">
      <h3>الطلبات</h3>
      <p><a href="index.php">الصفحة الرئيسية</a> <span> / الطلبات</span></p>
   </div>

   <section class="orders">
      <h1 class="title">طلباتك</h1>
      <div class="box-container">
         <?php
         if ($user_id == '') {
            echo '<p class="empty">الرجاء تسجيل الدخول لرؤية طلباتك!</p>';
         } else {
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? ORDER BY placed_on DESC");
            $select_orders->execute([$user_id]);
            if ($select_orders->rowCount() > 0) {
               while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                  $order_id = htmlspecialchars($fetch_orders['id'], ENT_QUOTES, 'UTF-8');
                  $name = htmlspecialchars($fetch_orders['name'], ENT_QUOTES, 'UTF-8');
                  $number = htmlspecialchars($fetch_orders['number'], ENT_QUOTES, 'UTF-8');
                  $email = htmlspecialchars($fetch_orders['email'], ENT_QUOTES, 'UTF-8');
                  $address = htmlspecialchars($fetch_orders['address'], ENT_QUOTES, 'UTF-8');
                  $payment_method = htmlspecialchars($fetch_orders['payment_method'], ENT_QUOTES, 'UTF-8');
                  $total_products = htmlspecialchars($fetch_orders['total_products'], ENT_QUOTES, 'UTF-8');
                  $total_price = htmlspecialchars($fetch_orders['total_price'], ENT_QUOTES, 'UTF-8');
                  $placed_on = htmlspecialchars($fetch_orders['placed_on'], ENT_QUOTES, 'UTF-8');
         ?>
                  <div class="box">
                     <p>تاريخ الطلب : <span><?= $placed_on; ?></span></p>
                     <p>الاسم : <span><?= $name; ?></span></p>
                     <p>رقم الهاتف : <span><?= $number; ?></span></p>
                     <p>البريد الإلكتروني : <span><?= $email; ?></span></p>
                     <p>العنوان : <span><?= $address; ?></span></p>
                     <p>طريقة الدفع : <span><?= $payment_method; ?></span></p>
                     <p>المنتجات المطلوبة : <span><?= $total_products; ?></span></p>
                     <p>المجموع الكلي : <span>$<?= $total_price; ?>/-</span></p>
                     <p>حالة الدفع : <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') {
                                                            echo 'red';
                                                         } else {
                                                            echo 'green';
                                                         }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
                  </div>
         <?php
               }
            } else {
               echo '<p class="empty">لا توجد طلبات حتى الآن!</p>';
            }
         }
         ?>
      </div>
   </section>

   <?php include 'components/footer.php'; ?>


</body>

</html>