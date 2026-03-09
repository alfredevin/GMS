<?php
session_start();
date_default_timezone_set("Asia/Manila");
include 'config.php';




use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './mailer/src/Exception.php';
require './mailer/src/PHPMailer.php';
require './mailer/src/SMTP.php';
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
        $sql = "SELECT * FROM user_tbl WHERE username = '$username' AND useractive = '1'";
        $result = mysqli_query($conn, $sql);

        $teacher = "SELECT * FROM teacher_tbl WHERE teacher_id  = '$username' ";
        $teacherresult = mysqli_query($conn, $teacher);


        $student = "SELECT * FROM student_tbl WHERE student_id  = '$username' ";
        $studentResult = mysqli_query($conn, $student);

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
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
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
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
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
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    });
                </script>';
            }
        } else {
            echo '<script>
            document.addEventListener("DOMContentLoaded", function () {
                swal.fire({
                    title: "Incorrect Credential!",
                    text: "Try Again!!!",
                    icon: "warning",
                    confirmButtonText: "Okay",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                })
            });
        </script>';
        }
    }
}

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
                $mail->SMTPDebug = 0; // ✅ Turn off verbose debug output
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'highschoolbangbangnational@gmail.com';
                $mail->Password = 'njdvqtbzbgtppobe'; // ⚠️ Consider using env/config file for safety
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
                    document.addEventListener('DOMContentLoaded', function() {
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
                    document.addEventListener('DOMContentLoaded', function() {
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
                document.addEventListener('DOMContentLoaded', function() {
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
            document.addEventListener('DOMContentLoaded', function() {
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <title>Bangbang National Highschool</title>
    <link href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9BRQM_uqdXGt-qLZgiHczlYTKTnEcxifgsQ&s" rel="icon">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    </script>
    <script src="sweetalert2.min.js"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">
    <script src="sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font Awesome 6 Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body,
        input {
            font-family: "Poppins", sans-serif;
        }

        .container {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
        }

        .forms-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .signin-signup {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 75%;
            width: 30%;
            transition: 1s 0.7s ease-in-out;
            display: grid;
            grid-template-columns: 1fr;
            z-index: 5;
        }

        form {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0rem 5rem;
            transition: all 0.2s 0.7s;
            overflow: hidden;
            grid-column: 1 / 2;
            grid-row: 1 / 2;
        }

        form.sign-up-form {
            opacity: 0;
            z-index: 1;
        }

        form.sign-in-form {
            z-index: 2;
        }

        .title {
            font-size: 2.2rem;
            color: #444;
            margin-bottom: 10px;
        }

        .input-field {
            max-width: 380px;
            width: 100%;
            background-color: #f0f0f0;
            margin: 10px 0;
            height: 55px;
            border-radius: 55px;
            display: grid;
            grid-template-columns: 15% 85%;
            padding: 0 0.4rem;
            position: relative;
        }

        .input-field i {
            text-align: center;
            line-height: 55px;
            color: #acacac;
            transition: 0.5s;
            font-size: 1.1rem;
        }

        .input-field input {
            background: none;
            outline: none;
            border: none;
            line-height: 1;
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .input-field input::placeholder {
            color: #aaa;
            font-weight: 500;
        }

        .social-text {
            padding: 0.7rem 0;
            font-size: 1rem;
        }

        .social-media {
            display: flex;
            justify-content: center;
        }

        .social-icon {
            height: 46px;
            width: 46px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 0.45rem;
            color: #333;
            border-radius: 50%;
            border: 1px solid #333;
            text-decoration: none;
            font-size: 1.1rem;
            transition: 0.3s;
        }

        .social-icon:hover {
            color: #4481eb;
            border-color: #4481eb;
        }

        .btn {
            width: 150px;
            background-color: #5995fd;
            border: none;
            outline: none;
            height: 49px;
            border-radius: 49px;
            color: #fff;
            text-transform: uppercase;
            font-weight: 600;
            margin: 10px 0;
            cursor: pointer;
            transition: 0.5s;
        }

        .btn:hover {
            background-color: #4d84e2;
        }

        .panels-container {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        .container:before {
            content: "";
            position: absolute;
            height: 2000px;
            width: 2000px;
            top: -10%;
            right: 48%;
            transform: translateY(-50%);
            background-image: linear-gradient(-45deg, rgb(201, 96, 21) 0%, rgb(255, 111, 0) 100%);
            transition: 1.8s ease-in-out;
            border-radius: 50%;
            z-index: 6;
        }

        .image {
            width: 100%;
            transition: transform 1.1s ease-in-out;
            transition-delay: 0.4s;
        }

        .panel {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-around;
            text-align: center;
            z-index: 6;
        }

        .left-panel {
            pointer-events: all;
            padding: 3rem 17% 2rem 12%;
        }

        .right-panel {
            pointer-events: none;
            padding: 3rem 12% 2rem 17%;
        }

        .panel .content {
            color: #fff;
            transition: transform 0.9s ease-in-out;
            transition-delay: 0.6s;
        }

        .panel h3 {
            font-weight: 600;
            line-height: 1;
            font-size: 1.5rem;
        }

        .panel p {
            font-size: 0.95rem;
            padding: 0.7rem 0;
        }

        .btn.transparent {
            margin: 0;
            background: none;
            border: 2px solid #fff;
            width: 130px;
            height: 41px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .right-panel .image,
        .right-panel .content {
            transform: translateX(800px);
        }

        /* ANIMATION */

        .container.sign-up-mode:before {
            transform: translate(100%, -50%);
            right: 52%;
        }

        .container.sign-up-mode .left-panel .image,
        .container.sign-up-mode .left-panel .content {
            transform: translateX(-800px);
        }

        .container.sign-up-mode .signin-signup {
            left: 25%;
        }

        .container.sign-up-mode form.sign-up-form {
            opacity: 1;
            z-index: 2;
        }

        .container.sign-up-mode form.sign-in-form {
            opacity: 0;
            z-index: 1;
        }

        .container.sign-up-mode .right-panel .image,
        .container.sign-up-mode .right-panel .content {
            transform: translateX(0%);
        }

        .container.sign-up-mode .left-panel {
            pointer-events: none;
        }

        .container.sign-up-mode .right-panel {
            pointer-events: all;
        }

        @media (max-width: 870px) {
            .container {
                min-height: 800px;
                height: 100vh;
            }

            .signin-signup {
                width: 100%;
                top: 95%;
                transform: translate(-50%, -100%);
                transition: 1s 0.8s ease-in-out;
            }

            .signin-signup,
            .container.sign-up-mode .signin-signup {
                left: 50%;
            }

            .panels-container {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr 2fr 1fr;
            }

            .panel {
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                padding: 2.5rem 8%;
                grid-column: 1 / 2;
            }

            .right-panel {
                grid-row: 3 / 4;
            }

            .left-panel {
                grid-row: 1 / 2;
            }

            .image {
                width: 200px;
                transition: transform 0.9s ease-in-out;
                transition-delay: 0.6s;
            }

            .panel .content {
                padding-right: 15%;
                transition: transform 0.9s ease-in-out;
                transition-delay: 0.8s;
            }

            .panel h3 {
                font-size: 1.2rem;
            }

            .panel p {
                font-size: 0.7rem;
                padding: 0.5rem 0;
            }

            .btn.transparent {
                width: 110px;
                height: 35px;
                font-size: 0.7rem;
            }

            .container:before {
                width: 1500px;
                height: 1500px;
                transform: translateX(-50%);
                left: 30%;
                bottom: 68%;
                right: initial;
                top: initial;
                transition: 2s ease-in-out;
            }

            .container.sign-up-mode:before {
                transform: translate(-50%, 100%);
                bottom: 32%;
                right: initial;
            }

            .container.sign-up-mode .left-panel .image,
            .container.sign-up-mode .left-panel .content {
                transform: translateY(-300px);
            }

            .container.sign-up-mode .right-panel .image,
            .container.sign-up-mode .right-panel .content {
                transform: translateY(0px);
            }

            .right-panel .image,
            .right-panel .content {
                transform: translateY(300px);
            }

            .container.sign-up-mode .signin-signup {
                top: 5%;
                transform: translate(-50%, 0);
            }
        }

        @media (max-width: 570px) {
            form {
                padding: 0 1.5rem;
            }

            .image {
                display: none;
            }

            .panel .content {
                padding: 0.5rem 1rem;
            }

            .container {
                padding: 1.5rem;
            }

            .container:before {
                bottom: 72%;
                left: 50%;
            }

            .container.sign-up-mode:before {
                bottom: 28%;
                left: 50%;
            }
        }
    </style>
</head>

<body style="background: url(https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRoRsljm7GdU0EL_EtQ6lO2af-nWJD9EqlE8A&s) no-repeat;background-size:cover;">
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form method="POST" autocomplete="off" class="sign-in-form" style="background-color: rgba(0, 0, 0, 0.4);">
                    <h2 class="title " style="color:white;">LOGIN</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" />
                    </div>

                    <!-- Show Password Checkbox -->
                    <div class="form-check mt-2" style="color:white;">
                        <input class="form-check-input" type="checkbox" id="showPassword">
                        <label class="form-check-label  " for="showPassword">
                            Show Password
                        </label>
                    </div>
                    <script>
                        const checkbox = document.getElementById('showPassword');
                        const passwordField = document.getElementById('password');

                        checkbox.addEventListener('change', function() {
                            passwordField.type = this.checked ? 'text' : 'password';
                        });
                    </script>


                    <input type="submit" name="submit" value="Login" class="btn solid" style="background:rgb(201, 96, 21);" />

                    <p class="social-text" style="margin:0;color:white;">Forgot you password??</p>

                    <div class="social-media" style="margin:0;">
                        <a href="#" style="margin:0;color:orange">
                            Click Here...
                        </a>
                    </div>
                </form>
                <!-- Forgot Password Form -->
                <form method="POST" class="forgot-password-form" style="display:none; background-color: rgba(0, 0, 0, 0.4);">
                    <h2 class="text-center" style="color:white;">Forgot Password</h2>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="reset_email" placeholder="Enter your email" required />
                    </div>
                    <input type="submit" name="changepass" value="Send Reset Link" class="btn solid" style="background:rgb(201, 96, 21);" />

                    <p class="social-text" style="margin:0;color:white;">Remembered your password?</p>
                    <div class="social-media" style="margin:0;">
                        <a href="#" id="backToLogin" style="margin:0;color:orange">Back to Login</a>
                    </div>
                </form>

            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9BRQM_uqdXGt-qLZgiHczlYTKTnEcxifgsQ&s"
                        style="border-radius:50%;" width="100" alt="">
                    <h3 style="text-transform:uppercase;font-family:roman;">Bangbang National High School
                    </h3>
                    <p>Bangbang National High School is a school in Gasan, Marinduque, Mimaropa. Bangbang National High School is situated nearby to Libtangin Bridge, as well as near the place of worship Birhan De Barangay Catholic Chapel.</p>
                </div>
            </div>

        </div>
    </div>
    <script>
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector(".container");

        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });
    </script>

    <script>
        const forgotLink = document.querySelector('.social-media a');
        const forgotForm = document.querySelector('.forgot-password-form');
        const loginForm = document.querySelector('.sign-in-form');
        const backToLogin = document.querySelector('#backToLogin');

        forgotLink.addEventListener('click', function(e) {
            e.preventDefault();
            loginForm.style.display = 'none';
            forgotForm.style.display = 'flex';
        });

        backToLogin.addEventListener('click', function(e) {
            e.preventDefault();
            forgotForm.style.display = 'none';
            loginForm.style.display = 'flex';
        });
    </script>

</body>

</html>