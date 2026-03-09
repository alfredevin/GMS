<?php
include '../config.php';
 ;


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
                title: "Email or Name already exists!"
            }); 
        });
    </script>';
    } else {
        $stmt = $conn->prepare("INSERT INTO new_users (full_name,   email, username, password ) VALUES (?, ?, ?,  ?)");
        $stmt->bind_param("ssss", $full_name,   $email, $username, $password);
        if ($stmt->execute()) {
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
                title: "Successfully Registered!"
            }); 
        });
    </script>';
        }
    }
}


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
        $sql = "SELECT * FROM new_users WHERE username = '$username' AND user_status = 1 ";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $email = $row['email'];


            if (password_verify($password, $row['password'])) {
                $sql = "DELETE FROM login_attempts WHERE ip_address = '$ipAddress'";
                mysqli_query($conn, $sql);

                $loginTime = date("Y-m-d H:i:s");
                $sql = "INSERT INTO userlogs_tbl (userid, username, login_time, ip_address) VALUES ('$email', '$username', '$loginTime', '$ipAddress')";
                $resutsql = mysqli_query($conn, $sql);
                $encodedUrl = base64_encode("./index");
                if ($resutsql) {
                    $_SESSION['new_users_id'] = $row['new_users_id'];
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

?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#showRegister').click(function(e) {
            e.preventDefault();
            $('#loginForm').hide();
            $('#registerForm').show();
        });

        $('#showLogin').click(function(e) {
            e.preventDefault();
            $('#registerForm').hide();
            $('#loginForm').show();
        });
    });
</script>