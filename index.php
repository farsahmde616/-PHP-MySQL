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

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'components/header_meta.php'; ?>
   <title>الصفحة الرئيسية</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
</head>

<body dir="rtl">

   <?php include 'components/user_header.php'; ?>

   <section class="hero">
      <div class="swiper hero-slider">
         <div class="swiper-wrapper">
            <div class="swiper-slide slide">
               <div class="content">
                  <span>اطلب عبر الإنترنت</span>
                  <h3>بيتزا لذيذة</h3>
                  <a href="menu.php" class="btn">انظر قائمة الطعام</a>
               </div>
               <div class="image">
                  <img src="images/home-img-1.png" alt="">
               </div>
            </div>

            <div class="swiper-slide slide">
               <div class="content">
                  <span>اطلب عبر الإنترنت</span>
                  <h3>همبرغر تشيزي</h3>
                  <a href="menu.php" class="btn">انظر قائمة الطعام</a>
               </div>
               <div class="image">
                  <img src="images/home-img-2.png" alt="">
               </div>
            </div>

            <div class="swiper-slide slide">
               <div class="content">
                  <span>اطلب عبر الإنترنت</span>
                  <h3>دجاج مشوي</h3>
                  <a href="menu.php" class="btn">انظر قائمة الطعام</a>
               </div>
               <div class="image">
                  <img src="images/home-img-3.png" alt="">
               </div>
            </div>
         </div>
         <div class="swiper-pagination"></div>
      </div>
   </section>

   <section class="category">
      <h1 class="title">أنواع الأطعمة</h1>
      <div class="box-container">
         <a href="category.php?category=وجبات سريعة" class="box">
            <img src="images/cat-1.png" alt="">
            <h3>الوجبات السريعة</h3>
         </a>

         <a href="category.php?category=وجبات رئيسية" class="box">
            <img src="images/cat-2.png" alt="">
            <h3>الوجبات الرئيسية</h3>
         </a>

         <a href="category.php?category=مشروبات" class="box">
            <img src="images/cat-3.png" alt="">
            <h3>المشروبات</h3>
         </a>

         <a href="category.php?category=حلويات" class="box">
            <img src="images/cat-4.png" alt="">
            <h3>الحلويات</h3>
         </a>

         <a href="category.php?category=مشويات" class="box">
            <img src="images/cat-5.png" alt="">
            <h3>مشويات</h3>
         </a>

         <a href="category.php?category=معجنات" class="box">
            <img src="images/cat-6.png" alt="">
            <h3>معجنات</h3>
         </a>
      </div>
   </section>

   <section class="products">
      <h1 class="title">أحدث الأطباق</h1>
      <div class="box-container">
         <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
         $select_products->execute();
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
                  <a href="quick_view.php?pid=<?= $product_id; ?>" class="fas fa-eye"></a>
                  <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
                  <img src="uploaded_img/<?= $product_image; ?>" alt="">
                  <a href="category.php?category=<?= $product_category; ?>" class="cat"><?= $product_category; ?></a>
                  <div class="name"><?= $product_name; ?></div>
                  <div class="flex">
                     <div class="price"><span>$</span><?= $product_price; ?></div>
                     <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                  </div>
               </form>
         <?php
            }
         } else {
            echo '<p class="empty">لم تتم إضافة أي منتجات بعد!</p>';
         }
         ?>
      </div>

      <div class="more-btn">
         <a href="menu.php" class="btn">عرض الكل</a>
      </div>
   </section>

   <?php include 'components/footer.php'; ?>

   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

   <script src="js/swiper.js"></script>

</body>

</html>