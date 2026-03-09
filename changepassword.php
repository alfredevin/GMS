<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
session_start();
date_default_timezone_set("Asia/Manila");
include 'config.php';


$msg = "";

 
if (isset($_GET['reset'])) {
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM user_tbl WHERE code='{$_GET['reset']}'")) > 0) {
        if (isset($_POST['submit'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm-password'];

            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $query = mysqli_query($conn, "UPDATE user_tbl SET password='{$hashed_password}', code='' WHERE code='{$_GET['reset']}'");

                if ($query) {
                    ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            swal.fire({
                                title: "Success",
                                text: "Password changed successfully. You can sign-in now!",
                                icon: "success",
                                confirmButtonText: "Okay",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = ('http://localhost/boac/gms/');
                                }
                            })
                        });  
                    </script>
                    <?php
                }
            } else {
                ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        swal.fire({
                            title: "Failed",
                            text: "Password and Confirmation Password did not match!",
                            icon: "error",
                            confirmButtonText: "Okay",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                            }
                        })
                    });  
                </script>
                <?php
            }
        }
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                swal.fire({
                    title: "Failed",
                    text: "Invalid link!",
                    icon: "error",
                    confirmButtonText: "Okay",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = ('http://localhost/boac/gms/');
                    }
                })
            });  
        </script>
        <?php
    }
} else {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            swal.fire({
                title: "Failed",
                text: "Invalid link!",
                icon: "error",
                confirmButtonText: "Okay",
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = ('http://localhost/boac/gms/');
                }
            })
        });  
    </script>
    <?php
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
                    <h2 class="" style="color:white;margin-top:20px;">New Password</h2>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm-password" id="password" placeholder="Confirm Password" />
                    </div>




                    <input type="submit" name="submit" value="SAVE PASSWORD" class="btn solid" style="background:rgb(201, 96, 21);" />


                </form>
                <!-- Forgot Password Form -->


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


</body>

</html>