<?php
include '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $year = date('Y');
    // Mark student as graduated (example: set status = 2 or is_graduated = 1)
    $sql = "UPDATE student_tbl SET status = 2,year_graduate = '$year' WHERE student_id = '$student_id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
