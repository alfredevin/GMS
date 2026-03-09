<?php
session_start();
include '../config.php';

// 1. CHECK IF LOGGED IN
if (!isset($_SESSION['new_users_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['new_users_id'];

// 2. FETCH ACCOUNT DATA
$user_query = mysqli_query($conn, "SELECT * FROM new_users WHERE new_users_id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

// 3. FETCH ENROLLMENT DATA
$enroll_query = mysqli_query($conn, "SELECT * FROM enrollment_tbl WHERE user_ids = '$user_id' ORDER BY enrollmentId DESC LIMIT 1");
$enrollment = mysqli_fetch_assoc($enroll_query);
$has_enrollment = ($enrollment) ? true : false;
$enrollment_id = $has_enrollment ? $enrollment['enrollmentId'] : 0;

// --- FORM HANDLING ---

// A. UPDATE PROFILE PICTURE
if (isset($_POST['update_pic'])) {
    if (!empty($_FILES['profile_pic']['name'])) {
        $img_name = $_FILES['profile_pic']['name'];
        $img_tmp = $_FILES['profile_pic']['tmp_name'];
        $unique_name = time() . "_" . $img_name;
        $upload_dir = "assets/img/profiles/";

        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if (move_uploaded_file($img_tmp, $upload_dir . $unique_name)) {
            mysqli_query($conn, "UPDATE new_users SET profile_pic = '$unique_name' WHERE new_users_id = '$user_id'");
            $alert_title = "Success";
            $alert_msg = "Profile picture updated!";
            $alert_icon = "success";
        }
    }
}

// B. UPLOAD / UPDATE REQUIREMENT
if (isset($_POST['submit_requirement'])) {
    $req_name = $_POST['req_name'];

    if (!empty($_FILES['req_file']['name'])) {
        $file_name = $_FILES['req_file']['name'];
        $file_tmp = $_FILES['req_file']['tmp_name'];
        $unique_file = time() . "_" . $file_name;
        $doc_dir = "./uploads/"; // Siguraduhin na tama ang path na ito (kung saan nag uupload ang enrollment)

        if (move_uploaded_file($file_tmp, $doc_dir . $unique_file)) {
            // Check if file already exists for this requirement
            $check_file = mysqli_query($conn, "SELECT * FROM enrollment_uploaded_files WHERE enrollment_id = '$enrollment_id' AND requirement_name = '$req_name'");

            if (mysqli_num_rows($check_file) > 0) {
                // Update existing
                mysqli_query($conn, "UPDATE enrollment_uploaded_files SET file_name = '$unique_file' WHERE enrollment_id = '$enrollment_id' AND requirement_name = '$req_name'");
            } else {
                // Insert new
                mysqli_query($conn, "INSERT INTO enrollment_uploaded_files (enrollment_id, requirement_name, file_name) VALUES ('$enrollment_id', '$req_name', '$unique_file')");
            }

            $alert_title = "Uploaded!";
            $alert_msg = "$req_name has been successfully uploaded.";
            $alert_icon = "success";
        } else {
            $alert_title = "Error";
            $alert_msg = "Failed to upload file.";
            $alert_icon = "error";
        }
    }
}

// C. CHANGE PASSWORD
if (isset($_POST['change_pass'])) {
    // (Same password logic as before)
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Note: Adjust this if you are using Hashed passwords already or Plain text
    if ($current_pass !== $user_data['password']) {
        $alert_title = "Error";
        $alert_msg = "Current password is incorrect.";
        $alert_icon = "error";
    } elseif ($new_pass !== $confirm_pass) {
        $alert_title = "Error";
        $alert_msg = "New passwords do not match.";
        $alert_icon = "error";
    } else {
        mysqli_query($conn, "UPDATE new_users SET password = '$new_pass' WHERE new_users_id = '$user_id'");
        $alert_title = "Success";
        $alert_msg = "Password changed successfully!";
        $alert_icon = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>My Account - BNHS</title>

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
        }

        .profile-header-bg {
            background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
            height: 180px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -90px;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .profile-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 15px;
        }

        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            background: #fff;
        }

        .camera-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: 0.2s;
        }

        .camera-btn:hover {
            background: #0d6efd;
            color: #fff;
            transform: scale(1.1);
        }

        .nav-pills .nav-link.active {
            background-color: #e7f1ff;
            color: #0d6efd;
        }

        .nav-pills .nav-link {
            color: #6c757d;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 20px;
        }

        /* Requirement Table Styles */
        .req-table td {
            vertical-align: middle;
            padding: 12px;
        }

        .status-dot {
            height: 10px;
            width: 10px;
            background-color: #ccc;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .status-dot.uploaded {
            background-color: #198754;
        }

        .status-dot.missing {
            background-color: #dc3545;
        }
    </style>
</head>

<body>

    <header id="header" class="header d-flex align-items-center fixed-top bg-white shadow-sm">
        <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
            <a href="index.php" class="logo d-flex align-items-center text-decoration-none">
                <h1 class="sitename text-primary m-0">BNHS</h1>
            </a>
            <nav id="navmenu" class="navmenu">
                <ul class="list-unstyled m-0">
                    <li><a href="index.php" class="text-dark fw-bold text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main mt-5">
        <div class="profile-header-bg"></div>

        <div class="container pb-5">
            <div class="row gy-4">

                <div class="col-lg-4">
                    <div class="card text-center p-4 h-100">
                        <form method="POST" enctype="multipart/form-data" id="picForm">
                            <div class="profile-wrapper">
                                <?php
                                $pic = !empty($user_data['profile_pic']) ? "assets/img/profiles/" . $user_data['profile_pic'] : "https://ui-avatars.com/api/?name=" . $user_data['full_name'] . "&size=140&background=random&color=fff";
                                ?>
                                <img src="<?= $pic ?>" class="profile-img">
                                <label for="uploadPic" class="camera-btn"><i class="bi bi-camera-fill"></i></label>
                                <input type="file" id="uploadPic" name="profile_pic" style="display:none" accept="image/*" onchange="document.getElementById('picForm').submit();">
                                <input type="hidden" name="update_pic" value="1">
                            </div>
                        </form>

                        <h4 class="fw-bold text-dark mb-1"><?= $user_data['full_name'] ?></h4>
                        <p class="text-muted small mb-4"><?= $user_data['email'] ?></p>

                        <?php if ($has_enrollment): ?>
                            <div class="d-inline-block px-3 py-1 rounded-pill bg-light border text-dark fw-bold text-uppercase" style="font-size: 12px;">
                                Status: <?= ($enrollment['enrollment_status'] == 1) ? '<span class="text-warning">Pending</span>' : '<span class="text-success">Enrolled</span>' ?>
                            </div>
                        <?php else: ?>
                            <a href="enroll.php?sy=<?= date('Y') ?>" class="btn btn-primary rounded-pill px-4 w-100">Enroll Now</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card p-3">
                        <div class="card-header bg-white border-0">
                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                <li class="nav-item"><button class="nav-link active" id="pills-enrollment-tab" data-bs-toggle="pill" data-bs-target="#pills-enrollment" type="button">My Enrollment</button></li>
                                <li class="nav-item"><button class="nav-link" id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-security" type="button">Security</button></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content" id="pills-tabContent">

                                <div class="tab-pane fade show active" id="pills-enrollment">
                                    <?php if ($has_enrollment): ?>

                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <h6 class="text-uppercase text-muted small fw-bold">Student Information</h6>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Name</small>
                                                <div class="fw-bold text-dark"><?= $enrollment['lastname'] ?>, <?= $enrollment['firstname'] ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">LRN</small>
                                                <div class="fw-bold text-dark"><?= $enrollment['lrn'] ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Grade Level</small>
                                                <div class="fw-bold text-dark">Grade <?= $enrollment['grade'] ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Date Applied</small>
                                                <div class="fw-bold text-dark"><?= date('M d, Y', strtotime($enrollment['date_created'])) ?></div>
                                            </div>
                                        </div>

                                        <hr class="bg-light">

                                        <div class="mt-4">
                                            <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                                <i class="bi bi-folder2-open me-1"></i> Submitted Requirements
                                            </h6>

                                            <div class="table-responsive">
                                                <table class="table req-table table-borderless align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Requirement</th>
                                                            <th class="text-center">Status</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        // Get All Requirements
                                                        $req_query = mysqli_query($conn, "SELECT * FROM enrollment_requirement_tbl");

                                                        while ($req = mysqli_fetch_assoc($req_query)) {
                                                            $req_name = $req['enrollment_requirement_name'];

                                                            // Check if User Uploaded this
                                                            $check_up = mysqli_query($conn, "SELECT * FROM enrollment_uploaded_files WHERE enrollment_id = '$enrollment_id' AND requirement_name = '$req_name'");
                                                            $uploaded = mysqli_fetch_assoc($check_up);
                                                            $is_uploaded = !empty($uploaded);
                                                        ?>
                                                            <tr class="border-bottom">
                                                                <td>
                                                                    <div class="fw-bold text-dark"><?= $req_name ?></div>
                                                                    <?php if ($is_uploaded): ?>
                                                                        <small class="text-muted"><i class="bi bi-paperclip"></i> <?= substr($uploaded['file_name'], 11) // remove timestamp prefix 
                                                                                                                                    ?></small>
                                                                    <?php else: ?>
                                                                        <small class="text-danger fst-italic">Required</small>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if ($is_uploaded): ?>
                                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Uploaded</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Missing</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-end">
                                                                    <form method="POST" enctype="multipart/form-data" class="d-flex justify-content-end align-items-center">
                                                                        <input type="hidden" name="req_name" value="<?= $req_name ?>">

                                                                        <input type="file" name="req_file" id="file_<?= $req['enrollment_requirement_id'] ?>" class="d-none" onchange="this.form.submit()">
                                                                        <input type="hidden" name="submit_requirement" value="1">

                                                                        <?php if ($is_uploaded): ?>
                                                                            <a href="./uploads/<?= $uploaded['file_name'] ?>" target="_blank" class="btn btn-sm btn-light text-primary me-1" title="View"><i class="bi bi-eye"></i></a>
                                                                            <label for="file_<?= $req['enrollment_requirement_id'] ?>" class="btn btn-sm btn-light text-dark" title="Change File" style="cursor:pointer;">
                                                                                <i class="bi bi-pencil"></i>
                                                                            </label>
                                                                        <?php else: ?>
                                                                            <label for="file_<?= $req['enrollment_requirement_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill" style="cursor:pointer;">
                                                                                <i class="bi bi-upload me-1"></i> Upload
                                                                            </label>
                                                                        <?php endif; ?>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="bi bi-journal-x text-muted display-1"></i>
                                            <p class="mt-3 text-muted">No active enrollment found.</p>
                                            <a href="enroll.php?sy=<?= date('Y') ?>" class="btn btn-primary rounded-pill">Enroll Now</a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="tab-pane fade" id="pills-security">
                                    <form method="POST" class="row g-3">
                                        <div class="col-12">
                                            <h6 class="text-uppercase text-muted small fw-bold">Password Settings</h6>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control bg-light border-0" name="current_password" id="curPass" required>
                                                <button class="btn btn-light border-0" type="button" onclick="togglePass('curPass')"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control bg-light border-0" name="new_password" id="newPass" required>
                                                <button class="btn btn-light border-0" type="button" onclick="togglePass('newPass')"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control bg-light border-0" name="confirm_password" id="conPass" required>
                                                <button class="btn btn-light border-0" type="button" onclick="togglePass('conPass')"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-4 text-end">
                                            <button type="submit" name="change_pass" class="btn btn-success px-4 rounded-pill shadow-sm">Save Changes</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function togglePass(id) {
            var x = document.getElementById(id);
            x.type = (x.type === "password") ? "text" : "password";
        }

        <?php if (isset($alert_title)): ?>
            Swal.fire({
                icon: '<?= $alert_icon ?>',
                title: '<?= $alert_title ?>',
                text: '<?= $alert_msg ?>',
                confirmButtonColor: '#0d6efd',
                timer: 3000
            });
        <?php endif; ?>
    </script>

</body>

</html>