<?php
include '../../config.php';
$teachers = [];

$result = mysqli_query($conn, "SELECT teacher_id , teacher_name FROM teacher_tbl");
while ($row = mysqli_fetch_assoc($result)) {
    $teachers[] = [
        'id' => $row['teacher_id'],
        'name' => $row['teacher_name']
    ];
}
echo json_encode($teachers);
?>
