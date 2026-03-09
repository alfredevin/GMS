<?php
session_start();
include '../../config.php';

// Kunin ang Teacher ID mula sa session
$teacher_id = $_SESSION['teacher_id'] ?? $_GET['teacher_id'] ?? '';

// --- GRADE COMPUTATION HELPER FUNCTIONS ---
function computeSpecificAverage($conn, $q, $type, $subject_id, $student_id)
{
    $query = "SELECT score, items FROM announcement_tbl 
              INNER JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
              WHERE quarterly = '$q' AND type = '$type' AND subject = '$subject_id' AND student_id = '$student_id'";
    $res = mysqli_query($conn, $query);
    $total = 0;
    $count = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['items'] > 0) {
            $total += ($row['score'] / $row['items']) * 100;
            $count++;
        }
    }
    return $count > 0 ? $total / $count : 0;
}

function getFinalSubjectGrade($conn, $student_id, $subject_id)
{
    $finalSum = 0;
    $gradeCount = 0;

    for ($q = 1; $q <= 4; $q++) {
        $quiz = computeSpecificAverage($conn, $q, 'quiz', $subject_id, $student_id);
        $pt = computeSpecificAverage($conn, $q, 'pt', $subject_id, $student_id);
        $exam = computeSpecificAverage($conn, $q, 'exam', $subject_id, $student_id);

        $finalGrade = ($quiz * 0.20) + ($pt * 0.60) + ($exam * 0.20);
        if ($finalGrade > 0) {
            $finalSum += $finalGrade;
            $gradeCount++;
        }
    }

    return ($gradeCount == 4) ? number_format($finalSum / 4, 2) : 'INC';
}

// Kunin ang Section at Grade Level ng Adviser
$adviser_query = mysqli_query($conn, "SELECT teacher_name, grade_level, section_id FROM teacher_tbl WHERE teacher_id = '$teacher_id' AND teacher_type = 'Class Adviser'");
$adviser_grade = '';
$adviser_section = '';
$adviser_name = '';
$subjects = [];

if (mysqli_num_rows($adviser_query) > 0) {
    $adv_row = mysqli_fetch_assoc($adviser_query);
    $adviser_grade = $adv_row['grade_level'];
    $adviser_section = $adv_row['section_id'];
    $adviser_name = strtoupper($adv_row['teacher_name']);

    // Kunin ang lahat ng Subjects para sa Grade Level
    $sub_res = mysqli_query($conn, "SELECT subject_id, subject_name FROM subject_tbl WHERE subject_grade = '$adviser_grade'");
    while ($s = mysqli_fetch_assoc($sub_res)) {
        $subjects[] = $s;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Grade Sheet - Grade <?= htmlspecialchars($adviser_grade) ?></title>
    <style>
        /* PRINT SPECIFIC STYLES */
        @page {
            size: 13in 8.5in;
            /* Legal Size Landscape */
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            /* Mas maliit na font para magkasya lahat ng subjects */
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-container h2,
        .header-container h3,
        .header-container h4 {
            margin: 3px 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .info-table td {
            padding: 2px 5px;
            border: none;
        }

        .info-data {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 0 10px;
        }

        /* MAIN GRADES TABLE */
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .grade-table th,
        .grade-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .grade-table th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
            /* Pinapanatili ang kulay kahit i-print */
            text-transform: uppercase;
            font-size: 10px;
        }

        .text-left {
            text-align: left !important;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .inc-grade {
            color: #c0392b;
            font-weight: bold;
        }

        /* FOOTER SIGNATURES */
        .signature-container {
            margin-top: 40px;
            width: 100%;
            display: table;
        }

        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
        }

        .sign-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 40px auto 5px auto;
            font-weight: bold;
            padding-top: 5px;
            text-transform: uppercase;
        }

        /* HIDE ELEMENTS NOT NEEDED FOR PRINTING */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header-container">
        <h4>Republic of the Philippines</h4>
        <h4>Department of Education</h4>
        <h2>BANGBANG NATIONAL HIGH SCHOOL</h2>
        <h3 style="margin-top: 10px; text-transform: uppercase; text-decoration: underline;">
            Master Grade Sheet
        </h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="10%">Grade Level:</td>
            <td width="20%"><span class="info-data"><?= htmlspecialchars($adviser_grade) ?></span></td>
            <td width="10%">Section:</td>
            <td width="30%">
                <span class="info-data">
                    <?php
                    // Kunin ang section name
                    $sec_name_query = mysqli_query($conn, "SELECT section_name FROM section_tbl WHERE section_id = '$adviser_section'");
                    $sec_name = mysqli_fetch_assoc($sec_name_query)['section_name'] ?? 'N/A';
                    echo htmlspecialchars($sec_name);
                    ?>
                </span>
            </td>
            <td width="10%">School Year:</td>
            <td width="20%"><span class="info-data">2025-2026</span></td>
        </tr>
    </table>

    <table class="grade-table">
        <thead>
            <tr>
                <th width="3%">NO.</th>
                <th class="text-left" width="20%">LEARNER'S NAME <br><small>(Last Name, First Name, M.I.)</small></th>

                <?php foreach ($subjects as $sub): ?>
                    <th><?= htmlspecialchars($sub['subject_name']) ?></th>
                <?php endforeach; ?>

                <th width="8%">GEN. AVERAGE</th>
                <th width="10%">REMARKS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Kunin lahat ng estudyante sa section
            $stud_query = "SELECT s.student_id, e.firstname, e.lastname, e.middlename 
                           FROM student_tbl s
                           INNER JOIN enrollment_tbl e ON s.enrollment_id = e.enrollmentId
                           WHERE s.section_id = '$adviser_section' AND s.student_grade = '$adviser_grade'
                           ORDER BY e.lastname ASC";
            $stud_res = mysqli_query($conn, $stud_query);

            $count = 1;
            if (mysqli_num_rows($stud_res) > 0) {
                while ($student = mysqli_fetch_assoc($stud_res)) {
                    $mi = !empty($student['middlename']) ? substr($student['middlename'], 0, 1) . '.' : '';
                    $full_name = strtoupper($student['lastname'] . ', ' . $student['firstname'] . ' ' . $mi);
                    $sid = $student['student_id'];

                    echo "<tr>";
                    echo "<td>$count</td>";
                    echo "<td class='text-left font-weight-bold'>$full_name</td>";

                    $totalGenAvg = 0;
                    $subjectCount = 0;
                    $hasInc = false;

                    // Loop through all subjects
                    foreach ($subjects as $sub) {
                        $sub_id = $sub['subject_id'];
                        $finalSubjectGrade = getFinalSubjectGrade($conn, $sid, $sub_id);

                        if ($finalSubjectGrade === 'INC') {
                            echo "<td class='inc-grade'>INC</td>";
                            $hasInc = true;
                        } else {
                            echo "<td>$finalSubjectGrade</td>";
                            $totalGenAvg += $finalSubjectGrade;
                            $subjectCount++;
                        }
                    }

                    // Calculate General Average
                    if ($hasInc || $subjectCount == 0) {
                        echo "<td class='inc-grade'>INC</td>";
                        echo "<td class='inc-grade'>INCOMPLETE</td>";
                    } else {
                        $genAvg = number_format($totalGenAvg / $subjectCount, 2);
                        $remarks = ($genAvg >= 90) ? 'PASSED W/ HONORS' : (($genAvg >= 75) ? 'PASSED' : 'FAILED');

                        echo "<td class='font-weight-bold'>$genAvg</td>";
                        echo "<td class='font-weight-bold'>$remarks</td>";
                    }

                    echo "</tr>";
                    $count++;
                }
            } else {
                $colspan = count($subjects) + 4;
                echo "<tr><td colspan='$colspan' style='padding: 20px;'>No learners enrolled in this section.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="signature-container">
        <div class="signature-box">
            <div class="sign-line"><?= htmlspecialchars($adviser_name) ?></div>
            <span>Class Adviser</span>
        </div>
        <div class="signature-box">
        </div>
        <div class="signature-box">
            <div class="sign-line">School Principal / Registrar</div>
            <span>Approved By</span>
        </div>
    </div>

</body>

</html>