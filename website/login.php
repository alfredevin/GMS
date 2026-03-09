<?php
session_start();
date_default_timezone_set("Asia/Manila");
include 'config.php';

// IPILIT ANG MANILA TIME SA DATABASE PARA SA LOGIN LOGS
mysqli_query($conn, "SET time_zone = '+08:00'");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './../mailer/src/Exception.php';
require './../mailer/src/PHPMailer.php';
require './../mailer/src/SMTP.php';

// ==========================================
// REGISTRATION LOGIC
// ==========================================
if (isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkEmailStmt = $conn->prepare("SELECT * FROM new_users WHERE email = ? OR username = ?");
    $checkEmailStmt->bind_param("ss", $email, $username);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer)
                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: "error",
                title: "Email or Username already exists!"
            }); 
        });
    </script>';
    } else {
        $stmt = $conn->prepare("INSERT INTO new_users (full_name, email, username, password ) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $username, $password);

        if ($stmt->execute()) {

            // =======================================================
            // ITEM 4: SEND VERIFICATION / CONFIRMATION EMAIL
            // =======================================================
            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'highschoolbangbangnational@gmail.com';
                $mail->Password = 'njdvqtbzbgtppobe';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('highschoolbangbangnational@gmail.com', 'Bangbang National High School');
                $mail->addAddress($email, $full_name);
                $mail->isHTML(true);
                $mail->Subject = 'Registration Successful - Bangbang NHS';

                $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #4e73df; text-align: center;'>Welcome to Bangbang National High School!</h2>
                    <p>Hi <strong>$full_name</strong>,</p>
                    <p>Your registration on our website was successful. We are glad to have you!</p>
                    
                    <div style='background-color: #f8f9fc; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 0; font-weight: bold; color: #333;'>Your Account Details:</p>
                        <p style='margin: 5px 0 0 0;'><strong>Username:</strong> $username</p>
                        <p style='margin: 5px 0 0 0;'><strong>Email:</strong> $email</p>
                    </div>

                    <p>You may now log in using your registered credentials. If your account requires admin approval, please wait for further notice.</p>
                    <br>
                    <p>Best regards,</p>
                    <p><strong>GMS Admin</strong></p>
                </div>";

                $mail->send();

            } catch (Exception $e) {
                error_log("Registration email failed: {$mail->ErrorInfo}");
            }
            // =======================================================

            echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer)
                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: "success",
                title: "Successfully Registered! Please check your email."
            }).then(() => {
                // Optional: pwede mong i-redirect dito kung gusto mo, or i-show ang login form.
            }); 
        });
    </script>';
        }
    }
}

// ==========================================
// LOGIN LOGIC
// ==========================================
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $lockoutTime = 10 * 60;
    $maxLoginAttempts = 3;

    $currentTime = time();
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    $sql = "SELECT * FROM login_attempts WHERE ip_address = '$ipAddress'";
    $result = mysqli_query($conn, $sql);

    $failedAttemptsCount = mysqli_num_rows($result);

    if ($failedAttemptsCount >= $maxLoginAttempts) {
        $lastAttemptTime = mysqli_fetch_assoc($result)['last_attempt'];
        $remainingLockoutTime = $lastAttemptTime + $lockoutTime - $currentTime;

        if ($remainingLockoutTime > 0) {
            $minutesRemaining = ceil($remainingLockoutTime / 60);
            echo '<script>
                document.addEventListener("DOMContentLoaded", function () {
                    swal.fire({
                        title: "Unable to Login!",
                        text: "Try Again After ' . $minutesRemaining . ' minutes!",
                        icon: "error",
                        confirmButtonText: "Okay",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {

                        }
                    })
                });
            </script>';
            $msg = "<div class='alert alert-danger'>You have exceeded the maximum login attempts. Please try again in $minutesRemaining minutes.</div>";
        } else {
            $sql = "DELETE FROM login_attempts WHERE ip_address = '$ipAddress'";
            mysqli_query($conn, $sql);
        }
    } else {
        // I-SET UP LAHAT NG QUERIES PARA SA IBA'T IBANG TABLES
        $sql = "SELECT * FROM user_tbl WHERE username = '$username' AND useractive = '1'";
        $result = mysqli_query($conn, $sql);

        $teacher = "SELECT * FROM teacher_tbl WHERE teacher_id  = '$username' ";
        $teacherresult = mysqli_query($conn, $teacher);

        $student = "SELECT * FROM student_tbl WHERE student_id  = '$username' ";
        $studentResult = mysqli_query($conn, $student);

        // DINAGDAG: NEW USERS QUERY
        $newUserQuery = "SELECT * FROM new_users WHERE username = '$username' AND user_status = 1";
        $newUserResult = mysqli_query($conn, $newUserQuery);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $usertype = $row['usertype'];

            if (password_verify($password, $row['password'])) {
                $sql = "DELETE FROM login_attempts WHERE ip_address = '$ipAddress'";
                mysqli_query($conn, $sql);

                $loginTime = date("Y-m-d H:i:s");
                $sql = "INSERT INTO userlogs_tbl (userid, username, login_time, ip_address) VALUES ('$usertype', '$username', '$loginTime', '$ipAddress')";
                mysqli_query($conn, $sql);
                $encodedUrl = base64_encode("./users/admin/");
                if ($row['usertype'] === '1') {

                    $_SESSION['usertype'] = 1;
                    $_SESSION['userid'] = $row['userid'];
                    $_SESSION['position'] = $row['position'];
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener("mouseenter", Swal.stopTimer)
                                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Signed In Successfully!!!"
                            });

                            setTimeout(function () {
                                window.location.href = atob("' . $encodedUrl . '");
                            }, 2000);
                        });
                    </script>';
                } else {
                    $msg = "<div class='alert alert-danger'>Unknown user status.</div>";
                }
            } else {
                $sql = "INSERT INTO login_attempts (ip_address, last_attempt) VALUES ('$ipAddress', $currentTime)";
                mysqli_query($conn, $sql);

                echo '<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        swal.fire({
                            title: "Incorrect Credential!",
                            text: "Try Again!!!",
                            icon: "warning",
                            confirmButtonText: "Okay",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        })
                    });
                </script>';
            }
        } else if (mysqli_num_rows($studentResult) === 1) {
            $res_student = mysqli_fetch_assoc($studentResult);
            $student_id = $res_student['student_id'];

            if (password_verify($password, $res_student['password'])) {
                $sql = "DELETE FROM login_attempts WHERE ip_address = '$ipAddress'";
                mysqli_query($conn, $sql);

                $loginTime = date("Y-m-d H:i:s");
                $sql = "INSERT INTO userlogs_tbl (userid, username, login_time, ip_address) VALUES ('$student_id', '$username', '$loginTime', '$ipAddress')";
                $resultsql = mysqli_query($conn, $sql);
                $encodedUrlStudent = base64_encode("./users/student/");
                if ($resultsql) {
                    $_SESSION['student_id'] = $res_student['student_id'];
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener("mouseenter", Swal.stopTimer)
                                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Signed In Successfully!!!"
                            });

                            setTimeout(function () {
                                window.location.href = atob("' . $encodedUrlStudent . '");
                            }, 2000);
                        });
                    </script>';
                } else {
                    $msg = "<div class='alert alert-danger'>Unknown user status.</div>";
                }
            } else {
                $sql = "INSERT INTO login_attempts (ip_address, last_attempt) VALUES ('$ipAddress', $currentTime)";
                mysqli_query($conn, $sql);

                echo '<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        swal.fire({
                            title: "Incorrect Credential!",
                            text: "Try Again!!!",
                            icon: "warning",
                            confirmButtonText: "Okay",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        })
                    });
                </script>';
            }
        } else if (mysqli_num_rows($teacherresult) === 1) {
            $res_teacher = mysqli_fetch_assoc($teacherresult);
            $teacher_id = $res_teacher['teacher_id'];

            if (password_verify($password, $res_teacher['password'])) {
                $sql = "DELETE FROM login_attempts WHERE ip_address = '$ipAddress'";
                mysqli_query($conn, $sql);

                $loginTime = date("Y-m-d H:i:s");
                $sql = "INSERT INTO userlogs_tbl (userid, username, login_time, ip_address) VALUES ('$teacher_id', '$username', '$loginTime', '$ipAddress')";
                $resultsql = mysqli_query($conn, $sql);
                $encodedUrlTeacher = base64_encode("./users/teacher/");
                if ($resultsql) {
                    $_SESSION['teacher_id'] = $res_teacher['teacher_id'];
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener("mouseenter", Swal.stopTimer)
                                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Signed In Successfully!!!"
                            });

                            setTimeout(function () {
                                window.location.href = atob("' . $encodedUrlTeacher . '");
                            }, 2000);
                        });
                    </script>';
                } else {
                    $msg = "<div class='alert alert-danger'>Unknown user status.</div>";
                }
            } else {
                $sql = "INSERT INTO login_attempts (ip_address, last_attempt) VALUES ('$ipAddress', $currentTime)";
                mysqli_query($conn, $sql);

                echo '<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        swal.fire({
                            title: "Incorrect Credential!",
                            text: "Try Again!!!",
                            icon: "warning",
                            confirmButtonText: "Okay",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        })
                    });
                </script>';
            }
            // ==========================================
            // DINAGDAG: NEW USERS LOGIN BLOCK
            // ==========================================
        } else if (mysqli_num_rows($newUserResult) === 1) {
            $res_newuser = mysqli_fetch_assoc($newUserResult);
            $email = $res_newuser['email'];

            if (password_verify($password, $res_newuser['password'])) {
                $sql = "DELETE FROM login_attempts WHERE ip_address = '$ipAddress'";
                mysqli_query($conn, $sql);

                $loginTime = date("Y-m-d H:i:s");
                $sql = "INSERT INTO userlogs_tbl (userid, username, login_time, ip_address) VALUES ('$email', '$username', '$loginTime', '$ipAddress')";
                $resultsql = mysqli_query($conn, $sql);

                // URL papunta sa page para sa mga new_users (halimbawa index page)
                $encodedUrlNewUser = base64_encode("./index");

                if ($resultsql) {
                    $_SESSION['new_users_id'] = $res_newuser['new_users_id'];
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener("mouseenter", Swal.stopTimer)
                                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: "success",
                                title: "Signed In Successfully!!!"
                            });

                            setTimeout(function () {
                                window.location.href = atob("' . $encodedUrlNewUser . '");
                            }, 2000);
                        });
                    </script>';
                } else {
                    $msg = "<div class='alert alert-danger'>Unknown user status.</div>";
                }
            } else {
                $sql = "INSERT INTO login_attempts (ip_address, last_attempt) VALUES ('$ipAddress', $currentTime)";
                mysqli_query($conn, $sql);

                echo '<script>
                    document.addEventListener("DOMContentLoaded", function () {
                        swal.fire({
                            title: "Incorrect Credential!",
                            text: "Try Again!!!",
                            icon: "warning",
                            confirmButtonText: "Okay",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        })
                    });
                </script>';
            }
        } else {
            // KAPAG WALA TALAGANG NAHANAP SA KAHIT ANONG TABLE
            echo '<script>
            document.addEventListener("DOMContentLoaded", function () {
                swal.fire({
                    title: "Incorrect Credential!",
                    text: "Try Again!!!",
                    icon: "warning",
                    confirmButtonText: "Okay",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                })
            });
        </script>';
        }
    }
}

// ==========================================
// CHANGE PASSWORD LOGIC
// ==========================================
if (isset($_POST['changepass'])) {
    $email = mysqli_real_escape_string($conn, $_POST['reset_email']);
    $code = mysqli_real_escape_string($conn, md5(rand()));

    // Check if the email exists in user_tbl
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM user_tbl WHERE email='{$email}'")) > 0) {
        $query = mysqli_query($conn, "UPDATE user_tbl SET code='{$code}' WHERE email='{$email}'");

        if ($query) {
            echo "<div style='display: none;'>";
            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'highschoolbangbangnational@gmail.com';
                $mail->Password = 'njdvqtbzbgtppobe';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('highschoolbangbangnational@gmail.com', 'Bangbang National High School');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request - Bangbang NHS';
                $mail->Body = '
                    <p>Dear User,</p>
                    <p>We received a request to reset your password for your <strong>Bangbang National High School</strong> account. 
                    If you made this request, please click the button below to create a new password. 
                    If you did not request a password reset, please ignore this message.</p>

                    <p style="text-align: center;">
                        <a href="http://localhost/boac/gms/changepassword?reset=' . $code . '" style="
                            background-color: #007bff; 
                            color: white; 
                            padding: 10px 20px; 
                            text-decoration: none; 
                            border-radius: 5px;
                            font-size: 16px;
                            display: inline-block;
                        ">
                            Reset Your Password
                        </a>
                    </p>

                    <p>For your security, this link will expire after a certain period or once used.</p>
                    <p>If you have any concerns, please contact our school IT support team.</p>

                    <br>
                    <p>Best regards,</p>
                    <p><strong>Bangbang National High School</strong></p>
                ';
                $mail->send();
                ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            title: "Success!",
                            text: "We have sent a password reset link to your email address.",
                            icon: "success",
                            confirmButtonText: "Okay",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then(() => {
                            window.location.href = 'http://localhost/boac/gms/';
                        });
                    });
                </script>
                <?php
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
                ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            title: "Failed!",
                            text: "Message could not be sent. Please try again later.",
                            icon: "error",
                            confirmButtonText: "Okay"
                        });
                    });
                </script>
                <?php
            }
        } else {
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: "Failed!",
                        text: "Something went wrong while processing your request.",
                        icon: "error",
                        confirmButtonText: "Okay"
                    });
                });
            </script>
            <?php
        }
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: "Email Not Found!",
                    text: "No account associated with this email address.",
                    icon: "warning",
                    confirmButtonText: "Okay"
                });
            });
        </script>
        <?php
    }
}
?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('#showRegister').click(function (e) {
            e.preventDefault();
            $('#loginForm').hide();
            $('#registerForm').show();
        });

        $('#showLogin').click(function (e) {
            e.preventDefault();
            $('#registerForm').hide();
            $('#loginForm').show();
        });
    });
</script>