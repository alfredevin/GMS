<?php
include '../../config.php';

$student_id = $_POST['student_id'] ?? '';
$new_grade = $_POST['grade'] ?? '';
$new_section_id = $_POST['section_id'] ?? '';

$response = ['success' => false];

if ($student_id && $new_grade && $new_section_id) {
    $sql = "UPDATE student_tbl SET student_grade = '$new_grade', section_id = '$new_section_id' WHERE student_id = '$student_id'";
    if (mysqli_query($conn, $sql)) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Database update failed.';
    }
} else {
    $response['message'] = 'Invalid data.';
}

echo json_encode($response);
?>
