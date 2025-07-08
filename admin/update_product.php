<?php

include '../components/connect.php';

if (!isset($_SESSION)) {
   session_start();
}

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
};

if (isset($_POST['update'])) {

   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_SPECIAL_CHARS);
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_SPECIAL_CHARS);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_SPECIAL_CHARS);

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, price = ? WHERE id = ?");
   $update_product->execute([$name, $category, $price, $pid]);

   $message[] = 'product updated!';

   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/' . $image;

   if (!empty($image)) {
      if ($image_size > 2000000) {
         $message[] = 'images size is too large!';
      } else {
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $pid]);
         move_uploaded_file($image_tmp_name, $image_folder);
         unlink('../uploaded_img/' . $old_image);
         $message[] = 'image updated!';
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
   <title>update product</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php' ?>

   <!-- update product section starts  -->

   <section class="update-product">

      <h1 class="heading">update product</h1>

      <?php
      $update_id = $_GET['update'];
      $show_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $show_products->execute([$update_id]);
      if ($show_products->rowCount() > 0) {
         while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
            <form action="" method="POST" enctype="multipart/form-data">
               <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
               <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
               <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
               <span>تعديل الاسم</span>
               <input type="text" required placeholder="أدخل اسم المنتج" name="name" maxlength="100" class="box" value="<?= $fetch_products['name']; ?>">
               <span>تعديل السعر</span>
               <input type="number" min="0" max="9999999999" required placeholder="أدخل سعر المنتج" name="price" onkeypress="if(this.value.length == 10) return false;" class="box" value="<?= $fetch_products['price']; ?>">
               <span>فئة الوجبة</span>
               <select name="category" class="box" required>
                  <option selected value="<?= $fetch_products['category']; ?>"><?= $fetch_products['category']; ?></option>
                  <option value="وجبات رئيسية">الوجبات الرئيسية</option>
                  <option value="وجبات سريعة">الوجبات السريعة</option>
                  <option value="مشويات">المشويات</option>
                  <option value="مشروبات">المشروبات</option>
                  <option value="حلويات">الحلويات</option>
                  <option value="معجنات">المعجنات</option>
               </select>
               <span>تعديل الصوره</span>
               <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
               <div class="flex-btn">
                  <input type="submit" value="تحديث" class="btn" name="update">
                  <a href="products.php" class="option-btn">العودة للخلف</a>
               </div>
            </form>
      <?php
         }
      } else {
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>

   </section>

   <!-- update product section ends -->










   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

</body>

</html>