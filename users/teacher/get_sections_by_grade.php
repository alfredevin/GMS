<?php
include '../../config.php';

if (isset($_GET['grade'])) {
    $grade = $_GET['grade'];

    $sql = "SELECT * FROM section_tbl WHERE section_grade = '$grade'";
    $result = mysqli_query($conn, $sql);

    echo '<option value="" selected disabled>SECTION</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['section_id'] . '">' . $row['section_name'] . '</option>';
    }
}
?>
