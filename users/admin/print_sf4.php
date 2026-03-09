<?php
include '../../config.php';

// Kukunin ang pinasa galing sa SweetAlert
$filter_month = isset($_GET['month']) ? $_GET['month'] : date('F');
$filter_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Convert string month to number (e.g. 'January' -> '01')
$month_num = date('m', strtotime($filter_month));

// Calculate Total School Days in that month (Weekdays only)
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month_num, $filter_year);
$school_days_count = 0;
for ($d = 1; $d <= $days_in_month; $d++) {
    $time = mktime(0, 0, 0, $month_num, $d, $filter_year);
    if (date('N', $time) < 6) { // 1=Mon, 5=Fri
        $school_days_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Form 4 (SF4) - <?= $filter_month ?> <?= $filter_year ?></title>
    <style>
        /* PRINT STYLES - LEGAL LANDSCAPE */
        @page {
            size: 13in 8.5in;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 2px;
        }

        .header-subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .info-table td {
            padding: 2px;
        }

        .info-box {
            border: 1px solid #000;
            padding: 2px 5px;
            font-weight: bold;
            display: inline-block;
            min-width: 150px;
            text-align: center;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .report-table th {
            background-color: #e0e0e0 !important;
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
            padding-left: 5px !important;
        }

        .signature-container {
            margin-top: 30px;
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            width: 300px;
            text-align: center;
        }

        .sign-line {
            border-bottom: 1px solid #000;
            height: 20px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header-title">School Form 4 (SF4) Monthly Learner's Movement and Attendance</div>
    <div class="header-subtitle"><i>(This replaces Form 3 & STS Form 4-Absenteeism and Dropout Profile)</i></div>

    <table class="info-table">
        <tr>
            <td width="8%">School ID</td>
            <td width="15%">
                <div class="info-box">301531</div>
            </td>
            <td width="8%">Region</td>
            <td width="15%">
                <div class="info-box">IV-B MIMAROPA</div>
            </td>
            <td width="8%">Division</td>
            <td width="15%">
                <div class="info-box">Marinduque</div>
            </td>
            <td width="8%">District</td>
            <td width="23%">
                <div class="info-box">Mogpog</div>
            </td>
        </tr>
        <tr>
            <td>School Name</td>
            <td colspan="3">
                <div class="info-box" style="width: 95%; text-align:left;">Bangbang National High School</div>
            </td>
            <td>School Year</td>
            <td>
                <div class="info-box">2025-2026</div>
            </td>
            <td>Report Month</td>
            <td>
                <div class="info-box">
                    <?= strtoupper(htmlspecialchars($filter_month) . ' ' . htmlspecialchars($filter_year)) ?></div>
            </td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" width="8%">GRADE/<br>YEAR LEVEL</th>
                <th rowspan="2" width="10%">SECTION</th>
                <th rowspan="2" width="18%">NAME OF ADVISER</th>
                <th colspan="3">REGISTERED LEARNERS<br>(As of End of the Month)</th>
                <th colspan="3">ATTENDANCE</th>
                <th colspan="3">DROPPED OUT</th>
                <th colspan="3">TRANSFERRED OUT</th>
                <th colspan="3">TRANSFERRED IN</th>
            </tr>
            <tr>
                <th width="3%">M</th>
                <th width="3%">F</th>
                <th width="4%">Total</th>
                <th width="5%">Daily<br>Average</th>
                <th width="5%">% for the<br>Month</th>
                <th width="5%">ADA</th>
                <th width="3%">M</th>
                <th width="3%">F</th>
                <th width="4%">Total</th>
                <th width="3%">M</th>
                <th width="3%">F</th>
                <th width="4%">Total</th>
                <th width="3%">M</th>
                <th width="3%">F</th>
                <th width="4%">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // MAIN QUERY: Kunin lahat ng sections at teachers
            $sql = "SELECT 
                        sec.section_id,
                        sec.section_grade, 
                        sec.section_name, 
                        t.teacher_name,
                        SUM(CASE WHEN e.sex = 'MALE' AND st.status = 1 THEN 1 ELSE 0 END) as male_count,
                        SUM(CASE WHEN e.sex = 'FEMALE' AND st.status = 1 THEN 1 ELSE 0 END) as female_count
                    FROM section_tbl sec
                    LEFT JOIN teacher_tbl t ON sec.section_id = t.section_id AND t.teacher_type = 'Class Adviser'
                    LEFT JOIN student_tbl st ON sec.section_id = st.section_id 
                    LEFT JOIN enrollment_tbl e ON st.enrollment_id = e.enrollmentId
                    GROUP BY sec.section_id
                    ORDER BY sec.section_grade ASC, sec.section_name ASC";

            $result = mysqli_query($conn, $sql);

            // Grand Totals variables
            $grand_male = 0;
            $grand_female = 0;
            $grand_total_ada = 0;
            $sections_with_data = 0;
            $g_drop_m = 0;
            $g_drop_f = 0;
            $g_trans_m = 0;
            $g_trans_f = 0;

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $sec_id = $row['section_id'];
                    $grade = $row['section_grade'];
                    $section = $row['section_name'];
                    $adviser = !empty($row['teacher_name']) ? strtoupper($row['teacher_name']) : 'NO ADVISER ASSIGNED';

                    $m_count = $row['male_count'] ?? 0;
                    $f_count = $row['female_count'] ?? 0;
                    $total_count = $m_count + $f_count;

                    $grand_male += $m_count;
                    $grand_female += $f_count;

                    // ==========================================
                    // 1. ATTENDANCE COMPUTATION
                    // ==========================================
                    $total_present = 0;

                    if ($school_days_count > 0 && $total_count > 0) {
                        $att_sql = "SELECT am_status, pm_status FROM attendance_tbl 
                                    INNER JOIN student_tbl ON attendance_tbl.student_id = student_tbl.student_id
                                    WHERE student_tbl.section_id = '$sec_id' 
                                    AND MONTH(attendance_date) = '$month_num' AND YEAR(attendance_date) = '$filter_year'
                                    AND WEEKDAY(attendance_date) < 5";

                        $att_res = mysqli_query($conn, $att_sql);
                        $total_absences = 0;
                        while ($att = mysqli_fetch_assoc($att_res)) {
                            if ($att['am_status'] == 'Absent' && $att['pm_status'] == 'Absent') {
                                $total_absences += 1;
                            } elseif ($att['am_status'] == 'Absent' || $att['pm_status'] == 'Absent') {
                                $total_absences += 0; // Half day logic (Adjust if needed)
                            }
                        }

                        $perfect_attendance_days = $total_count * $school_days_count;
                        $total_present = $perfect_attendance_days - $total_absences;
                        $ada = round($total_present / $school_days_count, 2);
                        $percentage = round(($ada / $total_count) * 100, 2);

                        $grand_total_ada += $ada;
                        $sections_with_data++;
                    } else {
                        $ada = 0;
                        $percentage = 0;
                    }

                    // ==========================================
                    // 2. DROPPED OUT COMPUTATION
                    // ==========================================
                    $drop_q = "SELECT 
                                SUM(CASE WHEN e.sex = 'MALE' THEN 1 ELSE 0 END) as drop_m,
                                SUM(CASE WHEN e.sex = 'FEMALE' THEN 1 ELSE 0 END) as drop_f
                               FROM student_dropout_tbl d
                               INNER JOIN student_tbl s ON d.student_id = s.student_id
                               INNER JOIN enrollment_tbl e ON s.enrollment_id = e.enrollmentId
                               WHERE s.section_id = '$sec_id' 
                               AND MONTH(d.dropout_date) = '$month_num' AND YEAR(d.dropout_date) = '$filter_year'";
                    $drop_res = mysqli_fetch_assoc(mysqli_query($conn, $drop_q));
                    $drop_m = $drop_res['drop_m'] ?? 0;
                    $drop_f = $drop_res['drop_f'] ?? 0;
                    $drop_t = $drop_m + $drop_f;

                    $g_drop_m += $drop_m;
                    $g_drop_f += $drop_f;

                    // ==========================================
                    // 3. TRANSFERRED OUT COMPUTATION
                    // ==========================================
                    $trans_q = "SELECT 
                                SUM(CASE WHEN e.sex = 'MALE' THEN 1 ELSE 0 END) as trans_m,
                                SUM(CASE WHEN e.sex = 'FEMALE' THEN 1 ELSE 0 END) as trans_f
                               FROM student_transferee_tbl tr
                               INNER JOIN student_tbl s ON tr.student_id = s.student_id
                               INNER JOIN enrollment_tbl e ON s.enrollment_id = e.enrollmentId
                               WHERE s.section_id = '$sec_id' 
                               AND MONTH(tr.transfer_date) = '$month_num' AND YEAR(tr.transfer_date) = '$filter_year'";
                    $trans_res = mysqli_fetch_assoc(mysqli_query($conn, $trans_q));
                    $trans_m = $trans_res['trans_m'] ?? 0;
                    $trans_f = $trans_res['trans_f'] ?? 0;
                    $trans_t = $trans_m + $trans_f;

                    $g_trans_m += $trans_m;
                    $g_trans_f += $trans_f;

                    // ==========================================
                    // DISPLAY ROW
                    // ==========================================
                    echo "<tr>";
                    echo "<td>Grade $grade</td>";
                    echo "<td>$section</td>";
                    echo "<td class='text-left'>$adviser</td>";

                    // Registered
                    echo "<td>$m_count</td><td>$f_count</td><td style='font-weight:bold;'>$total_count</td>";

                    // Attendance 
                    echo "<td>" . ($total_present > 0 ? round($total_present / $school_days_count, 2) : 0) . "</td>";
                    echo "<td>" . ($percentage > 0 ? $percentage . "%" : "0%") . "</td>";
                    echo "<td style='font-weight:bold;'>" . ($ada > 0 ? $ada : 0) . "</td>";

                    // Dropped Out
                    echo "<td>$drop_m</td><td>$drop_f</td><td style='font-weight:bold;'>$drop_t</td>";

                    // Transferred Out
                    echo "<td>$trans_m</td><td>$trans_f</td><td style='font-weight:bold;'>$trans_t</td>";

                    // Transferred In (Assuming 0 for now unless you have a table for transferees IN)
                    echo "<td>0</td><td>0</td><td style='font-weight:bold;'>0</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='18' style='padding:20px;'>No data available. Please create sections and enroll students.</td></tr>";
            }
            ?>
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td colspan="3" style="text-align: right; padding-right: 10px;">TOTAL</td>
                <td><?= $grand_male ?></td>
                <td><?= $grand_female ?></td>
                <td><?= $grand_male + $grand_female ?></td>

                <?php
                $overall_ada = $sections_with_data > 0 ? round($grand_total_ada, 2) : 0;
                $overall_percentage = ($grand_male + $grand_female) > 0 ? round(($overall_ada / ($grand_male + $grand_female)) * 100, 2) : 0;
                ?>
                <td><?= $overall_ada ?></td>
                <td><?= $overall_percentage ?>%</td>
                <td><?= $overall_ada ?></td>

                <td><?= $g_drop_m ?></td>
                <td><?= $g_drop_f ?></td>
                <td><?= $g_drop_m + $g_drop_f ?></td>

                <td><?= $g_trans_m ?></td>
                <td><?= $g_trans_f ?></td>
                <td><?= $g_trans_m + $g_trans_f ?></td>

                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-container">
        <div class="signature-box">
            <div>Prepared and Submitted by:</div>
            <div class="sign-line"></div>
            <div style="font-weight:bold; text-transform:uppercase;">School Head / Principal</div>
            <div style="font-size: 9px;">(Signature over Printed Name)</div>
        </div>
    </div>

</body>

</html>