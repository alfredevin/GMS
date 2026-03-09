<?php
include '../../config.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php'; ?>

<style>
    /* Re-using the nice styles */
    .profile-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

    .profile-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .camera-icon {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #4e73df;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid white;
    }

    .camera-icon:hover {
        background: #2e59d9;
        transform: scale(1.1);
    }

    .nav-pills .nav-link.active {
        background-color: #4e73df;
        color: white;
        box-shadow: 0 4px 6px rgba(78, 115, 223, 0.3);
    }

    .nav-pills .nav-link {
        color: #5a5c69;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 20px;
    }
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <?php

                $user_id = $_SESSION['student_id'];

                // 2. FETCH STUDENT DATA
                // Join with enrollment_tbl just in case you need section info, etc.
                $query = mysqli_query($conn, "SELECT * FROM student_tbl
                INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id WHERE student_id = '$user_id'");
                $data = mysqli_fetch_assoc($query);

                $current_name = $data['firstname'] . ' ' . $data['lastname'];
                $current_username = $data['lrn'];
                $current_pic = isset($data['profile_pic']) ? $data['profile_pic'] : 'default.png';
                $db_hash_password = $data['password']; // Assuming column is 'password'

                // --- 3. HANDLE FORM SUBMISSIONS ---

                // A. UPDATE PROFILE PICTURE ONLY (Usually students can't change names)
                if (isset($_POST['update_profile'])) {
                    // If you want to allow them to change LRN/Name, uncomment below:
                    // $new_username = mysqli_real_escape_string($conn, $_POST['username']);

                    $image_update_query = "";
                    if (!empty($_FILES['profile_pic']['name'])) {
                        $img_name = $_FILES['profile_pic']['name'];
                        $img_tmp = $_FILES['profile_pic']['tmp_name'];
                        $unique_name = time() . "_" . $img_name;

                        // Ensure this folder exists: /student/uploads/
                        $upload_dir = "./uploads/";

                        if (move_uploaded_file($img_tmp, $upload_dir . $unique_name)) {
                            $image_update_query = "profile_pic = '$unique_name'";

                            // Update DB
                            $sql = "UPDATE student_tbl SET $image_update_query WHERE student_id = '$user_id'";
                            if (mysqli_query($conn, $sql)) {
                                $msg_type = "success";
                                $msg_text = "Profile picture updated!";
                                echo "<meta http-equiv='refresh' content='1'>";
                            } else {
                                $msg_type = "error";
                                $msg_text = "Database error.";
                            }
                        }
                    }
                }

                // B. CHANGE PASSWORD
                if (isset($_POST['change_pass'])) {
                    $current_pass = $_POST['current_password'];
                    $new_pass = $_POST['new_password'];
                    $confirm_pass = $_POST['confirm_password'];

                    // Verify Old Password
                    if (!password_verify($current_pass, $db_hash_password)) {
                        $pass_msg_type = "error";
                        $pass_msg_text = "Current password is incorrect.";
                    } elseif ($new_pass !== $confirm_pass) {
                        $pass_msg_type = "error";
                        $pass_msg_text = "New passwords do not match.";
                    } else {
                        // Hash New Password
                        $new_pass_hashed = password_hash($new_pass, PASSWORD_DEFAULT);

                        $update_pass = mysqli_query($conn, "UPDATE student_tbl SET password = '$new_pass_hashed' WHERE student_id = '$user_id'");

                        if ($update_pass) {
                            $pass_msg_type = "success";
                            $pass_msg_text = "Password changed successfully!";
                        } else {
                            $pass_msg_type = "error";
                            $pass_msg_text = "Database error: " . mysqli_error($conn);
                        }
                    }
                }
                ?>
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
                    </div>

                    <div class="row">
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4 border-bottom-primary">
                                <div class="card-body text-center pt-5 pb-5">
                                    <form method="POST" enctype="multipart/form-data" id="picForm">
                                        <div class="profile-container mb-3">
                                            <?php
                                            $img_src = "../student/uploads/" . $current_pic;
                                            if (!file_exists($img_src)) $img_src = "https://ui-avatars.com/api/?name=" . $current_name . "&size=150&background=4e73df&color=fff";
                                            ?>
                                            <img src="<?= $img_src ?>" class="profile-img" id="profilePreview">

                                            <label for="uploadPic" class="camera-icon" title="Change Profile Picture">
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input type="file" id="uploadPic" name="profile_pic" style="display: none;" accept="image/*" onchange="previewImage(this)">
                                        </div>
                                    </form>

                                    <h4 class="font-weight-bold text-gray-800"><?= $current_name ?></h4>
                                    <p class="text-muted mb-1">Student</p>
                                    <p class="text-xs text-gray-500 mb-3">LRN: <?= $current_username ?></p>

                                    <hr>
                                    <div class="text-left px-4 small">
                                        <div class="mb-2"><i class="fas fa-id-card mr-2 text-primary"></i> Student ID: <?= $user_id ?></div>
                                        <div class="mb-2"><i class="fas fa-venus-mars mr-2 text-primary"></i> Sex: <?= $data['sex'] ?></div>
                                        <div><i class="fas fa-birthday-cake mr-2 text-primary"></i> Birthday: <?= $data['birthdate'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-white">
                                    <ul class="nav nav-pills" id="profileTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="photo-tab" data-toggle="pill" href="#photo" role="tab">
                                                <i class="fas fa-image mr-1"></i> Update Photo
                                            </a>
                                        </li>
                                        <li class="nav-item ml-2">
                                            <a class="nav-link" id="password-tab" data-toggle="pill" href="#password" role="tab">
                                                <i class="fas fa-lock mr-1"></i> Change Password
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <div class="tab-content">

                                        <div class="tab-pane fade show active" id="photo" role="tabpanel">
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="text-center py-4">
                                                    <img src="../img/undraw_profile.svg" width="100" class="mb-3 opacity-50">
                                                    <p class="text-muted mb-4">
                                                        To update your profile picture, click the <strong>Camera Icon</strong> on the left card, select a new image, then click the button below.
                                                    </p>
                                                    <button class="btn btn-primary px-4" type="submit" name="update_profile">
                                                        <i class="fas fa-upload mr-2"></i> Upload New Picture
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade" id="password" role="tabpanel">
                                            <form method="POST">
                                                <div class="form-group">
                                                    <label class="small mb-1 font-weight-bold">Current Password</label>
                                                    <div class="input-group">
                                                        <input class="form-control" type="password" name="current_password" id="curPass" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text bg-white" onclick="togglePass('curPass')"><i class="fas fa-eye text-gray-500"></i></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="small mb-1 font-weight-bold">New Password</label>
                                                            <div class="input-group">
                                                                <input class="form-control" type="password" name="new_password" id="newPass" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text bg-white" onclick="togglePass('newPass')"><i class="fas fa-eye text-gray-500"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="small mb-1 font-weight-bold">Confirm Password</label>
                                                            <div class="input-group">
                                                                <input class="form-control" type="password" name="confirm_password" id="conPass" required>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text bg-white" onclick="togglePass('conPass')"><i class="fas fa-eye text-gray-500"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <button class="btn btn-success btn-block" type="submit" name="change_pass">
                                                    <i class="fas fa-check-circle mr-1"></i> Update Securely
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <?php include './../template/footer.php'; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <?php include './../template/script.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Preview Image Logic
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // 2. Append File Input to Form on Submit
        // We need to move the file input from Left Card to the Form inside Tab 1 before submitting
        var photoForm = document.querySelector('#photo form');
        photoForm.addEventListener('submit', function() {
            var fileInput = document.getElementById('uploadPic');
            // Cloning or moving the input is required because it's outside the form tag
            // Easiest way: Append it directly
            this.appendChild(fileInput);
        });

        // 3. Toggle Password
        function togglePass(id) {
            var x = document.getElementById(id);
            x.type = (x.type === "password") ? "text" : "password";
        }

        // 4. Alerts
        <?php if (isset($msg_type)): ?>
            Swal.fire({
                icon: '<?= $msg_type ?>',
                title: '<?= ($msg_type == "success") ? "Success!" : "Oops..." ?>',
                text: '<?= $msg_text ?>',
                confirmButtonColor: '#4e73df'
            });
        <?php endif; ?>
        <?php if (isset($pass_msg_type)): ?>
            Swal.fire({
                icon: '<?= $pass_msg_type ?>',
                title: '<?= ($pass_msg_type == "success") ? "Success!" : "Error" ?>',
                text: '<?= $pass_msg_text ?>',
                confirmButtonColor: '#4e73df'
            });
        <?php endif; ?>
    </script>
</body>

</html>