<?php
include '../../config.php';

if (
    isset($_POST['teacher_id'], $_POST['teacher_name'], $_POST['teacher_type']) &&
    !empty($_POST['teacher_id']) && !empty($_POST['teacher_name']) && !empty($_POST['teacher_type']) && !empty($_POST['teacher_email'])
) {
    $teacher_id = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $teacher_name = mysqli_real_escape_string($conn, $_POST['teacher_name']);
    $teacher_type = mysqli_real_escape_string($conn, $_POST['teacher_type']);
    $email = mysqli_real_escape_string($conn, $_POST['teacher_email']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);  
    $grade_level = $_POST['grade_level'] ?? null;
    $section_id = $_POST['section_id'] ?? null;

    // If Class Adviser, check if grade + section already has another adviser
    if ($teacher_type === 'Class Adviser') {
        $check_duplicate = "SELECT * FROM teacher_tbl 
            WHERE teacher_type = 'Class Adviser' 
            AND grade_level = '$grade_level' 
            AND section_id = '$section_id' 
            AND teacher_id != '$teacher_id'";

        $result = mysqli_query($conn, $check_duplicate);
        if (mysqli_num_rows($result) > 0) {
            echo "duplicate"; // Adviser already assigned
            exit;
        }

        // Validate section exists
        $check_section = mysqli_query($conn, "SELECT * FROM section_tbl WHERE section_id = '$section_id'");
        if (mysqli_num_rows($check_section) === 0) {
            echo "invalid_section"; // Invalid section ID
            exit;
        }
    } else {
        // For Subject Teacher, remove grade/section
        $grade_level = null;
        $section_id = null;
    }

    $update = "UPDATE teacher_tbl SET 
                teacher_name = '$teacher_name',
                email = '$email',
                teacher_type = '$teacher_type',
                grade_level = " . ($grade_level ? "'$grade_level'" : "NULL") . ",
                section_id = " . ($section_id ? "'$section_id'" : "NULL") . ",
                    teacher_status = '$status'
               WHERE teacher_id = '$teacher_id'";

    if (mysqli_query($conn, $update)) {
        echo "success";
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
} else {
    echo "Missing fields";
}
