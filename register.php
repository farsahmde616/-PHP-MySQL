<?php

include 'components/connect.php';
include 'components/csrf.php';

if (!isset($_SESSION)) {
    session_start();
} // بدء جلسة المستخدم

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // استعادة معرف المستخدم من الجلسة
} else {
    $user_id = '';
}

$website_domain = 'http://localhost'; // عنوان الموقع

// التحقق مما إذا كان النموذج قد تم تقديمه
if (isset($_POST['submit'])) {
    // Verify CSRF token
    if (!CSRF::validateToken($_POST['csrf_token'])) {
        $message[] = 'خطأ في التحقق من الأمان!';
    } else {
        // تصفية المدخلات مع التحقق من صحة البيانات
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $number = filter_input(INPUT_POST, 'number', FILTER_SANITIZE_SPECIAL_CHARS);
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];

        // التحقق من صحة المدخلات
        if (empty($name) || empty($email) || empty($number) || empty($pass) || empty($cpass)) {
            $message[] = 'يرجى ملء جميع الحقول!';
            return; // إنهاء العملية إذا كانت المدخلات غير صالحة
        }

        // التحقق من تنسيق البريد الإلكتروني
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message[] = 'البريد الإلكتروني غير صالح!';
            return; // إنهاء العملية إذا كان البريد الإلكتروني غير صالح
        }

        // التحقق من وجود المستخدم
        try {
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $select_user->execute([$email]);
            // $row = $select_user->fetch(PDO::FETCH_ASSOC);

            if ($select_user->rowCount() > 0) {
                $message[] = 'هذا الحساب موجود بالفعل! يرجى تسجيل الدخول';
            } else {
                if ($pass !== $cpass) {
                    $message[] = 'تأكيد كلمة المرور غير متطابقة!';
                } else {
                    // تجزئة كلمة المرور
                    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

                    // إدراج المستخدم
                    $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password) VALUES(?,?,?,?)");

                    // بداية كود معالجة الأخطاء
                    if ($insert_user->execute([$name, $email, $number, $hashed_pass])) {
                        // إرسال بريد تأكيد
                        $user_id = $conn->lastInsertId(); // الحصول على ID المستخدم الجديد
                        $verification_code = bin2hex(random_bytes(16)); // توليد كود تأكيد فريد

                        // إدراج كود التحقق في قاعدة البيانات
                        $insert_code = $conn->prepare("INSERT INTO `email_verifications` (user_id, code) VALUES (?, ?)");
                        $insert_code->execute([$user_id, $verification_code]);

                        // إعداد رابط التحقق
                        $verification_link = $website_domain . "/verify_email.php?code=" . $verification_code;

                        // إرسال البريد الإلكتروني (يمكنك استخدام PHPMailer أو mail())
                        $to = $email;
                        $subject = "تأكيد بريدك الإلكتروني";
                        $message = "يرجى تأكيد بريدك الإلكتروني بالنقر على الرابط التالي: $verification_link";
                        mail($to, $subject, $message); // إرسال البريد الإلكتروني

                        // إعادة توجيه المستخدم
                        $_SESSION['user_id'] = $user_id; // تعيين الجلسة
                        header('location:index.php');
                        exit(); // تأكد من إنهاء السكربت بعد إعادة التوجيه
                    } else {
                        $message[] = 'حدث خطأ أثناء إنشاء الحساب!';
                    }
                    // نهاية كود معالجة الأخطاء
                }
            }
        } catch (PDOException $e) {
            // معالجة الاستثناءات في حالة حدوث خطأ في قاعدة البيانات
            $message[] = 'خطأ في الخادم';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'components/header_meta.php'; ?>
    <title>انشاء حساب</title>
</head>

<body dir="rtl">

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="form-container">

        <form action="" method="post">
            <h3>انشاء حساب</h3>
            <input type="text" name="name" required placeholder="ادخل اسمك" class="box" maxlength="50">
            <input type="email" name="email" required placeholder="ادخل بريدك الالكتروني" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="number" name="number" required placeholder="ادخل رقم هاتفك" class="box" min="0" max="9999999999" maxlength="10">
            <input type="password" name="pass" required placeholder="ادخل كلمة مرور" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" required placeholder="أكد كلمة المرور" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <?php echo CSRF::getTokenField(); ?>
            <input type="submit" value="انشاء حساب" name="submit" class="btn">
            <p>هل لديك حساب من قبل ؟ <a href="login.php">تسجيل الدخول الآن</a></p>
        </form>
    </section>

    <?php include 'components/footer.php'; ?>
</body>

</html>