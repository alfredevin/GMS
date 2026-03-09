<?php
include '../../config.php';

if (isset($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];

    $stmt = $conn->prepare("SELECT * FROM teacher_tbl WHERE teacher_id = ?");
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo json_encode(['exists' => $result->num_rows > 0]);
}
?>
