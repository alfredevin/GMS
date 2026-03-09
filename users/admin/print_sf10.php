<?php
include '../../config.php';

$student_id = $_GET['studId'];

// 1. GET BASIC STUDENT INFO
$stud_sql = "SELECT * FROM student_tbl 
             INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id 
             WHERE student_id = '$student_id'";
$stud_res = mysqli_query($conn, $stud_sql);
$student = mysqli_fetch_assoc($stud_res);

$fullname = strtoupper($student['lastname'] . ', ' . $student['firstname'] . ' ' . $student['middlename']);
$lrn = $student['lrn'];
$birthdate = date('m/d/Y', strtotime($student['birthdate']));
$sex = strtoupper($student['sex']);

// --- HELPER FUNCTION FOR GRADE CALCULATION ---
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
?>

<!DOCTYPE html>
<html>

<head>
    <title>SF10 - Learner's Permanent Academic Record</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 5mm;
        }

        /* Long Bond Paper */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .font-bold {
            font-weight: bold;
        }

        .no-border {
            border: none !important;
        }

        /* TABLE STYLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        td,
        th {
            border: 1px solid black;
            padding: 2px;
            vertical-align: middle;
        }

        /* SPECIAL CLASSES */
        .header-logo {
            width: 50px;
        }

        .bg-gray {
            background-color: #dcdcdc;
        }

        .grade-header td {
            font-size: 9px;
            font-weight: bold;
            border: none;
            padding: 2px 0;
        }

        .rating-cell {
            width: 35px;
            text-align: center;
        }
    </style>
</head>

<body onload="window.print()">

    <table class="no-border" style="margin-bottom: 0;">
        <tr class="no-border">
            <td width="10%" class="no-border text-center"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Department_of_Education.svg/1200px-Department_of_Education.svg.png" class="header-logo"></td>
            <td width="80%" class="no-border text-center">
                <div style="font-size: 8px;">Republic of the Philippines</div>
                <div style="font-size: 8px;">Department of Education</div>
                <div style="font-size: 14px; font-weight: bold; margin: 5px 0;">Learner's Permanent Academic Record for Junior High School (SF10-JHS)</div>
                <div style="font-size: 8px; font-style: italic;">(Formerly Form 137)</div>
            </td>
            <td width="10%" class="no-border text-center"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Department_of_Education.svg/1200px-Department_of_Education.svg.png" class="header-logo"></td>
        </tr>
    </table>

    <div class="bg-gray text-center font-bold" style="padding: 2px; border: 1px solid black; border-bottom: none;">LEARNER'S INFORMATION</div>
    <table style="margin-bottom: 0;">
        <tr class="no-border">
            <td width="10%" class="no-border">LAST NAME:</td>
            <td width="20%" class="no-border font-bold" style="border-bottom: 1px solid black !important;"><?php echo $student['lastname']; ?></td>
            <td width="10%" class="no-border text-right">FIRST NAME:</td>
            <td width="25%" class="no-border font-bold" style="border-bottom: 1px solid black !important;"><?php echo $student['firstname']; ?></td>
            <td width="10%" class="no-border text-right">NAME EXTN:</td>
            <td width="5%" class="no-border font-bold" style="border-bottom: 1px solid black !important;"><?php echo $student['extname']; ?></td>
            <td width="10%" class="no-border text-right">MIDDLE NAME:</td>
            <td width="10%" class="no-border font-bold" style="border-bottom: 1px solid black !important;"><?php echo $student['middlename']; ?></td>
        </tr>
    </table>
    <table style="margin-top: 0;">
        <tr class="no-border">
            <td width="15%" class="no-border">Learner Reference Number (LRN):</td>
            <td width="15%" class="no-border font-bold" style="border-bottom: 1px solid black !important;"><?php echo $lrn; ?></td>
            <td width="10%" class="no-border text-right">Birthdate (mm/dd/yyyy):</td>
            <td width="10%" class="no-border font-bold" style="border-bottom: 1px solid black !important;"><?php echo $birthdate; ?></td>
            <td width="5%" class="no-border text-right">Sex:</td>
            <td width="5%" class="no-border font-bold" style="border-bottom: 1px solid black !important;"><?php echo $sex; ?></td>
        </tr>
    </table>

    <div class="bg-gray text-center font-bold" style="padding: 2px; border: 1px solid black; border-top: none;">ELIGIBILITY FOR JHS ENROLMENT</div>
    <table style="margin-top: 5px;">
        <tr class="no-border">
            <td width="25%" class="no-border">
                <div style="display:inline-block; width:10px; height:10px; border:1px solid black;"></div> Elementary School Completer
            </td>
            <td width="10%" class="no-border text-right">General Average:</td>
            <td width="10%" style="border-bottom: 1px solid black !important;"></td>
            <td width="10%" class="no-border text-right">Citation (if Any):</td>
            <td width="45%" style="border-bottom: 1px solid black !important;"></td>
        </tr>
    </table>

    <div class="bg-gray text-center font-bold" style="padding: 2px; border: 1px solid black; margin-top: 5px;">SCHOLASTIC RECORD</div>

    <?php
    $grades_to_print = [7, 8, 9, 10];

    foreach ($grades_to_print as $lvl) {

        // --- HEADER BLOCK ---
        echo '
        <div style="border: 1px solid black; padding: 2px; margin-top: 5px;">
            <table class="no-border" style="margin: 0;">
                <tr class="grade-header">
                    <td width="5%">School:</td>
                    <td width="25%" style="border-bottom: 1px solid black !important;">BANGBANG NATIONAL HIGH SCHOOL</td>
                    <td width="8%" class="text-right">School ID:</td>
                    <td width="8%" style="border-bottom: 1px solid black !important;">301531</td>
                    <td width="5%" class="text-right">District:</td>
                    <td width="10%" style="border-bottom: 1px solid black !important;">GASAN</td>
                    <td width="5%" class="text-right">Division:</td>
                    <td width="10%" style="border-bottom: 1px solid black !important;">MARINDUQUE</td>
                    <td width="5%" class="text-right">Region:</td>
                    <td width="10%" style="border-bottom: 1px solid black !important;">IV-B</td>
                </tr>
            </table>
            <table class="no-border" style="margin: 0;">
                <tr class="grade-header">
                    <td width="10%">Classified as Grade:</td>
                    <td width="5%" style="border-bottom: 1px solid black !important;">' . $lvl . '</td>
                    <td width="5%" class="text-right">Section:</td>
                    <td width="15%" style="border-bottom: 1px solid black !important;"></td> 
                    <td width="8%" class="text-right">School Year:</td>
                    <td width="10%" style="border-bottom: 1px solid black !important;"></td>
                    <td width="15%" class="text-right">Adviser:</td>
                    <td width="25%" style="border-bottom: 1px solid black !important;"></td>
                    <td width="7%" class="text-right">Signature:</td>
                    <td width="10%" style="border-bottom: 1px solid black !important;"></td>
                </tr>
            </table>
        </div>';

        // --- SUBJECTS & GRADES TABLE ---
        echo '
        <table style="margin-top: 0;">
            <thead>
                <tr class="bg-gray">
                    <th rowspan="2" width="40%">LEARNING AREAS</th>
                    <th colspan="4">Quarterly Rating</th>
                    <th rowspan="2" width="10%">FINAL<br>RATING</th>
                    <th rowspan="2" width="10%">REMARKS</th>
                </tr>
                <tr class="bg-gray">
                    <th>1</th><th>2</th><th>3</th><th>4</th>
                </tr>
            </thead>
            <tbody>';

        // --- DYNAMIC LOGIC ---
        // 1. Kunin ang Subjects ng Grade Level na ito
        $subjectQuery = "SELECT * FROM subject_tbl WHERE subject_grade = '$lvl'";
        $subjectResult = mysqli_query($conn, $subjectQuery);

        $totalFinalAverage = 0;
        $totalSubjects = 0;
        $rows_count = 0;

        while ($subject = mysqli_fetch_assoc($subjectResult)) {
            $rows_count++;
            $subject_id = $subject['subject_id'];
            $subject_name = $subject['subject_name'];

            $quarterGrades = [];
            $finalSum = 0;
            $gradeCount = 0;

            // 2. I-compute ang Grades per Quarter (Base sa Query mo)
            for ($q = 1; $q <= 4; $q++) {
                $quizQuery = "SELECT * FROM announcement_tbl LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'quiz' AND subject = '$subject_id' AND student_id = '$student_id'";
                $ptQuery = "SELECT * FROM announcement_tbl LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'pt' AND subject = '$subject_id' AND student_id = '$student_id'";
                $examQuery = "SELECT * FROM announcement_tbl LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'exam' AND subject = '$subject_id' AND student_id = '$student_id'";

                $quiz = computeAverage(mysqli_query($conn, $quizQuery));
                $pt = computeAverage(mysqli_query($conn, $ptQuery));
                $exam = computeAverage(mysqli_query($conn, $examQuery));

                // Formula: 20% Quiz, 60% PT, 20% Exam
                $finalGrade = ($quiz * 0.20) + ($pt * 0.60) + ($exam * 0.20);

                if ($finalGrade > 0) {
                    $quarterGrades[$q] = round($finalGrade);
                    $finalSum += $finalGrade;
                    $gradeCount++;
                } else {
                    $quarterGrades[$q] = '';
                }
            }

            // 3. Compute Final Subject Rating
            if ($gradeCount > 0) {
                $finalSubjectGrade = ($gradeCount == 4) ? round($finalSum / 4) : '';
                $remarks = ($finalSubjectGrade >= 75) ? 'Passed' : (($finalSubjectGrade != '') ? 'Failed' : '');
                if ($gradeCount == 4) {
                    $totalFinalAverage += $finalSubjectGrade;
                    $totalSubjects++;
                }
            } else {
                $finalSubjectGrade = '';
                $remarks = '';
            }

            echo "<tr>
                    <td style='text-align:left; padding-left:5px;'>$subject_name</td>
                    <td class='rating-cell'>{$quarterGrades[1]}</td>
                    <td class='rating-cell'>{$quarterGrades[2]}</td>
                    <td class='rating-cell'>{$quarterGrades[3]}</td>
                    <td class='rating-cell'>{$quarterGrades[4]}</td>
                    <td class='rating-cell font-bold'>$finalSubjectGrade</td>
                    <td class='text-center'>$remarks</td>
                  </tr>";
        }

        // Kung walang subjects sa Grade na ito, maglagay ng blank lines
        if ($rows_count == 0) {
            echo "<tr><td colspan='7' style='text-align:center; padding:10px;'>No Records Available for Grade $lvl</td></tr>";
        }

        // 4. Compute General Average
        if ($totalSubjects > 0) {
            $generalAverage = round($totalFinalAverage / $totalSubjects);
            $finalRemarks = ($generalAverage >= 75) ? 'PROMOTED' : 'RETAINED';
        } else {
            $generalAverage = '';
            $finalRemarks = '';
        }

        echo '<tr>
                <td colspan="5" class="text-right font-bold" style="padding-right: 10px;">General Average</td>
                <td class="rating-cell font-bold">' . $generalAverage . '</td>
                <td class="text-center font-bold">' . $finalRemarks . '</td>
              </tr>
            </tbody>
        </table>';

        // --- REMEDIAL CLASSES ---
        echo '
        <table style="margin-top: -1px; margin-bottom: 10px;">
            <tr class="bg-gray">
                <th width="30%">Remedial Classes</th>
                <th width="20%">Final Rating</th>
                <th width="20%">Remedial Class Mark</th>
                <th width="20%">Recomputed Final Grade</th>
                <th width="10%">Remarks</th>
            </tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
        </table>';
    }
    ?>

    <div style="border: 1px solid black; padding: 5px; margin-top: 10px;">
        <div class="text-center font-bold" style="font-size: 12px; margin-bottom: 10px;">CERTIFICATION</div>
        <p style="text-indent: 30px; margin: 5px 0;">
            I CERTIFY that this is a true record of <span class="font-bold border-bottom" style="padding:0 5px;"><?php echo $fullname; ?></span> with LRN <span class="font-bold border-bottom" style="padding:0 5px;"><?php echo $lrn; ?></span>.
        </p>
        <table class="no-border" style="margin-top: 20px;">
            <tr>
                <td width="30%" class="no-border text-center">
                    <div style="border-bottom: 1px solid black; width: 80%; margin: 0 auto;">&nbsp;</div>Date
                </td>
                <td width="40%" class="no-border text-center">
                    <div style="border-bottom: 1px solid black; width: 90%; margin: 0 auto; font-weight: bold;">NORMINDA SOL MABAO</div>
                    Secondary School Principal I
                </td>
                <td width="30%" class="no-border text-center">
                    <div style="width: 60px; height: 60px; border: 1px solid #ccc; margin: 0 auto; font-size: 8px; line-height: 60px; color: #999;">(School Seal)</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>