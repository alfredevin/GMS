<?php
include '../../config.php';

if (isset($_GET['grade'])) {
    $grade = $_GET['grade'];
    $result = mysqli_query($conn, "SELECT section_id, section_name FROM section_tbl WHERE section_grade = '$grade'");

    $sections = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sections[] = $row;
    }

    echo json_encode($sections);
}
?>
