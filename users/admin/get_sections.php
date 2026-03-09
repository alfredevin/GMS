<?php
include '../../config.php'; // adjust path as needed

if (isset($_GET['grade'])) {
    $grade = $_GET['grade'];

    $query = "SELECT section_id, section_name FROM section_tbl WHERE section_grade = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $grade);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $sections = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sections[] = $row;
    }

    echo json_encode($sections);
}
?>
