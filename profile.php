<?php

include 'components/connect.php';

if (!isset($_SESSION)) {
    session_start();
} // بدء جلسة المستخدم

// التحقق مما إذا كان المستخدم مسجلاً للدخول
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // استعادة معرف المستخدم من الجلسة
} else {
    $user_id = '';
}

if (isset($_POST['delete'])) {
    $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $delete_user->execute([$user_id]);
    session_destroy();
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'components/header_meta.php'; ?>
    <title>الملف الشخصي</title>
</head>

<body dir="rtl">

    <?php include 'components/user_header.php'; ?>

    <div class="heading">
        <h3>الملف الشخصي</h3>
        <p><a href="index.php">الصفحة الرئيسية</a> <span> / الملف الشخصي</span></p>
    </div>

    <section class="user-details">
        <div class="user">
            <?php
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_user->execute([$user_id]);
            if ($select_user->rowCount() > 0) {
                $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
            ?>
                <img src="uploaded_img/<?= $fetch_user['image']; ?>" alt="">
                <p><i class="fas fa-user"></i><span><?= $fetch_user['name']; ?></span></p>
                <p><i class="fas fa-phone"></i><span><?= $fetch_user['number']; ?></span></p>
                <p><i class="fas fa-envelope"></i><span><?= $fetch_user['email']; ?></span></p>
                <a href="update_profile.php" class="btn">تحديث الملف الشخصي</a>
                <p class="address"><i class="fas fa-map-marker-alt"></i><span><?php if ($fetch_user['address'] == '') {
                                                                                    echo 'لم يتم إضافة عنوان';
                                                                                } else {
                                                                                    echo $fetch_user['address'];
                                                                                } ?></span></p>
                <a href="update_address.php" class="btn">تحديث العنوان</a>
            <?php
            }
            ?>
            <form action="" method="post">
                <input type="submit" value="حذف الحساب" name="delete" class="btn" onclick="return confirm('هل أنت متأكد من حذف حسابك؟')">
            </form>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>


</body>

</html>