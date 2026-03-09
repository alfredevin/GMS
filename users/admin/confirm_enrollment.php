<?php
include '../../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './../../mailer/src/Exception.php';
require './../../mailer/src/PHPMailer.php';
require './../../mailer/src/SMTP.php';

if (isset($_POST['confirm_id'])) {
    $id = $_POST['confirm_id'];
    $select_enrolles = mysqli_query($conn, "SELECT * FROM enrollment_tbl
    INNER JOIN new_users ON new_users.new_users_id  = enrollment_tbl.user_ids
    WHERE enrollmentId  = '$id' LIMIT 1");
    $row_users = mysqli_fetch_assoc($select_enrolles);
    $user_email = $row_users["email"];
    $user_name = $row_users["full_name"];
    $sy = $row_users["stud_sy"];

    $update = mysqli_query($conn, "UPDATE enrollment_tbl SET enrollment_status = 2 WHERE enrollmentId  = '$id'");

    if ($update) {

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'highschoolbangbangnational@gmail.com';
            $mail->Password = 'njdvqtbzbgtppobe';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('highschoolbangbangnational@gmail.com');
            $mail->addAddress($user_email);
            $mail->isHTML(true);


            $mail->Subject = ' Enrollment Confirmed - Final Step Required';

            $mail->Body = "
<div style='font-family: Arial, sans-serif; font-size: 15px; color: #333; line-height: 1.6;'>
    <p>👋 Hello <strong>$user_name</strong>,</p>

    <p>✅ We are pleased to inform you that your <strong>Enrollment Form</strong> for School Year <strong>$sy</strong> at <strong>Bangbang National High School</strong> has been <strong>successfully confirmed</strong> by our enrollment team.</p>

    <p>📄 To complete your enrollment process, please proceed to the school registrar’s office and submit all the required original documents. This is necessary to finalize and validate your enrollment status.</p>

    <p>📌 Kindly make sure to bring all required documents before the enrollment deadline to avoid any delays.</p>

    <p>📞 If you have any questions or need assistance, feel free to contact us or visit the school during office hours.</p>

    <p>🎓 We look forward to seeing you this school year!</p>

    <p style='color: #555;'>
        Best regards,<br>
        <strong>BNHS Enrollment Team</strong>
    </p>
</div>
";




            $mail->send();

            echo "success";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Email failed to send: " . $mail->ErrorInfo . "');</script>";
        }
    } else {
        echo "error";
    }
}
