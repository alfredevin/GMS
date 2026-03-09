<?php
include '../../config.php';

if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
    $type = $_POST['type']; // 'am' or 'pm'
    $status = $_POST['status'];
    $date = $_POST['date']; // Ito ang bagong variable mula sa JS

    // Check kung may record na ang student sa specific date na yun
    $check = mysqli_query($conn, "SELECT * FROM attendance_tbl WHERE student_id='$student_id' AND attendance_date='$date'");

    if (mysqli_num_rows($check) > 0) {
        // UPDATE kung meron na
        if ($type == 'am') {
            $sql = "UPDATE attendance_tbl SET am_status='$status' WHERE student_id='$student_id' AND attendance_date='$date'";
        } else {
            $sql = "UPDATE attendance_tbl SET pm_status='$status' WHERE student_id='$student_id' AND attendance_date='$date'";
        }
    } else {
        // INSERT kung wala pa
        if ($type == 'am') {
            $sql = "INSERT INTO attendance_tbl (student_id, am_status, pm_status, attendance_date) VALUES ('$student_id', '$status', '', '$date')";
        } else {
            $sql = "INSERT INTO attendance_tbl (student_id, am_status, pm_status, attendance_date) VALUES ('$student_id', '', '$status', '$date')";
        }
    }

    if (mysqli_query($conn, $sql)) {
        echo "Saved";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
