<?php
include '../../config.php';

// --- GRADE COMPUTATION HELPER FUNCTIONS ---
function computeSpecificAverage($conn, $q, $type, $subject_id, $student_id) {
    $query = "SELECT score, items FROM announcement_tbl 
              INNER JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
              WHERE quarterly = '$q' AND type = '$type' AND subject = '$subject_id' AND student_id = '$student_id'";
    $res = mysqli_query($conn, $query);
    $total = 0; $count = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['items'] > 0) {
            $total += ($row['score'] / $row['items']) * 100;
            $count++;
        }
    }
    return $count > 0 ? $total / $count : 0;
}

function getFinalSubjectGrade($conn, $student_id, $subject_id) {
    $finalSum = 0; $gradeCount = 0;
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

$school_year = date('Y') . '-' . (date('Y') + 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Form 6 (SF6)</title>
    <style>
        @page { size: 13in 8.5in; margin: 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; background-color: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .header-title { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 2px; }
        .header-subtitle { text-align: center; font-size: 11px; margin-bottom: 15px; }
        .info-table { width: 100%; margin-bottom: 10px; font-size: 11px; }
        .info-table td { padding: 2px; }
        .info-box { border: 1px solid #000; padding: 2px 5px; font-weight: bold; display: inline-block; min-width: 150px; text-align: center; }
        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .report-table th, .report-table td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; }
        .report-table th { background-color: #e0e0e0 !important; font-weight: bold; font-size: 9px; }
        .text-left { text-align: left !important; padding-left: 5px !important; }
        .signature-container { margin-top: 30px; width: 100%; display: table; }
        .signature-box { display: table-cell; width: 50%; text-align: center; }
        .sign-line { border-bottom: 1px solid #000; width: 60%; margin: 0 auto 5px auto; height: 20px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header-title">School Form 6 (SF6) Summarized Register of Learner Status</div>
    <div class="header-subtitle"><i>(End of School Year Report)</i></div>

    <table class="info-table">
        <tr>
            <td width="8%">School ID</td>
            <td width="15%"><div class="info-box">301531</div></td>
            <td width="8%">Region</td>
            <td width="15%"><div class="info-box">IV-B MIMAROPA</div></td>
            <td width="8%">Division</td>
            <td width="15%"><div class="info-box">Marinduque</div></td>
            <td width="8%">District</td>
            <td width="23%"><div class="info-box">Mogpog</div></td>
        </tr>
        <tr>
            <td>School Name</td>
            <td colspan="5"><div class="info-box" style="width: 97%; text-align:left;">Bangbang National High School</div></td>
            <td>School Year</td>
            <td><div class="info-box"><?= $school_year ?></div></td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="3" width="6%">GRADE LEVEL</th>
                <th rowspan="3" width="8%">SECTION</th>
                <th rowspan="3" width="15%">CLASS ADVISER</th>
                <th colspan="9">SUMMARY OF LEARNER STATUS</th>
                <th colspan="15">SUMMARY OF LEVEL OF PROGRESS AND ACHIEVEMENT</th>
            </tr>
            <tr>
                <th colspan="3">PROMOTED</th>
                <th colspan="3">CONDITIONAL</th>
                <th colspan="3">RETAINED</th>
                <th colspan="3">OUTSTANDING<br>(90-100)</th>
                <th colspan="3">VERY SATISFACTORY<br>(85-89)</th>
                <th colspan="3">SATISFACTORY<br>(80-84)</th>
                <th colspan="3">FAIRLY SATISFACTORY<br>(75-79)</th>
                <th colspan="3">DID NOT MEET EXP.<br>(Below 75)</th>
            </tr>
            <tr>
                <th>M</th><th>F</th><th>T</th>
                <th>M</th><th>F</th><th>T</th>
                <th>M</th><th>F</th><th>T</th>
                <th>M</th><th>F</th><th>T</th>
                <th>M</th><th>F</th><th>T</th>
                <th>M</th><th>F</th><th>T</th>
                <th>M</th><th>F</th><th>T</th>
                <th>M</th><th>F</th><th>T</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Grand Totals Arrays
            $gt_status = ['prom_m'=>0, 'prom_f'=>0, 'cond_m'=>0, 'cond_f'=>0, 'ret_m'=>0, 'ret_f'=>0];
            $gt_achieve = ['out_m'=>0, 'out_f'=>0, 'vs_m'=>0, 'vs_f'=>0, 's_m'=>0, 's_f'=>0, 'fs_m'=>0, 'fs_f'=>0, 'fail_m'=>0, 'fail_f'=>0];

            $sec_query = "SELECT sec.section_id, sec.section_grade, sec.section_name, t.teacher_name 
                          FROM section_tbl sec 
                          LEFT JOIN teacher_tbl t ON sec.section_id = t.section_id AND t.teacher_type = 'Class Adviser'
                          ORDER BY sec.section_grade ASC, sec.section_name ASC";
            $sec_res = mysqli_query($conn, $sec_query);

            if(mysqli_num_rows($sec_res) > 0) {
                while($sec = mysqli_fetch_assoc($sec_res)) {
                    $section_id = $sec['section_id'];
                    $grade = $sec['section_grade'];
                    $section_name = $sec['section_name'];
                    $adviser = !empty($sec['teacher_name']) ? strtoupper($sec['teacher_name']) : 'NO ADVISER';

                    // Variables for this specific section
                    $sec_status = ['prom_m'=>0, 'prom_f'=>0, 'cond_m'=>0, 'cond_f'=>0, 'ret_m'=>0, 'ret_f'=>0];
                    $sec_achieve = ['out_m'=>0, 'out_f'=>0, 'vs_m'=>0, 'vs_f'=>0, 's_m'=>0, 's_f'=>0, 'fs_m'=>0, 'fs_f'=>0, 'fail_m'=>0, 'fail_f'=>0];

                    // Get subjects for this grade level
                    $subjects = [];
                    $sub_query = mysqli_query($conn, "SELECT subject_id FROM subject_tbl WHERE subject_grade = '$grade'");
                    while($s = mysqli_fetch_assoc($sub_query)){
                        $subjects[] = $s['subject_id'];
                    }

                    // Get active students for this section
                    $stud_query = mysqli_query($conn, "SELECT st.student_id, e.sex FROM student_tbl st INNER JOIN enrollment_tbl e ON st.enrollment_id = e.enrollmentId WHERE st.section_id = '$section_id' AND st.status = 1");

                    while($stud = mysqli_fetch_assoc($stud_query)) {
                        $sid = $stud['student_id'];
                        $sex = strtoupper($stud['sex']) == 'MALE' ? '_m' : '_f';

                        $failed_subjects_count = 0;
                        $total_gen_avg = 0;
                        $sub_count = count($subjects);
                        $has_inc = false;

                        // Compute final grades for all subjects
                        foreach($subjects as $sub_id) {
                            $grade_val = getFinalSubjectGrade($conn, $sid, $sub_id);
                            if($grade_val === 'INC') {
                                $has_inc = true;
                            } else {
                                $total_gen_avg += $grade_val;
                                if($grade_val < 75) {
                                    $failed_subjects_count++;
                                }
                            }
                        }

                        // Determine Promotion Status and Achievement Level
                        if($sub_count > 0 && !$has_inc) {
                            $gen_avg = $total_gen_avg / $sub_count;

                            if($failed_subjects_count == 0) {
                                $sec_status['prom'.$sex]++;
                                $gt_status['prom'.$sex]++;
                            } elseif($failed_subjects_count <= 2) {
                                $sec_status['cond'.$sex]++;
                                $gt_status['cond'.$sex]++;
                            } else {
                                $sec_status['ret'.$sex]++;
                                $gt_status['ret'.$sex]++;
                            }

                            // Tally Achievement Level based on General Average
                            if($gen_avg >= 90) { $sec_achieve['out'.$sex]++; $gt_achieve['out'.$sex]++; }
                            elseif($gen_avg >= 85) { $sec_achieve['vs'.$sex]++; $gt_achieve['vs'.$sex]++; }
                            elseif($gen_avg >= 80) { $sec_achieve['s'.$sex]++; $gt_achieve['s'.$sex]++; }
                            elseif($gen_avg >= 75) { $sec_achieve['fs'.$sex]++; $gt_achieve['fs'.$sex]++; }
                            else { $sec_achieve['fail'.$sex]++; $gt_achieve['fail'.$sex]++; }
                        }
                    }

                    // Print Row for Section
                    echo "<tr>";
                    echo "<td>Grade $grade</td>";
                    echo "<td>$section_name</td>";
                    echo "<td class='text-left'>$adviser</td>";

                    // Print Status Columns
                    echo "<td>{$sec_status['prom_m']}</td><td>{$sec_status['prom_f']}</td><td class='font-weight-bold'>".($sec_status['prom_m']+$sec_status['prom_f'])."</td>";
                    echo "<td>{$sec_status['cond_m']}</td><td>{$sec_status['cond_f']}</td><td class='font-weight-bold'>".($sec_status['cond_m']+$sec_status['cond_f'])."</td>";
                    echo "<td>{$sec_status['ret_m']}</td><td>{$sec_status['ret_f']}</td><td class='font-weight-bold'>".($sec_status['ret_m']+$sec_status['ret_f'])."</td>";

                    // Print Achievement Columns
                    echo "<td>{$sec_achieve['out_m']}</td><td>{$sec_achieve['out_f']}</td><td class='font-weight-bold'>".($sec_achieve['out_m']+$sec_achieve['out_f'])."</td>";
                    echo "<td>{$sec_achieve['vs_m']}</td><td>{$sec_achieve['vs_f']}</td><td class='font-weight-bold'>".($sec_achieve['vs_m']+$sec_achieve['vs_f'])."</td>";
                    echo "<td>{$sec_achieve['s_m']}</td><td>{$sec_achieve['s_f']}</td><td class='font-weight-bold'>".($sec_achieve['s_m']+$sec_achieve['s_f'])."</td>";
                    echo "<td>{$sec_achieve['fs_m']}</td><td>{$sec_achieve['fs_f']}</td><td class='font-weight-bold'>".($sec_achieve['fs_m']+$sec_achieve['fs_f'])."</td>";
                    echo "<td>{$sec_achieve['fail_m']}</td><td>{$sec_achieve['fail_f']}</td><td class='font-weight-bold'>".($sec_achieve['fail_m']+$sec_achieve['fail_f'])."</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='27' style='padding:20px;'>No data available.</td></tr>";
            }
            ?>
            
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td colspan="3" style="text-align: right; padding-right: 10px;">GRAND TOTAL</td>
                
                <td><?= $gt_status['prom_m'] ?></td><td><?= $gt_status['prom_f'] ?></td><td><?= $gt_status['prom_m'] + $gt_status['prom_f'] ?></td>
                <td><?= $gt_status['cond_m'] ?></td><td><?= $gt_status['cond_f'] ?></td><td><?= $gt_status['cond_m'] + $gt_status['cond_f'] ?></td>
                <td><?= $gt_status['ret_m'] ?></td><td><?= $gt_status['ret_f'] ?></td><td><?= $gt_status['ret_m'] + $gt_status['ret_f'] ?></td>
                
                <td><?= $gt_achieve['out_m'] ?></td><td><?= $gt_achieve['out_f'] ?></td><td><?= $gt_achieve['out_m'] + $gt_achieve['out_f'] ?></td>
                <td><?= $gt_achieve['vs_m'] ?></td><td><?= $gt_achieve['vs_f'] ?></td><td><?= $gt_achieve['vs_m'] + $gt_achieve['vs_f'] ?></td>
                <td><?= $gt_achieve['s_m'] ?></td><td><?= $gt_achieve['s_f'] ?></td><td><?= $gt_achieve['s_m'] + $gt_achieve['s_f'] ?></td>
                <td><?= $gt_achieve['fs_m'] ?></td><td><?= $gt_achieve['fs_f'] ?></td><td><?= $gt_achieve['fs_m'] + $gt_achieve['fs_f'] ?></td>
                <td><?= $gt_achieve['fail_m'] ?></td><td><?= $gt_achieve['fail_f'] ?></td><td><?= $gt_achieve['fail_m'] + $gt_achieve['fail_f'] ?></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-container">
        <div class="signature-box">
            <div>Prepared and Submitted by:</div>
            <div class="sign-line" style="margin-top: 30px;"></div>
            <div style="font-weight:bold; text-transform:uppercase;">School Head / Principal</div>
        </div>
        <div class="signature-box">
            <div>Reviewed and Checked by:</div>
            <div class="sign-line" style="margin-top: 30px;"></div>
            <div style="font-weight:bold; text-transform:uppercase;">Division Representative</div>
        </div>
    </div>

</body>
</html>