<?php
include '../../config.php';

if (isset($_POST['subject_id'], $_POST['subject_code'], $_POST['subject_name'], $_POST['subject_grade'])) {
    $subject_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $subject_grade = mysqli_real_escape_string($conn, $_POST['subject_grade']);

    // Check for duplicate subject name in the same grade (excluding current subject_id)
    $checkQuery = "SELECT * FROM subject_tbl 
                   WHERE subject_code = '$subject_code' AND subject_name = '$subject_name' 
                   AND subject_grade = '$subject_grade' 
                   AND subject_id != '$subject_id'";

    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "duplicate";
    } else {
        // Proceed with update
        $query = "UPDATE subject_tbl 
                  SET subject_code = '$subject_code', subject_name = '$subject_name' 
                  WHERE subject_id = '$subject_id'";

        if (mysqli_query($conn, $query)) {
            echo "success";
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    }
} else {
    echo "Missing required fields";
}
