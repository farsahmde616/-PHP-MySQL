<?php
include '../components/connect.php';

if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Check if the verification code exists in the database
    $select_code = $conn->prepare("SELECT * FROM `email_verifications` WHERE code = ?");
    $select_code->execute([$verification_code]);

    if ($select_code->rowCount() > 0) {
        $fetch_code = $select_code->fetch(PDO::FETCH_ASSOC);
        $user_id = $fetch_code['user_id'];

        // Update the user's email_verified status
        $update_user = $conn->prepare("UPDATE `users` SET email_verified = 1 WHERE id = ?");
        $update_user->execute([$user_id]);

        // Delete the verification code from the database
        $delete_code = $conn->prepare("DELETE FROM `email_verifications` WHERE code = ?");
        $delete_code->execute([$verification_code]);

        echo "تم تأكيد بريدك الإلكتروني بنجاح!";
    } else {
        echo "كود التحقق غير صالح!";
    }
} else {
    echo "لم يتم توفير كود التحقق!";
}
?>