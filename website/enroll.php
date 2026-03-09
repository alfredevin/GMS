<?php

session_start();
include '../config.php';
$new_users_id = $_SESSION['new_users_id'];
$users = "SELECT * FROM new_users WHERE new_users_id = '$new_users_id'  ";
$result_user = mysqli_query($conn, $users);
$row_user = mysqli_fetch_assoc($result_user);
$user_name = $row_user["full_name"];
$user_email = $row_user["email"];


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './../mailer/src/Exception.php';
require './../mailer/src/PHPMailer.php';
require './../mailer/src/SMTP.php';

$sy = $_GET['sy'];
if (isset($_POST['submit'])) {

    $lastname = strtoupper($_POST['lastname']);
    $firstname = strtoupper($_POST['firstname']);
    $middlename = strtoupper($_POST['middlename']);
    $extname = strtoupper($_POST['extname']);
    $user_ids = strtoupper($_POST['user_ids']);
    $grade = $_POST['grade'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $mothertongue = strtoupper($_POST['mothertongue']);
    $birthplace = strtoupper($_POST['birthplace']);
    $ip = $_POST['ip'];
    $is_4ps = $_POST['is_4ps'];
    $has_disability = $_POST['has_disability'];
    $disability_type = '';
    if (!empty($_POST['disability_type'])) {
        $disability_type = implode(', ', $_POST['disability_type']);
    }
    $disability_specify = isset($_POST['disability_specify']) ? strtoupper($_POST['disability_specify']) : '';
    $current_address = strtoupper($_POST['current_address']);
    $permanent_address = strtoupper($_POST['permanent_address']);

    // Parent info is now optional, handled empty strings gracefully
    $mother_lastname = strtoupper($_POST['mother_lastname'] ?? '');
    $mother_firstname = strtoupper($_POST['mother_firstname'] ?? '');
    $mother_middlename = strtoupper($_POST['mother_middlename'] ?? '');
    $mother_contact = $_POST['mother_contact'] ?? '';

    $father_lastname = strtoupper($_POST['father_lastname'] ?? '');
    $father_firstname = strtoupper($_POST['father_firstname'] ?? '');
    $father_middlename = strtoupper($_POST['father_middlename'] ?? '');
    $father_contact = $_POST['father_contact'] ?? '';

    $guardian_lastname = strtoupper($_POST['guardian_lastname'] ?? '');
    $guardian_firstname = strtoupper($_POST['guardian_firstname'] ?? '');
    $guardian_middlename = strtoupper($_POST['guardian_middlename'] ?? '');
    $guardian_contact = $_POST['guardian_contact'] ?? '';

    $lrn = strtoupper($_POST['lrn']);

    $sql = "INSERT INTO enrollment_tbl (
        lastname, firstname, middlename, extname, birthdate, age, sex, mothertongue, birthplace, ip, is_4ps, has_disability, disability_type, disability_specify, current_address, permanent_address,
        mother_lastname, mother_firstname, mother_middlename, mother_contact,
        father_lastname, father_firstname, father_middlename, father_contact,
        guardian_lastname, guardian_firstname, guardian_middlename, guardian_contact,
         user_ids,grade,stud_sy,lrn) VALUES (
        '$lastname', '$firstname', '$middlename', '$extname', '$birthdate', '$age', '$sex', '$mothertongue', '$birthplace', '$ip', '$is_4ps', '$has_disability', '$disability_type', '$disability_specify', '$current_address', '$permanent_address',
        '$mother_lastname', '$mother_firstname', '$mother_middlename', '$mother_contact',
        '$father_lastname', '$father_firstname', '$father_middlename', '$father_contact',
        '$guardian_lastname', '$guardian_firstname', '$guardian_middlename', '$guardian_contact',
        '$user_ids','$grade','$sy','$lrn')";
    $result_enroll = mysqli_query($conn, $sql);
    $enrollment_id = mysqli_insert_id($conn);

    if ($result_enroll) {
        $names = $_POST['requirement_names'];
        $files = $_FILES['requirements'];
        $target_dir = './uploads/';

        for ($i = 0; $i < count($files['name']); $i++) {
            $name = $files['name'][$i];
            $tmp = $files['tmp_name'][$i];
            $label = $names[$i];

            if (!empty($name)) {
                $unique = uniqid("req_", true) . '_' . basename($name);
                $target_path = $target_dir . $unique;

                if (move_uploaded_file($tmp, $target_path)) {
                    $insert_file = "INSERT INTO enrollment_uploaded_files (enrollment_id, requirement_name, file_name)
                                VALUES ('$enrollment_id', '$label', '$unique')";
                    mysqli_query($conn, $insert_file);
                }
            }
        }
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

            $mail->Subject = ' Enrollment Form Successfully Submitted';

            $mail->Body = "
<div style='font-family: Arial, sans-serif; font-size: 15px; color: #333; line-height: 1.6;'>
    <p>👋 Hello <strong>$user_name</strong>,</p>

    <p>✅ We have successfully received your <strong>Enrollment Form</strong> for School Year <strong>$sy</strong> at <strong>Bangbang National High School</strong>.</p>

    <p>📝 Your application is currently under review. Our enrollment team will process your submission, and you'll receive another email once your enrollment is approved.</p>

    <p>📌 If you need to update any information or have any questions, feel free to reach out via email or visit our registrar’s office during office hours.</p>

    <p>🎓 Thank you for choosing Bangbang National High School. We’re excited to have you on board!</p>

    <p style='color: #555;'>
        Best regards,<br>
        <strong>BNHS Enrollment Team</strong>
    </p>
</div>
";

            $mail->send();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: 'Enrollment submitted successfully and email has been sent.',
                icon: 'success',
                confirmButtonText: 'Okay'
            }).then(() => {
                window.location.href = 'index';
            });
        });
    </script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Email failed to send: " . $mail->ErrorInfo . "');</script>";
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Bangbang National Highschool - Enrollment Wizard</title>

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        /* Custom Styles for Wizard */
        .step-container {
            display: none;
            /* Hide all steps initially */
        }

        .step-container.active {
            display: block;
            /* Show only active step */
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Progress Bar Styles */
        .progress-wrapper {
            margin-bottom: 30px;
        }

        .progress-step {
            width: 25%;
            float: left;
            text-align: center;
            position: relative;
        }

        .progress-step .circle {
            width: 30px;
            height: 30px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: inline-block;
            line-height: 30px;
            color: #6c757d;
            font-weight: bold;
            transition: all 0.3s;
        }

        .progress-step.active .circle {
            background-color: #0d6efd;
            color: white;
        }

        .progress-step.completed .circle {
            background-color: #198754;
            color: white;
        }

        .progress-step p {
            font-size: 12px;
            margin-top: 5px;
            color: #6c757d;
        }

        .progress-step.active p {
            color: #0d6efd;
            font-weight: bold;
        }

        /* Form Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            /* For smooth edges */
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px;
        }

        .form-section-title {
            color: #0d6efd;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-nav {
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 600;
        }
    </style>
</head>

<body class="index-page" style="background-color: #f8f9fa;">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
            <a href="index.html" class="logo d-flex align-items-center">
                <h1 class="sitename">BNHS</h1>
            </a>
        </div>
    </header>

    <main class="main">
        <section id="enrollment" class="section" style="padding-top: 120px; padding-bottom: 60px;">
            <div class="container" data-aos="fade-up">

                <div class="text-center mb-5">
                    <h2>Enrollment Application</h2>
                    <p class="text-muted">School Year: <?php echo htmlspecialchars($sy ?? 'N/A'); ?></p>
                </div>

                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8">
                        <div class="progress-wrapper clearfix">
                            <div class="progress-step active" id="progress-step-1">
                                <div class="circle">1</div>
                                <p>Learner Info</p>
                            </div>
                            <div class="progress-step" id="progress-step-2">
                                <div class="circle">2</div>
                                <p>Address</p>
                            </div>
                            <div class="progress-step" id="progress-step-3">
                                <div class="circle">3</div>
                                <p>Parents</p>
                            </div>
                            <div class="progress-step" id="progress-step-4">
                                <div class="circle">4</div>
                                <p>Finish</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <form method="POST" enctype="multipart/form-data" id="enrollmentForm">
                            <input type="hidden" name="user_ids" value="<?php echo $_SESSION['new_users_id']; ?>">

                            <div class="card step-container active" id="step-1">
                                <div class="card-header">
                                    <h5 class="form-section-title"><i class="bi bi-person-circle"></i> Learner's Basic Information</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Grade Level <span class="text-danger">*</span></label>
                                            <select name="grade" class="form-select" required>
                                                <option value="" disabled selected>Select Grade</option>
                                                <option value="7">Grade 7</option>
                                                <option value="8">Grade 8</option>
                                                <option value="9">Grade 9</option>
                                                <option value="10">Grade 10</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">LRN <span class="text-danger">*</span></label>
                                            <input oninput="this.value = this.value.toUpperCase()" type="text" name="lrn" class="form-control" placeholder="12-digit LRN" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input oninput="this.value = this.value.toUpperCase()" type="text" name="lastname" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input oninput="this.value = this.value.toUpperCase()" type="text" name="firstname" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Middle Name</label>
                                            <input oninput="this.value = this.value.toUpperCase()" type="text" name="middlename" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Ext Name</label>
                                            <input oninput="this.value = this.value.toUpperCase()" type="text" name="extname" class="form-control" placeholder="e.g. Jr.">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                                            <input type="date" name="birthdate" id="birthdate" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Age</label>
                                            <input type="number" name="age" id="age" class="form-control bg-light" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Sex <span class="text-danger">*</span></label>
                                            <select name="sex" class="form-select" required>
                                                <option value="" selected disabled>Select</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Mother Tongue <span class="text-danger">*</span></label>
                                            <input oninput="this.value = this.value.toUpperCase()" type="text" name="mothertongue" class="form-control" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Place of Birth <span class="text-danger">*</span></label>
                                            <input oninput="this.value = this.value.toUpperCase()" type="text" name="birthplace" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label d-block">Indigenous People (IP)? <span class="text-danger">*</span></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="ip" id="ip_yes" value="Yes" required>
                                                <label class="form-check-label" for="ip_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="ip" id="ip_no" value="No">
                                                <label class="form-check-label" for="ip_no">No</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label d-block">4Ps Beneficiary? <span class="text-danger">*</span></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="is_4ps" id="is_4ps_yes" value="Yes" required>
                                                <label class="form-check-label" for="is_4ps_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="is_4ps" id="is_4ps_no" value="No">
                                                <label class="form-check-label" for="is_4ps_no">No</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label d-block">Learner with Disability? <span class="text-danger">*</span></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="has_disability" id="disabilityYes" value="Yes" required>
                                                <label class="form-check-label" for="disabilityYes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="has_disability" id="disabilityNo" value="No" checked>
                                                <label class="form-check-label" for="disabilityNo">No</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-2" id="disability-options" style="display: none;">
                                            <div class="p-3 bg-light rounded border">
                                                <label class="form-label mb-2">Select Disability Type:</label><br>
                                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="disability_type[]" value="Blind"><label class="form-check-label">Blind</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="disability_type[]" value="Low Vision"><label class="form-check-label">Low Vision</label></div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="disability_type[]" value="Others">
                                                    <label class="form-check-label">Others:</label>
                                                    <input type="text" name="disability_specify" class="form-control form-control-sm d-inline-block w-auto ms-2" placeholder="Specify">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end p-3">
                                    <button type="button" class="btn btn-primary btn-nav" onclick="nextStep(2)">Next: Address <i class="bi bi-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="card step-container" id="step-2">
                                <div class="card-header">
                                    <h5 class="form-section-title"><i class="bi bi-geo-alt-fill"></i> Address Information</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label">Current Address <span class="text-danger">*</span></label>
                                        <textarea name="current_address" id="current_address" class="form-control" rows="3" placeholder="House No., Street, Barangay, Municipality, Province" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="sameAddress">
                                            <label class="form-check-label" for="sameAddress">Permanent Address is same as Current Address</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Permanent Address <span class="text-danger">*</span></label>
                                        <textarea name="permanent_address" id="permanent_address" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between p-3">
                                    <button type="button" class="btn btn-outline-secondary btn-nav" onclick="prevStep(1)"><i class="bi bi-arrow-left"></i> Back</button>
                                    <button type="button" class="btn btn-primary btn-nav" onclick="nextStep(3)">Next: Parents <i class="bi bi-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="card step-container" id="step-3">
                                <div class="card-header">
                                    <h5 class="form-section-title"><i class="bi bi-people-fill"></i> Parent / Guardian Information</h5>
                                </div>
                                <div class="card-body p-4">
                                    <p class="text-muted small fst-italic">Note: These fields are optional. Fill out only what is applicable.</p>

                                    <h6 class="fw-bold text-secondary mt-2">Mother's Information</h6>
                                    <div class="row gy-2 mb-3">
                                        <div class="col-md-4"><input type="text" name="mother_lastname" class="form-control" placeholder="Last Name"></div>
                                        <div class="col-md-4"><input type="text" name="mother_firstname" class="form-control" placeholder="First Name"></div>
                                        <div class="col-md-4"><input type="text" name="mother_contact" class="form-control" placeholder="Contact No."></div>
                                    </div>

                                    <h6 class="fw-bold text-secondary mt-4">Father's Information</h6>
                                    <div class="row gy-2 mb-3">
                                        <div class="col-md-4"><input type="text" name="father_lastname" class="form-control" placeholder="Last Name"></div>
                                        <div class="col-md-4"><input type="text" name="father_firstname" class="form-control" placeholder="First Name"></div>
                                        <div class="col-md-4"><input type="text" name="father_contact" class="form-control" placeholder="Contact No."></div>
                                    </div>

                                    <h6 class="fw-bold text-secondary mt-4">Guardian's Information</h6>
                                    <div class="row gy-2">
                                        <div class="col-md-4"><input type="text" name="guardian_lastname" class="form-control" placeholder="Last Name"></div>
                                        <div class="col-md-4"><input type="text" name="guardian_firstname" class="form-control" placeholder="First Name"></div>
                                        <div class="col-md-4"><input type="text" name="guardian_contact" class="form-control" placeholder="Contact No."></div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between p-3">
                                    <button type="button" class="btn btn-outline-secondary btn-nav" onclick="prevStep(2)"><i class="bi bi-arrow-left"></i> Back</button>
                                    <button type="button" class="btn btn-primary btn-nav" onclick="nextStep(4)">Next: Requirements <i class="bi bi-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="card step-container" id="step-4">
                                <div class="card-header">
                                    <h5 class="form-section-title"><i class="bi bi-file-earmark-text-fill"></i> Requirements & Submission</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle-fill"></i> Please upload clear copies of the required documents.
                                    </div>
                                    <div class="row gy-3">
                                        <?php
                                        $requirement = mysqli_query($conn, 'SELECT * FROM enrollment_requirement_tbl');
                                        while ($row_requirement = mysqli_fetch_assoc($requirement)) {
                                        ?>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold"> <?php echo $row_requirement['enrollment_requirement_name']; ?>   </label>
                                                <input type="hidden" name="requirement_names[]" value="<?php echo $row_requirement['enrollment_requirement_name']; ?>">
                                                <input type="file" name="requirements[]" class="form-control"  >
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between p-3">
                                    <button type="button" class="btn btn-outline-secondary btn-nav" onclick="prevStep(3)"><i class="bi bi-arrow-left"></i> Back</button>
                                    <button type="submit" name="submit" class="btn btn-success btn-nav text-white">Submit Application <i class="bi bi-check-circle-fill"></i></button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- WIZARD LOGIC ---
        let currentStep = 1;
        const totalSteps = 4;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
            // Show current step
            document.getElementById('step-' + step).classList.add('active');

            // Update Progress Bar
            document.querySelectorAll('.progress-step').forEach((el, index) => {
                const stepNum = index + 1;
                el.classList.remove('active', 'completed');
                if (stepNum < step) {
                    el.classList.add('completed');
                } else if (stepNum === step) {
                    el.classList.add('active');
                }
            });

            // Scroll to top of form
            document.getElementById('enrollment').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function nextStep(targetStep) {
            // Basic Validation (Check required fields in current step)
            const currentStepEl = document.getElementById('step-' + currentStep);
            const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value) {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (valid) {
                currentStep = targetStep;
                showStep(currentStep);
            } else {
                alert("Please fill out all required fields before proceeding.");
            }
        }

        function prevStep(targetStep) {
            currentStep = targetStep;
            showStep(currentStep);
        }

        // --- EXISTING LOGIC (Age, Address, Disability) ---

        // Age Calculation
        document.getElementById('birthdate').addEventListener('change', function() {
            const birthdate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthdate.getFullYear();
            const m = today.getMonth() - birthdate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
                age--;
            }
            document.getElementById('age').value = age >= 0 ? age : '';
        });

        // Disability Logic
        const yesRadio = document.getElementById("disabilityYes");
        const noRadio = document.getElementById("disabilityNo");
        const optionsDiv = document.getElementById("disability-options");

        yesRadio.addEventListener("change", function() {
            optionsDiv.style.display = "block";
        });
        noRadio.addEventListener("change", function() {
            optionsDiv.style.display = "none";
            // Clear values logic here...
        });

        // Address Copy Logic
        document.addEventListener("DOMContentLoaded", function() {
            const currentAddress = document.getElementById('current_address');
            const permanentAddress = document.getElementById('permanent_address');
            const sameAddressCheckbox = document.getElementById('sameAddress');

            sameAddressCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    permanentAddress.value = currentAddress.value;
                    permanentAddress.readOnly = true;
                } else {
                    permanentAddress.value = '';
                    permanentAddress.readOnly = false;
                }
            });

            currentAddress.addEventListener('input', function() {
                if (sameAddressCheckbox.checked) permanentAddress.value = currentAddress.value;
            });
        });
    </script>
</body>

</html>