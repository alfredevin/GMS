<?php
include '../../config.php';

if(isset($_POST['teacher_id'])){
    // 1. Kunin ang mga pinasa mula sa AJAX
    $teacher_id = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $teacher_name = strtoupper(mysqli_real_escape_string($conn, $_POST['teacher_name']));
    $teacher_type = mysqli_real_escape_string($conn, $_POST['teacher_type']);
    $email = mysqli_real_escape_string($conn, $_POST['teacher_email']);
    $position = strtoupper(mysqli_real_escape_string($conn, $_POST['teacher_position'])); // KUKUNIN ANG POSITION DITO
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Handle Grade and Section (NULL kung Subject Teacher)
    $grade_level = ($_POST['grade_level'] == '') ? 'NULL' : "'" . mysqli_real_escape_string($conn, $_POST['grade_level']) . "'";
    $section_id = ($_POST['section_id'] == '') ? 'NULL' : "'" . mysqli_real_escape_string($conn, $_POST['section_id']) . "'";

    // 2. Adviser Duplicate Check
    if ($teacher_type === 'Class Adviser') {
        $check_adviser = "SELECT * FROM teacher_tbl 
                          WHERE teacher_type = 'Class Adviser' 
                          AND grade_level = $grade_level 
                          AND section_id = $section_id 
                          AND teacher_id != '$teacher_id'";
        $res = mysqli_query($conn, $check_adviser);
        if(mysqli_num_rows($res) > 0){
            echo "duplicate";
            exit;
        }
    }

    // 3. I-update ang database (kasama ang position)
    $query = "UPDATE teacher_tbl SET 
                teacher_name = '$teacher_name',
                teacher_type = '$teacher_type',
                email = '$email',
                position = '$position', 
                grade_level = $grade_level,
                section_id = $section_id,
                teacher_status = '$status'
              WHERE teacher_id = '$teacher_id'";

    if(mysqli_query($conn, $query)){
        echo "success";
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid Request";
}
?>