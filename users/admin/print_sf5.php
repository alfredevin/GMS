<?php
include '../../config.php';

$filter_grade = $_GET['grade'] ?? '';
$filter_section = $_GET['section'] ?? '';

// 1. Get Section Name Details
$sql_sec = "SELECT * FROM section_tbl WHERE section_id = '$filter_section'";
$result_sec = mysqli_query($conn, $sql_sec);
$row_sec = mysqli_fetch_array($result_sec);
$section_name = $row_sec["section_name"] ?? 'Unknown';

// --- DATA PROCESSING (SAME LOGIC AS BEFORE) ---
$male_students = [];
$female_students = [];

// Initialize Summary Counters
$summary_proficiency = [
    '90-100' => ['M' => 0, 'F' => 0],
    '85-89'  => ['M' => 0, 'F' => 0],
    '80-84'  => ['M' => 0, 'F' => 0],
    '75-79'  => ['M' => 0, 'F' => 0],
    'Below 75' => ['M' => 0, 'F' => 0]
];

$summary_outcome = [
    'PROMOTED' => ['M' => 0, 'F' => 0],
    'CONDITIONAL' => ['M' => 0, 'F' => 0],
    'RETAINED' => ['M' => 0, 'F' => 0]
];

// Helper Functions
function computeAverage($res)
{
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

function getScore($conn, $q, $type, $sub_id, $stud_id)
{
    $sql = "SELECT * FROM announcement_tbl 
            LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
            WHERE quarterly = '$q' AND type = '$type' AND subject = '$sub_id' AND student_id = '$stud_id'";
    return computeAverage(mysqli_query($conn, $sql));
}

// 2. Fetch Students
if (!empty($filter_grade) && !empty($filter_section)) {
    $sql = "SELECT * FROM student_tbl
            INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
            WHERE student_tbl.status = 1 
            AND student_tbl.student_grade = '$filter_grade' 
            AND student_tbl.section_id = '$filter_section'
            ORDER BY sex DESC, lastname ASC";

    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $stud_id = $row['student_id'];
        $sex = strtoupper($row['sex']);
        $gender_key = ($sex == 'MALE') ? 'M' : 'F';

        // --- CALCULATE GRADES ---
        $subjectQuery = "SELECT * FROM subject_tbl WHERE subject_grade = '$filter_grade'";
        $subjectResult = mysqli_query($conn, $subjectQuery);

        $sum_final_grades = 0;
        $subject_count = 0;
        $failed_subjects = [];

        while ($sub = mysqli_fetch_assoc($subjectResult)) {
            $sub_id = $sub['subject_id'];
            $sub_name = $sub['subject_name'];

            $subj_final_sum = 0;
            $quarter_count = 0;

            for ($q = 1; $q <= 4; $q++) {
                $quiz = getScore($conn, $q, 'quiz', $sub_id, $stud_id);
                $pt = getScore($conn, $q, 'pt', $sub_id, $stud_id);
                $exam = getScore($conn, $q, 'exam', $sub_id, $stud_id);

                $q_grade = ($quiz * 0.20) + ($pt * 0.60) + ($exam * 0.20);
                if ($q_grade > 0) {
                    $subj_final_sum += $q_grade;
                    $quarter_count++;
                }
            }

            if ($quarter_count == 4) {
                $final_subj_grade = round($subj_final_sum / 4);
                $sum_final_grades += $final_subj_grade;
                $subject_count++;

                if ($final_subj_grade < 75) {
                    $failed_subjects[] = $sub_name;
                }
            }
        }

        // --- GENERAL AVERAGE ---
        $gen_avg = ($subject_count > 0) ? round($sum_final_grades / $subject_count) : 0;

        $action_taken = "INCOMPLETE";
        if ($subject_count > 0) {
            if ($gen_avg >= 75 && count($failed_subjects) == 0) {
                $action_taken = "PROMOTED";
            } elseif ($gen_avg >= 75 && count($failed_subjects) <= 2) {
                $action_taken = "CONDITIONAL";
            } else {
                $action_taken = "RETAINED";
            }
        }

        // --- SUMMARY COUNTERS ---
        if ($action_taken != "INCOMPLETE") {
            $summary_outcome[$action_taken][$gender_key]++;

            if ($gen_avg >= 90) $summary_proficiency['90-100'][$gender_key]++;
            elseif ($gen_avg >= 85) $summary_proficiency['85-89'][$gender_key]++;
            elseif ($gen_avg >= 80) $summary_proficiency['80-84'][$gender_key]++;
            elseif ($gen_avg >= 75) $summary_proficiency['75-79'][$gender_key]++;
            else $summary_proficiency['Below 75'][$gender_key]++;
        }

        // --- PREPARE DATA ---
        $student_data = [
            'lrn' => $row['lrn'],
            'name' => strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . substr($row['middlename'], 0, 1) . '.'),
            'gen_avg' => ($gen_avg > 0) ? $gen_avg : '',
            'action' => $action_taken,
            'failed' => implode(', ', $failed_subjects)
        ];

        if ($sex == 'MALE') {
            $male_students[] = $student_data;
        } else {
            $female_students[] = $student_data;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>School Form 5 (SF5)</title>
    <style>
        /* LEGAL LANDSCAPE SETUP */
        @page {
            size: 13in 8.5in;
            /* Legal Landscape */
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        /* HEADER */
        .sf5-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .sf5-header-title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .sf5-header-sub {
            text-align: center;
            font-size: 9px;
        }

        .info-grid {
            width: 100%;
            font-size: 11px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 2px;
        }

        .border-bottom {
            border-bottom: 1px solid black;
            text-align: center;
            font-weight: bold;
        }

        /* FLEXBOX LAYOUT (COL-8 | COL-4) */
        .content-container {
            display: flex;
            width: 100%;
            gap: 15px;
            /* Space between columns */
        }

        .left-column {
            width: 70%;
            /* Roughly Col-8 */
        }

        .right-column {
            width: 30%;
            /* Roughly Col-4 */
        }

        /* MAIN TABLE */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid black;
            padding: 3px;
            font-size: 10px;
        }

        .main-table th {
            background-color: #dcdcdc;
            text-align: center;
            vertical-align: middle;
            text-transform: uppercase;
            font-size: 9px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
            padding-right: 5px;
        }

        .bg-divider {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
            text-transform: uppercase;
        }

        /* SUMMARY TABLES */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 15px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
        }

        .summary-header {
            background-color: #dcdcdc;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* SIGNATORIES */
        .signatures {
            margin-top: 20px;
            width: 100%;
            text-align: center;
            page-break-inside: avoid;
        }

        .sig-line {
            border-bottom: 1px solid black;
            display: inline-block;
            width: 80%;
            margin-top: 25px;
            margin-bottom: 2px;
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print()">

    <table class="sf5-header-table">
        <tr>
            <td width="80%">
                <div class="sf5-header-title">School Form 5 (SF5) Report on Promotion & Level of Proficiency</div>
                <div class="sf5-header-sub">(This replaces Forms 18-E1, 18-E2, 18A and List of Graduates)</div>
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td width="5%">Region</td>
            <td width="10%" class="border-bottom">IV-B</td>
            <td width="5%">Division</td>
            <td width="15%" class="border-bottom">MARINDUQUE</td>
            <td width="5%">District</td>
            <td width="15%" class="border-bottom">GASAN</td>
            <td width="5%">School ID</td>
            <td width="10%" class="border-bottom">301531</td>
            <td width="5%">School Year</td>
            <td width="10%" class="border-bottom"><?php echo date('Y'); ?>-<?php echo date('Y') + 1; ?></td>
        </tr>
        <tr>
            <td>School Name</td>
            <td colspan="3" class="border-bottom">BANGBANG NATIONAL HIGH SCHOOL</td>
            <td>Grade Level</td>
            <td class="border-bottom"><?= $filter_grade ?></td>
            <td>Section</td>
            <td colspan="3" class="border-bottom"><?= $section_name ?></td>
        </tr>
    </table>

    <hr>

    <div class="content-container">

        <div class="left-column">
            <table class="main-table">
                <thead>
                    <tr>
                        <th width="15%">LRN</th>
                        <th width="35%">LEARNER'S NAME<br>(Last Name, First Name, Middle Name)</th>
                        <th width="10%">GENERAL<br>AVERAGE<br>(Whole Number)</th>
                        <th width="15%">ACTION TAKEN:<br>PROMOTED, CONDITIONAL,<br>or RETAINED</th>
                        <th width="25%">DID NOT MEET EXPECTATIONS OF THE<br>FOLLOWING LEARNING AREA/S</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td colspan="5" class="bg-divider">MALE</td>
                    </tr>
                    <?php foreach ($male_students as $s): ?>
                        <tr>
                            <td class="text-center"><?= $s['lrn'] ?></td>
                            <td><?= $s['name'] ?></td>
                            <td class="text-center"><?= $s['gen_avg'] ?></td>
                            <td class="text-center"><?= $s['action'] ?></td>
                            <td class="text-center"><?= $s['failed'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="text-right"><strong>TOTAL MALE</strong></td>
                        <td class="text-center"><strong><?= count($male_students) ?></strong></td>
                        <td colspan="2"></td>
                    </tr>

                    <tr>
                        <td colspan="5" class="bg-divider">FEMALE</td>
                    </tr>
                    <?php foreach ($female_students as $s): ?>
                        <tr>
                            <td class="text-center"><?= $s['lrn'] ?></td>
                            <td><?= $s['name'] ?></td>
                            <td class="text-center"><?= $s['gen_avg'] ?></td>
                            <td class="text-center"><?= $s['action'] ?></td>
                            <td class="text-center"><?= $s['failed'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="text-right"><strong>TOTAL FEMALE</strong></td>
                        <td class="text-center"><strong><?= count($female_students) ?></strong></td>
                        <td colspan="2"></td>
                    </tr>

                    <tr>
                        <td colspan="2" class="text-right"><strong>COMBINED</strong></td>
                        <td class="text-center"><strong><?= count($male_students) + count($female_students) ?></strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="right-column">

            <table class="summary-table">
                <thead>
                    <tr>
                        <th colspan="4" class="summary-header">SUMMARY OF LEVEL OF PROFICIENCY</th>
                    </tr>
                    <tr>
                        <th width="40%">LEVEL</th>
                        <th width="20%">MALE</th>
                        <th width="20%">FEMALE</th>
                        <th width="20%">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $labels = [
                        '90-100' => 'Outstanding (90-100)',
                        '85-89' => 'Very Satisfactory (85-89)',
                        '80-84' => 'Satisfactory (80-84)',
                        '75-79' => 'Fairly Satisfactory (75-79)',
                        'Below 75' => 'Did Not Meet Expectations (Below 75)'
                    ];
                    $order = ['90-100', '85-89', '80-84', '75-79', 'Below 75'];
                    foreach ($order as $key) {
                        $m = $summary_proficiency[$key]['M'];
                        $f = $summary_proficiency[$key]['F'];
                        echo "<tr>
                                <td style='text-align:left;'>{$labels[$key]}</td>
                                <td>$m</td><td>$f</td><td>" . ($m + $f) . "</td>
                              </tr>";
                    }
                    ?>
                    <tr style="font-weight:bold;">
                        <td style="text-align:left;">TOTAL</td>
                        <td><?= count($male_students) ?></td>
                        <td><?= count($female_students) ?></td>
                        <td><?= count($male_students) + count($female_students) ?></td>
                    </tr>
                </tbody>
            </table>

            <table class="summary-table">
                <thead>
                    <tr>
                        <th colspan="4" class="summary-header">SUMMARY OF PROMOTION</th>
                    </tr>
                    <tr>
                        <th width="40%">STATUS</th>
                        <th width="20%">MALE</th>
                        <th width="20%">FEMALE</th>
                        <th width="20%">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $statuses = ['PROMOTED', 'CONDITIONAL', 'RETAINED'];
                    foreach ($statuses as $stat) {
                        $m = $summary_outcome[$stat]['M'];
                        $f = $summary_outcome[$stat]['F'];
                        $dispStat = ($stat == 'CONDITIONAL') ? 'CONDITIONALLY PROMOTED' : $stat;
                        echo "<tr>
                                <td style='text-align:left;'>$dispStat</td>
                                <td>$m</td><td>$f</td><td>" . ($m + $f) . "</td>
                              </tr>";
                    }
                    ?>
                    <tr style="font-weight:bold;">
                        <td style="text-align:left;">TOTAL</td>
                        <td><?= count($male_students) ?></td>
                        <td><?= count($female_students) ?></td>
                        <td><?= count($male_students) + count($female_students) ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="signatures-vertical">

                <div class="sig-section">
                    <div class="sig-label">PREPARED BY:</div>
                    <div class="sig-box">
                        <div class="sig-name">REGGIE PEREGRINA LINASA</div>
                        <div class="sig-role">Class Adviser</div>
                    </div>
                </div>

                <div class="sig-section">
                    <div class="sig-label">CERTIFIED CORRECT & SUBMITTED BY:</div>
                    <div class="sig-box">
                        <div class="sig-name">NORMINDA SOL MABAO</div>
                        <div class="sig-role">Secondary School Principal/SCC Chair</div>
                    </div>
                </div>

                <div class="sig-section">
                    <div class="sig-label">REVIEWED BY: SCC Members</div>

                    <div class="sig-box">
                        <div class="sig-name">MARITES SIGUE SENA</div>
                        <div class="sig-role">Member</div>
                    </div>

                    <div class="sig-box">
                        <div class="sig-name">FIDEL GARCIA EMBING</div>
                        <div class="sig-role">Member</div>
                    </div>

                    <div class="sig-box">
                        <div class="sig-name">ROSALIE ORILLA BARBERO</div>
                        <div class="sig-role">Member</div>
                    </div>

                    <div class="sig-box">
                        <div class="sig-name">JANICE DELA SANTA DU</div>
                        <div class="sig-role">Generated thru LIS (SCC CO-Chair)</div>
                    </div>
                </div>

            </div>
            <style>
                /* SIGNATORIES - VERTICAL STACK STYLE */
                .signatures-vertical {
                    margin-top: 40px;
                    width: 100%;
                    page-break-inside: avoid;
                    font-family: Arial, sans-serif;
                }

                .sig-section {
                    margin-bottom: 20px;
                    /* Space between groups (Prepared, Certified, Reviewed) */
                    width: 100%;
                }

                .sig-label {
                    font-size: 10px;
                    font-weight: bold;
                    text-transform: uppercase;
                    margin-bottom: 15px;
                    /* Space between Label and Name */
                    text-align: left;
                    padding-left: 20px;
                    /* Indent konti para pantay sa image */
                }

                .sig-box {
                    width: 60%;
                    /* Hindi sagad sa gilid, parang nasa picture */
                    margin: 0 auto 15px auto;
                    /* Center the name block */
                    text-align: center;
                }

                .sig-name {
                    border-bottom: 1px solid black;
                    font-weight: bold;
                    font-size: 11px;
                    text-transform: uppercase;
                    padding-bottom: 2px;
                }

                .sig-role {
                    font-size: 10px;
                    font-style: italic;
                    /* Naka-italic sa image */
                    margin-top: 2px;
                }
            </style>
        </div>

    </div>


</body>

</html>