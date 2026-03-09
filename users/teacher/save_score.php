<?php
include '../../config.php';


$student_id = $_POST['student_id'];
$score = $_POST['score'];
$announcement_id = $_POST['announcement_id'];

// Check if there's already a score
$check = mysqli_query($conn, "SELECT * FROM student_scores_tbl 
                              WHERE student_id = '$student_id' AND announcement_id = '$announcement_id'");

if (mysqli_num_rows($check) > 0) {
    // Update existing score
    $update = "UPDATE student_scores_tbl SET score = '$score' 
               WHERE student_id = '$student_id' AND announcement_id = '$announcement_id'";
    mysqli_query($conn, $update);
} else {
    // Insert new score
    $insert = "INSERT INTO student_scores_tbl (student_id, announcement_id, score) 
               VALUES ('$student_id', '$announcement_id', '$score')";
    mysqli_query($conn, $insert);
}
