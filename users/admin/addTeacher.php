<?php
include '../../config.php';


if (isset($_POST['submit_teacher'])) {
    $teacher_ids = $_POST['teacher_id'];
    $first_names = $_POST['first_name'];
    $middle_names = $_POST['middle_name'];
    $last_names = $_POST['last_name'];
    $specializations = $_POST['specialization'];
    $degrees = $_POST['degree'];

    $exts = $_POST['ext'] ?? [];
    $emails = $_POST['email'];
    $teacher_types = $_POST['teacher_type'];
    $grades = $_POST['grade_level'] ?? [];
    $sections = $_POST['section_id'] ?? [];

    $target_dir = "./teacher_profile/"; // make sure this folder exists and is writable

    foreach ($teacher_ids as $index => $teacher_id) {
        $specialization = strtoupper(trim($specializations[$index]));
        $degree = strtoupper(trim($degrees[$index]));

        $first_name = strtoupper(trim($first_names[$index]));
        $middle_name = strtoupper(trim($middle_names[$index]));
        $last_name = strtoupper(trim($last_names[$index]));
        $extenstion_name = isset($exts[$index]) ? strtoupper(trim($exts[$index])) : '';
        $email = trim($emails[$index]);

        $teacher_name = $first_name . ' ' . $middle_name . ' ' . $last_name . ' ' . $extenstion_name;
        $teacher_type = $teacher_types[$index];
        $grade = $grades[$index] ?? null;
        $section = $sections[$index] ?? null;
        // $password = $teacher_id;
        $password = 'Password123';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Handle profile image
        $profile_name = $_FILES['profile']['name'][$index];
        $profile_tmp = $_FILES['profile']['tmp_name'][$index];
        $ext = pathinfo($profile_name, PATHINFO_EXTENSION);
        $new_filename = uniqid("teacher_", true) . '.' . $ext;
        $target_file = $target_dir . $new_filename;

        if (!move_uploaded_file($profile_tmp, $target_file)) {
            continue; // skip this teacher if upload fails
        }

        // Check duplicates
        $check_query = "SELECT * FROM teacher_tbl WHERE teacher_id = '$teacher_id' OR teacher_name = '$teacher_name'";
        $check_result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            continue;
        }

        // If adviser, check if another adviser exists
        if ($teacher_type === 'Class Adviser') {
            $check_adviser = "SELECT * FROM teacher_tbl 
                              WHERE teacher_type = 'Class Adviser' 
                              AND grade_level = '$grade' 
                              AND section_id = '$section'";
            $adviser_result = mysqli_query($conn, $check_adviser);
            if (mysqli_num_rows($adviser_result) > 0) {
                continue;
            }
        }

        // Insert
        $insert_query = "INSERT INTO teacher_tbl 
                        (teacher_id, teacher_name, teacher_type, grade_level, section_id, password, profile, first_name, middle_name, last_name, ext, email, specialization,degree)
                        VALUES (
                            '$teacher_id', '$teacher_name', '$teacher_type',
                            " . ($grade ? "'$grade'" : "NULL") . ",
                            " . ($section ? "'$section'" : "NULL") . ",
                            '$hashed_password', '$new_filename',
                            '$first_name','$middle_name','$last_name',
                            " . ($extenstion_name ? "'$extenstion_name'" : "NULL") . ",
                            '$email', '$specialization', '$degree'
                        )";
        mysqli_query($conn, $insert_query);
    }

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
                title: "Teachers processed successfully!"
            });
        });
    </script>';
}




?>
<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <div class="container-fluid  ">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Add Teacher -
                                <a href="#" id="addMore" class="btn btn-primary btn-sm ">Add Multiple</a>
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" autocomplete="off" id="teacherForm" onsubmit="return validateTeacherIDs()" enctype="multipart/form-data">
                                <div id="teacherFields">
                                    <div class="row teacher-entry  ">
                                        <div class="col-md-2 mb-3">
                                            <label>Teacher ID <span class="text-danger">*</span></label>
                                            <input type="text" name="teacher_id[]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name[]" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Middle Name <span class="text-danger">*</span></label>
                                            <input type="text" name="middle_name[]" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name[]" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                                        </div>
                                        <div class="col-md-1  mb-3">
                                            <label>Ext <span class="text-danger">*</span></label>
                                            <input type="text" name="ext[]" class="form-control" oninput="this.value = this.value.toUpperCase();">
                                        </div>
                                        <div class="col-md-3  mb-3">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input type="text" name="email[]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Profile <span class="text-danger">*</span></label>
                                            <input type="file" name="profile[]" class="form-control" required accept="image/*">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Teacher Type <span class="text-danger">*</span></label>
                                            <select name="teacher_type[]" class="form-control teacher-type" required>
                                                <option value="" selected disabled>Select Type</option>
                                                <option value="Class Adviser">Class Adviser</option>
                                                <option value="Subject Teacher">Subject Teacher</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Degree <span class="text-danger">*</span></label>
                                            <input type="text" name="degree[]" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Specialization <span class="text-danger">*</span></label>
                                            <input type="text" name="specialization[]" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                                        </div>

                                        <div class="col-md-1 mb-3  ">
                                            <label class="text-white">Remove </label>
                                            <button type="button" class="btn btn-danger     remove-btn" style="display: none;" onclick="removeTeacherRow(this)">
                                                Remove
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-3 grade-container" style="display:none;">
                                            <label>Grade Level <span class="text-danger">*</span></label>
                                            <select name="grade_level[]" class="form-control grade-level">
                                                <option value="" selected disabled>Select Grade</option>
                                                <?php for ($i = 7; $i <= 10; $i++) {
                                                    echo "<option value='$i'>Grade $i</option>";
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3 section-container" style="display:none;">
                                            <label>Section <span class="text-danger">*</span></label>
                                            <select name="section_id[]" class="form-control section-id">
                                                <option value="" selected disabled>Select Section</option>
                                            </select>
                                        </div>


                                    </div>

                                    <hr class="bg-danger">
                                </div>
                                <div>
                                    <a href="teacher" class="btn btn-primary">Back to Page</a>
                                    <button type="submit" name="submit_teacher" class="btn btn-success">Add Teacher(s)</button>
                                </div>
                            </form>

                        </div>
                    </div>


                </div>
            </div>
            <?php include './../template/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <?php include './../template/script.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const teacherFields = document.getElementById('teacherFields');
            const addMoreBtn = document.getElementById('addMore');

            addMoreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const firstEntry = document.querySelector('.teacher-entry');
                const clone = firstEntry.cloneNode(true);

                // Clear inputs in clone
                clone.querySelectorAll('input, select').forEach(input => input.value = '');

                // Show and set up remove button
                const removeBtn = clone.querySelector('.remove-btn');
                if (removeBtn) {
                    removeBtn.style.display = 'block';
                    removeBtn.setAttribute('onclick', 'removeTeacherRow(this)');
                }

                // Hide optional fields
                clone.querySelector('.grade-container').style.display = 'none';
                clone.querySelector('.section-container').style.display = 'none';

                teacherFields.appendChild(clone);
            });



            // Event delegation for dynamic select change
            teacherFields.addEventListener('change', function(e) {
                if (e.target.classList.contains('teacher-type')) {
                    const entry = e.target.closest('.teacher-entry');
                    const gradeContainer = entry.querySelector('.grade-container');
                    const sectionContainer = entry.querySelector('.section-container');
                    const sectionSelect = entry.querySelector('.section-id');

                    if (e.target.value === 'Class Adviser') {
                        gradeContainer.style.display = 'block';
                        sectionContainer.style.display = 'block';
                    } else {
                        gradeContainer.style.display = 'none';
                        sectionContainer.style.display = 'none';
                        sectionSelect.innerHTML = '<option value="" selected disabled>Select Section</option>';
                    }
                }

                if (e.target.classList.contains('grade-level')) {
                    const entry = e.target.closest('.teacher-entry');
                    const grade = e.target.value;
                    const sectionSelect = entry.querySelector('.section-id');

                    fetch(`get_sections.php?grade=${grade}`)
                        .then(res => res.json())
                        .then(data => {
                            sectionSelect.innerHTML = '<option value="" selected disabled>Select Section</option>';
                            data.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.section_id;
                                option.text = section.section_name;
                                sectionSelect.appendChild(option);
                            });
                        });
                }
            });
        });
    </script>

    <script>
        function removeTeacherRow(button) {
            const allRows = document.querySelectorAll('.teacher-entry');
            if (allRows.length > 1) {
                button.closest('.teacher-entry').remove();
            } else {
                alert("At least one teacher entry is required.");
            }
        }


        document.addEventListener('input', function(e) {
            if (e.target.name === 'teacher_id[]') {
                const allIds = document.querySelectorAll('input[name="teacher_id[]"]');
                const enteredIds = [];

                allIds.forEach((input, index) => {
                    const value = input.value.trim();
                    input.classList.remove('is-invalid'); // Reset any previous error style
                    input.setCustomValidity(''); // Reset validity

                    if (value !== '') {
                        if (enteredIds.includes(value)) {
                            input.classList.add('is-invalid');
                            input.setCustomValidity('Duplicate Teacher ID within the form.');
                        } else {
                            enteredIds.push(value);
                        }
                    }
                });
            }
        });


        document.addEventListener('input', function(e) {
            if (e.target.name === 'teacher_id[]') {
                const input = e.target;
                const value = input.value.trim();

                // Prevent unnecessary requests
                if (value === '') return;

                // AJAX check against database
                fetch('check_teacher_id.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'teacher_id=' + encodeURIComponent(value)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            input.classList.add('is-invalid');
                            input.setCustomValidity('Teacher ID already exists in database.');
                        } else {
                            // Remove error if it's no longer duplicated
                            input.classList.remove('is-invalid');
                            input.setCustomValidity('');
                        }
                    });
            }
        });
    </script>
    <style>
        .is-invalid {
            border-color: #dc3545;
        }
    </style>

    <script>
        document.addEventListener('input', function(e) {
            if (e.target.name === 'teacher_id[]') {
                checkDuplicateTeacherIDs();
            }
        });

        function checkDuplicateTeacherIDs() {
            const inputs = document.querySelectorAll('input[name="teacher_id[]"]');
            const values = {};
            let hasDuplicate = false;

            // Clear all previous error states
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                const existingFeedback = input.parentElement.querySelector('.duplicate-feedback');
                if (existingFeedback) existingFeedback.remove();
            });

            // Track input values and duplicates
            inputs.forEach(input => {
                const val = input.value.trim();
                if (val !== '') {
                    if (!values[val]) {
                        values[val] = [];
                    }
                    values[val].push(input);
                }
            });

            // Apply error state to all duplicate inputs
            for (let val in values) {
                if (values[val].length > 1) {
                    hasDuplicate = true;
                    values[val].forEach(input => {
                        input.classList.add('is-invalid');

                        const feedback = document.createElement('div');
                        feedback.className = 'duplicate-feedback text-danger small';
                        feedback.textContent = 'Duplicate Teacher ID';
                        input.parentElement.appendChild(feedback);
                    });
                }
            }

            return !hasDuplicate;
        }

        function validateTeacherIDs() {
            return checkDuplicateTeacherIDs();
        }
    </script>
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .duplicate-feedback {
            margin-top: 4px;
            font-size: 0.875em;
        }
    </style>



</body>

</html>