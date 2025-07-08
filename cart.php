<?php

include 'components/connect.php';

if (!isset($_SESSION)) {
   session_start();
}

if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:index.php');
   exit(); // تأكد من إنهاء السكربت بعد إعادة التوجيه
}

// حماية ضد XSS
function sanitize($data)
{
   return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['delete'])) {
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
      die("CSRF token validation failed");
   }
   $cart_id = $_POST['cart_id'];
   // تحقق من أن cart_id هو رقم صحيح
   if (filter_var($cart_id, FILTER_VALIDATE_INT)) {
      $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
      $delete_cart_item->execute([$cart_id]);
      $message[] = 'cart item deleted!';
   }
}

if (isset($_POST['delete_all'])) {
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
      die("CSRF token validation failed");
   }
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   $message[] = 'deleted all from cart!';
}

if (isset($_POST['update_qty'])) {
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
      die("CSRF token validation failed");
   }
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_SPECIAL_CHARS);
   // تحقق من أن qty هو رقم صحيح
   if (filter_var($cart_id, FILTER_VALIDATE_INT) && filter_var($qty, FILTER_VALIDATE_INT)) {
      $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
      $update_qty->execute([$qty, $cart_id]);
      $message[] = 'cart quantity updated';
   }
}

$grand_total = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'components/header_meta.php'; ?>
   <title>سلة التسوق</title>
</head>

<body dir="rtl">

   <?php include 'components/user_header.php'; ?>

   <div class="heading">
      <h3>سلة التسوق</h3>
      <p><a href="index.php">الصفحة الرئيسية</a> <span> / سلة التسوق</span></p>
   </div>

   <section class="shopping-cart">
      <h1 class="title">المنتجات المضافة</h1>
      <div class="box-container">
         <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
               $cart_id = htmlspecialchars($fetch_cart['id'], ENT_QUOTES, 'UTF-8');
               $product_id = htmlspecialchars($fetch_cart['pid'], ENT_QUOTES, 'UTF-8');
               $product_name = htmlspecialchars($fetch_cart['name'], ENT_QUOTES, 'UTF-8');
               $product_price = htmlspecialchars($fetch_cart['price'], ENT_QUOTES, 'UTF-8');
               $product_image = htmlspecialchars($fetch_cart['image'], ENT_QUOTES, 'UTF-8');
               $product_qty = htmlspecialchars($fetch_cart['quantity'], ENT_QUOTES, 'UTF-8');
               $sub_total = $product_price * $product_qty;
               $grand_total += $sub_total;
         ?>
               <div class="box">
                  <a href="cart.php?delete=<?= $cart_id; ?>" class="fas fa-times" onclick="return confirm('هل تريد حذف هذا المنتج؟');"></a>
                  <img src="uploaded_img/<?= $product_image; ?>" alt="">
                  <div class="name"><?= $product_name; ?></div>
                  <div class="price">$<?= $product_price; ?>/-</div>
                  <form action="" method="post">
                     <input type="hidden" name="cart_id" value="<?= $cart_id; ?>">
                     <input type="number" min="1" max="99" name="qty" class="qty" value="<?= $product_qty; ?>">
                     <button type="submit" class="fas fa-edit" name="update_qty"></button>
                  </form>
                  <div class="sub-total">المجموع الفرعي : <span>$<?= $sub_total; ?>/-</span></div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">سلة التسوق فارغة!</p>';
         }
         ?>
      </div>

      <?php if ($grand_total > 0) { ?>
         <div class="cart-total">
            <p>المجموع الكلي : <span>$<?= $grand_total; ?>/-</span></p>
            <div class="flex">
               <a href="menu.php" class="option-btn">مواصلة التسوق</a>
               <a href="checkout.php" class="btn">إتمام الطلب</a>
               <form action="" method="post">
                  <input type="hidden" name="delete_all" value="true">
                  <button type="submit" class="delete-btn" onclick="return confirm('هل تريد حذف كل المنتجات من السلة؟');">حذف الكل</button>
               </form>
            </div>
         </div>
      <?php } ?>

   </section>

   <?php include 'components/footer.php'; ?>


</body>

</html>