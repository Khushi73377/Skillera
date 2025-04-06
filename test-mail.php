<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com';
    $mail->Password = 'your-app-password';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your-email@gmail.com', 'Skill Era Support');
    $mail->addAddress('your-email@gmail.com'); // ✅ Send to your own email for testing
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email from PHPMailer.';

    if ($mail->send()) {
        echo "✅ Test email sent successfully!";
    } else {
        echo "❌ Mail Error: " . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $mail->ErrorInfo;
}
?>
