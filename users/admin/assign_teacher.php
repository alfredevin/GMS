<?php
include '../../config.php';

if (isset($_POST['subject_id']) && isset($_POST['teacher_id'])) {
    $subjectId = $_POST['subject_id'];
    $teacherId = $_POST['teacher_id'];

    $update = mysqli_query($conn, "UPDATE subject_tbl SET teacher_assign = '$teacherId' WHERE subject_id = '$subjectId'");

    echo $update ? 'success' : 'error';
}
?>
