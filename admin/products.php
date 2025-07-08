<?php

include '../components/connect.php';

if (!isset($_SESSION)) {
    session_start();
}

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_SPECIAL_CHARS);
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_SPECIAL_CHARS);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    // استخدام prepared statements للتحقق من وجود المنتج
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_products->execute([$name]);

    if ($select_products->rowCount() > 0) {
        $message[] = 'اسم المنتج موجود بالفعل!';
    } else {
        if ($image_size > 2000000) {
            $message[] = 'حجم الصورة كبير جداً';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);

            // إضافة المنتج باستخدام prepared statement
            $insert_product = $conn->prepare("INSERT INTO `products`(name, category, price, image) VALUES(?,?,?,?)");
            $insert_product->execute([$name, $category, $price, $image]);

            $message[] = 'تم إضافة منتج جديد!';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_img/' . $fetch_delete_image['image']);

    // حذف المنتج
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);

    // حذف من السلة
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
    $delete_cart->execute([$delete_id]);
    header('location:products.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' https://images.example.com; font-src 'self' https://fonts.googleapis.com; script-src 'self' https://trusted-scripts.com;">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اضافة منتجات</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- add products section starts  -->

    <section class="add-products">

        <form action="" method="POST" enctype="multipart/form-data">
            <h3>أضف منتج</h3>
            <input type="text" required placeholder="enter product name" name="name" maxlength="100" class="box">
            <input type="number" min="0" max="9999999999" required placeholder="enter product price" name="price" onkeypress="if(this.value.length == 10) return false;" class="box">
            <select name="category" class="box" required>
                <option value="" disabled selected>اختر فئة الوجبات</option>
                <option value="وجبات رئيسية">الوجبات الرئيسية</option>
                <option value="وجبات سريعة">الوجبات السريعة</option>
                <option value="مشويات">المشويات</option>
                <option value="مشروبات">المشروبات</option>
                <option value="حلويات">الحلويات</option>
                <option value="معجنات">المعجنات</option>
            </select>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
            <input type="submit" value="add product" name="add_product" class="btn">
        </form>

    </section>

    <!-- add products section ends -->

    <!-- show products section starts  -->

    <section class="show-products" style="padding-top: 0;">

        <div class="box-container">

            <?php
            $show_products = $conn->prepare("SELECT * FROM `products`");
            $show_products->execute();
            if ($show_products->rowCount() > 0) {
                while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                    // تأمين البيانات المعروضة
                    $product_name = htmlspecialchars($fetch_products['name'], ENT_QUOTES, 'UTF-8');
                    $product_price = htmlspecialchars($fetch_products['price'], ENT_QUOTES, 'UTF-8');
                    $product_category = htmlspecialchars($fetch_products['category'], ENT_QUOTES, 'UTF-8');
                    $product_image = htmlspecialchars($fetch_products['image'], ENT_QUOTES, 'UTF-8');
            ?>
                    <div class="box">
                        <img src="../uploaded_img/<?= $product_image; ?>" alt="">
                        <div class="flex">
                            <div class="price"><span>$</span><?= $product_price; ?><span>/-</span></div>
                            <div class="category"><?= $product_category; ?></div>
                        </div>
                        <div class="name"><?= $product_name; ?></div>
                        <div class="flex-btn">
                            <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">تحديث</a>
                            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('هل تريد حذف هذا المنتج؟');">حذف</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">لم تتم إضافة أي منتجات بعد</p>';
            }
            ?>

        </div>

    </section>

    <!-- show products section ends -->

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>