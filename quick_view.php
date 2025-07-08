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

if (isset($_GET['pid'])) {
   $pid = $_GET['pid'];
} else {
   $pid = '';
}

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'components/header_meta.php'; ?>
   <title>عرض المنتج</title>
</head>

<body dir="rtl">

   <?php include 'components/user_header.php'; ?>

   <div class="heading">
      <h3>عرض المنتج</h3>
      <p><a href="index.php">الصفحة الرئيسية</a> <span> / عرض المنتج</span></p>
   </div>

   <section class="quick-view">
      <?php
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$pid]);
      if ($select_products->rowCount() > 0) {
         while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            $product_id = htmlspecialchars($fetch_products['id'], ENT_QUOTES, 'UTF-8');
            $product_name = htmlspecialchars($fetch_products['name'], ENT_QUOTES, 'UTF-8');
            $product_price = htmlspecialchars($fetch_products['price'], ENT_QUOTES, 'UTF-8');
            $product_image = htmlspecialchars($fetch_products['image'], ENT_QUOTES, 'UTF-8');
            $product_category = htmlspecialchars($fetch_products['category'], ENT_QUOTES, 'UTF-8');
      ?>
            <form action="" method="post" class="box">
               <input type="hidden" name="pid" value="<?= $product_id; ?>">
               <input type="hidden" name="name" value="<?= $product_name; ?>">
               <input type="hidden" name="price" value="<?= $product_price; ?>">
               <input type="hidden" name="image" value="<?= $product_image; ?>">
               <img src="uploaded_img/<?= $product_image; ?>" alt="">
               <a href="category.php?category=<?= $product_category; ?>" class="cat"><?= $product_category; ?></a>
               <div class="name"><?= $product_name; ?></div>
               <div class="flex">
                  <div class="price"><span>$</span><?= $product_price; ?></div>
                  <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
               </div>
               <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
            </form>
      <?php
         }
      } else {
         echo '<p class="empty">لم يتم العثور على المنتج!</p>';
      }
      ?>
   </section>

   <?php include 'components/footer.php'; ?>

</body>

</html>