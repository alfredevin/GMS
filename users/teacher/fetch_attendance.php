<?php
include '../../config.php';

$student_id = $_GET['student_id'];

// Query to get attendance
$query = "SELECT attendance_date, am_status, pm_status FROM attendance_tbl WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $query);

$events = [];

while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['attendance_date'];

    // Gawing Capitalized ang status (e.g., "present" -> "Present")
    $am_status_text = ucfirst($row['am_status']);
    $pm_status_text = ucfirst($row['pm_status']);

    // --- AM Event ---
    if (!empty($row['am_status'])) {
        $events[] = [
            'start' => $date,
            'display' => 'background',
            'className' => 'am-' . strtolower($row['am_status']), // css class
            // Ito ang data na gagamitin ng tooltip
            'extendedProps' => [
                'period' => 'AM',
                'status' => $am_status_text
            ]
        ];
    }

    // --- PM Event ---
    if (!empty($row['pm_status'])) {
        $events[] = [
            'start' => $date,
            'display' => 'background',
            'className' => 'pm-' . strtolower($row['pm_status']), // css class
            // Ito ang data na gagamitin ng tooltip
            'extendedProps' => [
                'period' => 'PM',
                'status' => $pm_status_text
            ]
        ];
    }
}

echo json_encode($events);
