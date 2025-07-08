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

// التحقق من المدخلات
if (isset($_POST['send'])) {
    // تصفية المدخلات
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $msg = filter_input(INPUT_POST, 'msg', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // التحقق من تنسيق البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'البريد الإلكتروني غير صالح!';
    } else {
        // تحقق من CAPTCHA
        if (empty($_POST['captcha']) || $_POST['captcha'] !== $_SESSION['captcha_text']) {
            $message[] = 'التحقق فشل! يرجى إعادة المحاولة.';
        } else {
            // التحقق مما إذا كانت الرسالة قد أرسلت مسبقًا
            $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
            $select_message->execute([$name, $email, $number, $msg]);

            if ($select_message->rowCount() > 0) {
                $message[] = 'تم إرسال الرسالة بالفعل!';
            } else {
                // Debugging: Check the value of $user_id
                if (!isset($user_id) || empty($user_id)) {
                    $message[] = 'يجب تسجيل الدخول لإرسال الرسالة!';
                } else {
                    // التحقق من وجود رقم المستخدم في جدول المتسخدمين
                    $check_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                    $check_user->execute([$user_id]);
                    if ($check_user->rowCount() == 0) {
                        $message[] = 'حدث خطأ غير متوقع! يرجى إعادة المحاولة.';
                    } else {
                        // إدراج الرسالة
                        $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
                        $insert_message->execute([$user_id, htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), htmlspecialchars($email, ENT_QUOTES, 'UTF-8'), htmlspecialchars($number, ENT_QUOTES, 'UTF-8'), htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')]);

                        $message[] = 'تم إرسال الرسالة بنجاح!';
                    }
                }
            }
        }
    }
}

// توليد نص CAPTCHA
function generateCaptcha()
{
    $length = 6;
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captcha = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha;
}

// تخزين نص CAPTCHA في الجلسة
$_SESSION['captcha_text'] = generateCaptcha();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'components/header_meta.php'; ?>
    <title>اتصل بنا</title>
</head>

<body dir="rtl">

    <?php include 'components/user_header.php'; ?>

    <div class="heading">
        <h3>اتصل بنا</h3>
        <p><a href="index.php">الصفحة الرئيسية</a> <span> / اتصل بنا</span></p>
    </div>

    <section class="contact">
        <div class="row">
            <div class="image">
                <img src="images/contact-img.svg" alt="">
            </div>
            <form action="" method="post">
                <h3>أرسل لنا رسالة</h3>
                <input type="text" name="name" required placeholder="أدخل اسمك" maxlength="50" class="box">
                <input type="email" name="email" required placeholder="أدخل بريدك الإلكتروني" maxlength="50" class="box">
                <input type="number" name="number" required placeholder="أدخل رقم هاتفك" max="9999999999" min="0" class="box" onkeypress="if(this.value.length == 10) return false;">
                <textarea name="msg" class="box" required placeholder="أدخل رسالتك" cols="30" rows="10"></textarea>
                <input type="submit" value="إرسال الرسالة" name="send" class="btn">
            </form>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>


</body>

</html>