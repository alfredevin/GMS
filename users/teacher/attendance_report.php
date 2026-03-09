<?php
include '../../config.php';

$section_id = $_GET['section_id'] ?? '';
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// --- 1. DATA FETCHING ---

// Get Section Info
$sec_sql = mysqli_query($conn, "SELECT * FROM section_tbl WHERE section_id='$section_id'");
$sec_row = mysqli_fetch_assoc($sec_sql);
$section_name = $sec_row['section_name'] ?? '';
$grade_level = $sec_row['grade_level'] ?? '';

// Get Calendar Days (1 to 31)
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$dates = [];
$school_days_count = 0;

for ($d = 1; $d <= $days_in_month; $d++) {
    $time = mktime(0, 0, 0, $month, $d, $year);
    $day_char = substr(date('D', $time), 0, 1); // M, T, W...
    $is_weekend = (date('N', $time) >= 6);

    if (!$is_weekend) $school_days_count++; // Count only weekdays for stats

    $dates[$d] = [
        'full' => date('Y-m-d', $time),
        'day_num' => $d,
        'day_char' => $day_char,
        'is_weekend' => $is_weekend
    ];
}

// Fetch Attendance Data
$attendance_data = [];
$att_sql = "SELECT attendance_tbl.student_id, attendance_date, am_status, pm_status 
            FROM attendance_tbl 
            INNER JOIN student_tbl ON attendance_tbl.student_id = student_tbl.student_id
            WHERE student_tbl.section_id = '$section_id' 
            AND MONTH(attendance_date) = '$month' AND YEAR(attendance_date) = '$year'";
$att_res = mysqli_query($conn, $att_sql);
while ($row = mysqli_fetch_assoc($att_res)) {
    $attendance_data[$row['student_id']][$row['attendance_date']] = [
        'am' => $row['am_status'],
        'pm' => $row['pm_status']
    ];
}

// Fetch Students Function
function getStudents($conn, $sec_id, $gender)
{
    return mysqli_query($conn, "SELECT * FROM student_tbl 
        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id 
        WHERE section_id = '$sec_id' AND sex = '$gender' ORDER BY lastname ASC");
}

// Initialize Daily Total Counters
$daily_male_present = array_fill(1, $days_in_month, 0);
$daily_female_present = array_fill(1, $days_in_month, 0);

// Total Registered (For Summary)
$m_total = mysqli_num_rows(getStudents($conn, $section_id, 'Male'));
$f_total = mysqli_num_rows(getStudents($conn, $section_id, 'Female'));
?>

<!DOCTYPE html>
<html>

<head>
    <title>School Form 2 (SF2)</title>
    <style>
        /* @page {
            size: 8.5in 13in;
            margin: 5mm;
            orientation: landscape;
        }

        @media print {
            @page {
                size: 13in 8.5in;
            }
        } */

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* GENERAL TABLE */
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            white-space: nowrap;
        }

        /* HEADER */
        .header-table td {
            border: none;
            text-align: left;
            vertical-align: middle;
        }

        .deped-header {
            text-align: center;
            text-transform: uppercase;
            font-family: "Times New Roman", serif;
        }

        .deped-header h2,
        .deped-header h4 {
            margin: 0;
        }

        .input-box {
            border: 1px solid black;
            height: 16px;
            display: inline-block;
            vertical-align: middle;
            padding: 0 5px;
            font-weight: bold;
        }

        /* GRID */
        .name-col {
            width: 15%;
            text-align: left;
            padding-left: 5px;
            text-transform: uppercase;
            font-size: 9px;
        }

        .date-col {
            width: 2%;
            font-size: 8px;
            line-height: 9px;
        }

        .total-col {
            width: 2.5%;
            font-size: 9px;
            font-weight: bold;
        }

        .remarks-col {
            width: 10%;
            font-size: 9px;
        }

        .weekend-bg {
            background-color: #dcdcdc;
        }

        .divider-bg {
            background-color: #f0f0f0;
            text-align: left;
            padding-left: 10px;
            font-weight: bold;
        }

        /* THE ATTENDANCE CELL (DIAGONAL LINE) */
        .att-cell {
            padding: 0 !important;
            height: 16px;
            position: relative;
            /* Thin diagonal line from bottom-left to top-right */
            background: linear-gradient(to top right, transparent calc(50% - 0.5px), #000 50%, transparent calc(50% + 0.5px));
        }

        /* ATTENDANCE MARKERS */
        .absent-whole {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            color: black;
            z-index: 2;
            background: white;
            /* Cover the line */
        }

        /* TARDY / HALF DAY SHADING */
        .shade-upper {
            /* Absent AM */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top right, transparent 50%, #888 50%);
            z-index: 1;
        }

        .shade-lower {
            /* Absent PM */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top right, #888 50%, transparent 50%);
            z-index: 1;
        }

        /* FOOTER */
        .footer-table td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: top;
        }

        .guidelines {
            font-size: 8px;
            text-align: justify;
            padding-right: 10px;
        }

        .summary-table th,
        .summary-table td {
            font-size: 9px;
            padding: 2px;
        }

        .sig-line {
            border-bottom: 1px solid black;
            margin-top: 25px;
            width: 90%;
        }
    </style>
</head>

<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td width="10%" align="center"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Department_of_Education.svg/1200px-Department_of_Education.svg.png" width="55"></td>
            <td width="65%" align="center">
                <div style="font-size: 16px; font-weight: bold;">School Form 2 (SF2) Daily Attendance Report of Learners</div>
                <div style="font-size: 9px; font-style: italic;">(This replaced Form 1, Form 2 & STS Form 4 - Absenteeism and Dropout Profile)</div>
            </td>
            <td width="25%" align="right">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Department_of_Education_%28DepEd%29.svg/2560px-Department_of_Education_%28DepEd%29.svg.png" width="55">
            </td>
        </tr>
    </table>

    <table class="header-table" style="font-size: 11px; margin-bottom: 5px;">
        <tr>
            <td width="10%">School ID</td>
            <td width="15%">
                <div class="input-box" style="width: 100%;">301531</div>
            </td>
            <td width="10%" align="right">School Year</td>
            <td width="15%">
                <div class="input-box" style="width: 100%;"><?= $year ?>-<?= $year + 1 ?></div>
            </td>
            <td width="15%" align="right">Report for the Month of</td>
            <td width="20%">
                <div class="input-box" style="width: 100%;"><?= date('F', mktime(0, 0, 0, $month, 10)) ?></div>
            </td>
        </tr>
        <tr>
            <td>Name of School</td>
            <td colspan="3">
                <div class="input-box" style="width: 98%; text-align: left;">BANGBANG NATIONAL HIGH SCHOOL</div>
            </td>
            <td align="right">Grade Level</td>
            <td>
                <div class="input-box" style="width: 30%;"><?= $grade_level ?></div>
                <span style="margin-left: 5px;">Section</span>
                <div class="input-box" style="width: 35%;"><?= $section_name ?></div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr style="height: 30px;">
                <th rowspan="2" class="name-col">LEARNER'S NAME<br><span style="font-size: 8px; font-weight: normal;">(Last Name, First Name, Middle Name)</span></th>
                <th colspan="<?= count($dates) ?>" style="font-size: 8px;">(1st row for date, 2nd row for Day: M,T,W,TH,F)</th>
                <th colspan="2" style="width: 5%;">Total for the Month</th>
                <th rowspan="2" class="remarks-col">REMARKS (If DROPPED OUT, state reason, please refer to legend number 2. If TRANSFERRED IN/OUT, write the name of School.)</th>
            </tr>
            <tr>
                <?php foreach ($dates as $d): ?>
                    <th class="date-col <?= $d['is_weekend'] ? 'weekend-bg' : '' ?>"><?= $d['day_num'] ?><br><?= $d['day_char'] ?></th>
                <?php endforeach; ?>
                <th class="total-col">ABSENT</th>
                <th class="total-col">TARDY</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td colspan="<?= count($dates) + 4 ?>" class="divider-bg">MALE | TOTAL Per Day</td>
            </tr>

            <?php
            $males = getStudents($conn, $section_id, 'Male');
            $male_total_absent = 0; // Accumulator for Summary

            while ($stud = mysqli_fetch_assoc($males)):
                $absent_count = 0;
                $tardy_count = 0;
            ?>
                <tr>
                    <td class="name-col"><?= strtoupper($stud['lastname'] . ', ' . $stud['firstname'] . ' ' . substr($stud['middlename'], 0, 1) . '.') ?></td>

                    <?php foreach ($dates as $d_num => $d_info):
                        // Logic for Attendance
                        $rec = $attendance_data[$stud['student_id']][$d_info['full']] ?? null;
                        $am = $rec['am'] ?? 'Present';
                        $pm = $rec['pm'] ?? 'Present';

                        $content = "";
                        $is_present_today = 0;

                        if (!$d_info['is_weekend']) {
                            if ($am == 'Absent' && $pm == 'Absent') {
                                $content = "<div class='absent-whole'>X</div>"; // Whole day absent
                                $absent_count++;
                            } elseif ($am == 'Absent') {
                                $content = "<div class='shade-upper'></div>"; // AM Absent (Upper)
                                $tardy_count++;
                                $is_present_today = 1;
                            } elseif ($pm == 'Absent') {
                                $content = "<div class='shade-lower'></div>"; // PM Absent (Lower)
                                $tardy_count++;
                                $is_present_today = 1;
                            } else {
                                $is_present_today = 1; // Present whole day (Blank)
                            }

                            if ($is_present_today) $daily_male_present[$d_num]++;
                        }
                    ?>
                        <td class="att-cell <?= $d_info['is_weekend'] ? 'weekend-bg' : '' ?>"><?= $content ?></td>
                    <?php endforeach;
                    $male_total_absent += $absent_count; ?>

                    <td><?= $absent_count > 0 ? $absent_count : '' ?></td>
                    <td><?= $tardy_count > 0 ? $tardy_count : '' ?></td>
                    <td></td>
                </tr>
            <?php endwhile; ?>

            <tr style="background-color: #fff;">
                <td class="name-col" style="text-align: center; font-weight: bold;">MALE | TOTAL Per Day</td>
                <?php foreach ($dates as $d_num => $d_info): ?>
                    <td style="font-weight: bold; font-size: 9px;" class="<?= $d_info['is_weekend'] ? 'weekend-bg' : '' ?>">
                        <?= $d_info['is_weekend'] ? '' : $daily_male_present[$d_num] ?>
                    </td>
                <?php endforeach; ?>
                <td colspan="3" class="weekend-bg"></td>
            </tr>

            <tr>
                <td colspan="<?= count($dates) + 4 ?>" class="divider-bg">FEMALE | TOTAL Per Day</td>
            </tr>

            <?php
            $females = getStudents($conn, $section_id, 'Female');
            $female_total_absent = 0;

            while ($stud = mysqli_fetch_assoc($females)):
                $absent_count = 0;
                $tardy_count = 0;
            ?>
                <tr>
                    <td class="name-col"><?= strtoupper($stud['lastname'] . ', ' . $stud['firstname'] . ' ' . substr($stud['middlename'], 0, 1) . '.') ?></td>

                    <?php foreach ($dates as $d_num => $d_info):
                        $rec = $attendance_data[$stud['student_id']][$d_info['full']] ?? null;
                        $am = $rec['am'] ?? 'Present';
                        $pm = $rec['pm'] ?? 'Present';
                        $content = "";
                        $is_present_today = 0;

                        if (!$d_info['is_weekend']) {
                            if ($am == 'Absent' && $pm == 'Absent') {
                                $content = "<div class='absent-whole'>X</div>";
                                $absent_count++;
                            } elseif ($am == 'Absent') {
                                $content = "<div class='shade-upper'></div>";
                                $tardy_count++;
                                $is_present_today = 1;
                            } elseif ($pm == 'Absent') {
                                $content = "<div class='shade-lower'></div>";
                                $tardy_count++;
                                $is_present_today = 1;
                            } else {
                                $is_present_today = 1;
                            }
                            if ($is_present_today) $daily_female_present[$d_num]++;
                        }
                    ?>
                        <td class="att-cell <?= $d_info['is_weekend'] ? 'weekend-bg' : '' ?>"><?= $content ?></td>
                    <?php endforeach;
                    $female_total_absent += $absent_count; ?>

                    <td><?= $absent_count > 0 ? $absent_count : '' ?></td>
                    <td><?= $tardy_count > 0 ? $tardy_count : '' ?></td>
                    <td></td>
                </tr>
            <?php endwhile; ?>

            <tr style="background-color: #fff;">
                <td class="name-col" style="text-align: center; font-weight: bold;">FEMALE | TOTAL Per Day</td>
                <?php foreach ($dates as $d_num => $d_info): ?>
                    <td style="font-weight: bold; font-size: 9px;" class="<?= $d_info['is_weekend'] ? 'weekend-bg' : '' ?>">
                        <?= $d_info['is_weekend'] ? '' : $daily_female_present[$d_num] ?>
                    </td>
                <?php endforeach; ?>
                <td colspan="3" class="weekend-bg"></td>
            </tr>

            <tr>
                <td class="name-col" style="text-align: center; font-weight: bold;">Combined TOTAL Per Day</td>
                <?php foreach ($dates as $d_num => $d_info): ?>
                    <td style="font-weight: bold; font-size: 9px;" class="<?= $d_info['is_weekend'] ? 'weekend-bg' : '' ?>">
                        <?= $d_info['is_weekend'] ? '' : ($daily_male_present[$d_num] + $daily_female_present[$d_num]) ?>
                    </td>
                <?php endforeach; ?>
                <td colspan="3" class="weekend-bg"></td>
            </tr>

        </tbody>
    </table>

    <?php
    // Basic Calculations for Summary
    $grand_total = $m_total + $f_total;

    // Average Daily Attendance = Sum of Daily Totals / School Days
    $sum_m_attendance = array_sum($daily_male_present);
    $sum_f_attendance = array_sum($daily_female_present);

    $avg_m = ($school_days_count > 0) ? round($sum_m_attendance / $school_days_count, 2) : 0;
    $avg_f = ($school_days_count > 0) ? round($sum_f_attendance / $school_days_count, 2) : 0;
    $avg_total = $avg_m + $avg_f;

    // Percentage
    $perc_m = ($m_total > 0) ? round(($avg_m / $m_total) * 100, 2) : 0;
    $perc_f = ($f_total > 0) ? round(($avg_f / $f_total) * 100, 2) : 0;
    $perc_total = ($grand_total > 0) ? round(($avg_total / $grand_total) * 100, 2) : 0;
    ?>

    <table class="footer-table" style="margin-top: 5px; border: none;">
        <tr>
            <td width="30%" style="border: none;">
                <div style="font-weight: bold; font-size: 9px;">GUIDELINES:</div>
                <div class="guidelines">
                    1. The attendance shall be accomplished daily. Refer to the codes for checking learners' attendance.<br>
                    2. Dates shall be written in the preceding columns beside Learner's Name.<br>
                    3. To compute the following:
                    <div style="margin-left: 10px;">
                        a. Percentage of Enrolment = <span style="border-bottom: 1px solid black;">Registered Learner as of End of the Month</span> x 100 <br>
                        <span style="margin-left: 110px;">Enrolment as of 1st Friday of June</span>
                        <br>
                        b. Average Daily Attendance = <span style="border-bottom: 1px solid black;">Total Daily Attendance</span> <br>
                        <span style="margin-left: 110px;">Number of School Days in reporting month</span>
                        <br>
                        c. Percentage of Attendance for the month = <span style="border-bottom: 1px solid black;">Average daily attendance</span> x 100 <br>
                        <span style="margin-left: 125px;">Registered Learner as of End of the month</span>
                    </div>
                    <br>
                    4. Every End of the month, the class adviser will submit this form to the office of the principal for recording of summary table into the School Form 4. Once signed by the principal, this form should be returned to the adviser.<br>
                    5. The adviser will extend necessary intervention including but not limited to home visitation to learner/s that committed 5 consecutive days of absences or those with potentials of dropping out.<br>
                    6. Attendance performance of learner is expected to reflect in Form 137 and Form 138 every grading period.<br>
                    * Beginning of School Year cut-off report is every 1st Friday of School Calendar Days
                </div>
            </td>

            <td width="15%" style="border: 1px solid black; font-size: 8px;">
                <strong>1. CODES FOR CHECKING ATTENDANCE</strong><br><br>
                (blank)- Present; (x)- Absent; Tardy (half shaded= Upper for Late Comer, Lower for Cutting Classes)<br><br>
                <strong>2. REASONS/CAUSES OF DROP-OUTS</strong><br>
                <strong>a. Domestic-Related Factors</strong><br>
                a.1. Had to take care of siblings<br>
                a.2. Early marriage/pregnancy<br>
                a.3. Parents' attitude toward schooling<br>
                a.4. Family problems<br>
                <strong>b. Individual-Related Factors</strong><br>
                b.1. Illness<br>
                b.2. Overage<br>
                b.3. Death<br>
                b.4. Drug Abuse<br>
                b.5. Poor academic performance<br>
                b.6. Lack of interest/Distractions<br>
                b.7. Hunger/Malnutrition<br>
                <strong>c. School-Related Factors</strong><br>
                c.1. Teacher Factor<br>
                c.2. Physical condition of classroom<br>
                c.3. Peer Influence<br>
                <strong>d. Geographic/Environmental</strong><br>
                d.1. Distance between home and school<br>
                d.2. Armed conflict (incl. Tribal wars & clanfeuds)<br>
                d.3. Calamities/Disasters<br>
                <strong>e. Financial-Related</strong><br>
                e.1. Child labor, work<br>
                <strong>f. Others</strong>
            </td>

            <td width="25%" style="padding: 0; border: none;">
                <table class="summary-table" style="border-collapse: collapse; width: 100%;">
                    <tr>
                        <th style="text-align: left; font-size: 8px;">Month: <br> <span style="font-size: 10px;"><?= date('F', mktime(0, 0, 0, $month, 10)) ?></span></th>
                        <th style="text-align: center; font-size: 8px;">No. of Days of Classes:<br> <span style="font-size: 10px;"><?= $school_days_count ?></span></th>
                        <th colspan="3" style="text-align: center; font-size: 8px;">Summary for the Month</th>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                        <th>M</th>
                        <th>F</th>
                        <th>TOTAL</th>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">* Enrolment as of (1st Friday of June)</td>
                        <td><?= $m_total ?></td>
                        <td><?= $f_total ?></td>
                        <td><?= $grand_total ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Late Enrolment during the month (beyond cut-off)</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Registered Learner as of end of the month</td>
                        <td><?= $m_total ?></td>
                        <td><?= $f_total ?></td>
                        <td><?= $grand_total ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Percentage of Enrolment as of end of the month</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Average Daily Attendance</td>
                        <td><?= $avg_m ?></td>
                        <td><?= $avg_f ?></td>
                        <td><?= $avg_total ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Percentage of Attendance for the month</td>
                        <td><?= $perc_m ?>%</td>
                        <td><?= $perc_f ?>%</td>
                        <td><?= $perc_total ?>%</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Number of students with 5 consecutive days of absences:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Drop out</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Transferred out</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 8px;">Transferred in</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <div style="font-size: 8px; margin-top: 5px;">I certify that this is a true and correct report.</div>
                <div class="sig-line"></div>
                <div style="font-size: 8px; text-align: center;">(Signature of Teacher over Printed Name)</div>

                <div style="font-size: 8px; margin-top: 5px;">Attested by:</div>
                <div class="sig-line"></div>
                <div style="font-size: 8px; text-align: center;">(Signature of School Head over Printed Name)</div>
            </td>
        </tr>
    </table>

    <div style="font-size: 8px; margin-top: 5px;">School Form 2: Page 1 of 1</div>

</body>

</html>