<?php
include '../../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './../../mailer/src/Exception.php';
require './../../mailer/src/PHPMailer.php';
require './../../mailer/src/SMTP.php';

if (isset($_POST['confirm_id']) && isset($_POST['grade']) && isset($_POST['section_id'])) {
    $enrollmentId = $_POST['confirm_id'];
    $grade = $_POST['grade'];
    $sectionId = $_POST['section_id'];

    $select_section = mysqli_query($conn, "SELECT * FROM section_tbl WHERE section_id = '$sectionId' LIMIT 1");
    $row_section = mysqli_fetch_assoc($select_section);
    $section_name = $row_section['section_name'];


    $select_enrolles = mysqli_query($conn, "SELECT * FROM enrollment_tbl
    INNER JOIN new_users ON new_users.new_users_id  = enrollment_tbl.user_ids
    WHERE enrollmentId  = '$enrollmentId' LIMIT 1");
    $row_users = mysqli_fetch_assoc($select_enrolles);
    $user_email = $row_users["email"];
    $user_name = $row_users["full_name"];
    $sy = $row_users["stud_sy"];


    // Step 1: Update enrollment status
    $update = mysqli_query($conn, "UPDATE enrollment_tbl SET enrollment_status = 3 WHERE enrollmentId = '$enrollmentId'");

    if ($update) {
        // Step 2: Generate student_id (format: 2025-001 etc.)
        $year = date('Y');
        $prefix = $year;
        $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM student_tbl WHERE student_id LIKE '$year-%'");
        $countRow = mysqli_fetch_assoc($countQuery);
        $next = str_pad($countRow['total'] + 1, 3, '0', STR_PAD_LEFT);
        $studentId = $prefix . '-' . $next;
        $hashed_password =  password_hash($studentId, PASSWORD_DEFAULT);

        // Step 3: Insert to student_tbl
        $insert = mysqli_query($conn, "INSERT INTO student_tbl (student_id, enrollment_id, student_grade, section_id,password) 
                                       VALUES ('$studentId', '$enrollmentId', '$grade', '$sectionId','$hashed_password')");

        if ($insert) {

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


                $mail->Subject = ' You Are Officially Enrolled at BNHS!';

                $mail->Body = "
<div style='font-family: Arial, sans-serif; font-size: 15px; color: #333; line-height: 1.6;'>
    <p>👋 Hello <strong>$user_name</strong>,</p>

    <p>🎉 Congratulations! Your <strong>Enrollment</strong> for School Year <strong>$sy</strong> at <strong>Bangbang National High School</strong> has been <strong>fully completed</strong>.</p>
    
    <p> Your Grade and Section is <strong>$grade - $section_name</strong> .</p>

    <p>📑 We have received all your required documents, and your enrollment status is now marked as <strong>Officially Enrolled</strong>. Welcome to a new chapter of growth, learning, and achievement!</p>

    <p>🌟 Remember, education is the most powerful tool you can use to change your life and the world. Work hard, stay focused, and believe in your potential — because great things await you!</p>

    <p>📌 If you have any further questions or concerns, feel free to reach out to our office or send us an email. We’re always here to help.</p>

    <p>🙌 Once again, welcome to Bangbang National High School. We’re excited to be part of your academic journey!</p>

    <p style='color: #555; margin-top: 20px;'>
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
            echo "insert_error";
        }
    } else {
        echo "update_error";
    }
}
