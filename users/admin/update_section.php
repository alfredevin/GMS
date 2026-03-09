<?php
include '../../config.php';

if (isset($_POST['section_id'], $_POST['section_name'], $_POST['section_grade'])) {
    $section_id = mysqli_real_escape_string($conn, $_POST['section_id']);
    $section_name = mysqli_real_escape_string($conn, $_POST['section_name']);
    $section_grade = mysqli_real_escape_string($conn, $_POST['section_grade']);

    $checkQuery = "SELECT * FROM section_tbl 
                   WHERE  section_name = '$section_name' 
                   AND section_grade = '$section_grade' 
                   AND section_id  != '$section_id '";

    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "duplicate";
    } else {
        // Proceed with update
        $query = "UPDATE section_tbl 
                  SET section_name = '$section_name' 
                  WHERE section_id = '$section_id'";

        if (mysqli_query($conn, $query)) {
            echo "success";
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    }
} else {
    echo "Missing required fields";
}
