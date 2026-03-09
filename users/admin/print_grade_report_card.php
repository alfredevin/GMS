<?php
include '../../config.php';

$student_id = $_GET['studId'];

// GET STUDENT DETAILS
$sel_student = "SELECT * FROM student_tbl
                INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                WHERE student_id = '$student_id'";
$result_sel_student = mysqli_query($conn, $sel_student);
$res_student = mysqli_fetch_assoc($result_sel_student);

$student_name = strtoupper($res_student["lastname"] . ', ' . $res_student["firstname"] . ' ' . $res_student["middlename"]);
$lrn = $res_student['lrn'];
$sex = strtoupper($res_student['sex']);
$age = $res_student['age'];

// FUNCTION TO COMPUTE AVERAGE (Galing sa logic mo)
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
    <meta charset="utf-8">
    <title>Learner's Progress Report (SF9)</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
            color: black;
        }

        .header-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-container img {
            width: 70px;
            height: auto;
            vertical-align: middle;
        }

        .header-text {
            display: inline-block;
            vertical-align: middle;
            margin: 0 20px;
        }

        .header-text h3,
        .header-text h4,
        .header-text p {
            margin: 2px 0;
            text-transform: uppercase;
        }

        .student-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .student-info td {
            padding: 3px;
            font-weight: bold;
        }

        .border-bottom {
            border-bottom: 1px solid black;
            display: inline-block;
            width: 100%;
            min-height: 15px;
        }

        /* DEPED STYLE TABLE */
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .grade-table th,
        .grade-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            font-size: 11px;
        }

        .grade-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left !important;
            padding-left: 10px !important;
        }

        .text-right {
            text-align: right !important;
            padding-right: 10px !important;
        }

        .section-title {
            margin: 10px 0;
            font-weight: bold;
            font-size: 12px;
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* Remarks Colors */
        .text-danger {
            color: red;
            font-weight: bold;
        }

        .text-black {
            color: black;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header-container">
        <div class="header-text">
            <h4>Republic of the Philippines</h4>
            <h3>Department of Education</h3>
            <h4>Region IV-B MIMAROPA</h4>
            <h3>BANGBANG NATIONAL HIGH SCHOOL</h3>
            <p>Bangbang, Gasan, Marinduque</p>
        </div>
    </div>

    <hr>

    <table class="student-info">
        <tr>
            <td width="10%">Name:</td>
            <td width="40%"><span class="border-bottom"><?php echo $student_name; ?></span></td>
            <td width="10%">LRN:</td>
            <td width="20%"><span class="border-bottom"><?php echo $lrn; ?></span></td>
            <td width="10%">Sex:</td>
            <td width="10%"><span class="border-bottom"><?php echo $sex; ?></span></td>
        </tr>
        <tr>
            <td>Age:</td>
            <td><span class="border-bottom"><?php echo $age; ?></span></td>
            <td>Curriculum:</td>
            <td colspan="3"><span class="border-bottom">Junior Highschool Education Curriculum</span></td>
        </tr>
    </table>

    <br>

    <?php
    // Get all grade levels the student has taken subjects in, ordered sequentially
    $gradeLevelsQuery = "SELECT DISTINCT subject_grade FROM subject_tbl ORDER BY subject_grade ASC";
    $gradeLevelsResult = mysqli_query($conn, $gradeLevelsQuery);

    if (mysqli_num_rows($gradeLevelsResult) > 0) {
        while ($gradeRow = mysqli_fetch_assoc($gradeLevelsResult)) {
            $gradeLevel = $gradeRow['subject_grade'];

            // Determine Section for this Grade Level (Optional: If you store section history)
            // For now, using the current section or generic
            $displaySection = ($gradeLevel == $res_student['grade']) ? $res_student['section_name'] : "N/A (History)";

            echo "<div class='section-title'>REPORT ON LEARNING PROGRESS AND ACHIEVEMENT - GRADE $gradeLevel</div>";
    ?>

            <table class="grade-table">
                <thead>
                    <tr>
                        <th rowspan="2" width="40%">Learning Areas</th>
                        <th colspan="4">Quarter</th>
                        <th rowspan="2" width="10%">Final Grade</th>
                        <th rowspan="2" width="10%">Remarks</th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get subjects for this specific grade level
                    $subjectQuery = "SELECT * FROM subject_tbl WHERE subject_grade = '$gradeLevel'";
                    $subjectResult = mysqli_query($conn, $subjectQuery);

                    $totalFinalAverage = 0;
                    $totalSubjects = 0;

                    while ($subject = mysqli_fetch_assoc($subjectResult)) {
                        $subject_id = $subject['subject_id'];
                        $subject_name = $subject['subject_name'];

                        $quarterGrades = [];
                        $finalSum = 0;
                        $gradeCount = 0;

                        // Loop through 4 Quarters
                        for ($q = 1; $q <= 4; $q++) {
                            // Your Calculation Logic
                            $quizQuery = "SELECT * FROM announcement_tbl 
                                      LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
                                      WHERE quarterly = '$q' AND type = 'quiz' AND subject = '$subject_id' AND student_id = '$student_id'";
                            $ptQuery = "SELECT * FROM announcement_tbl 
                                    LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
                                    WHERE quarterly = '$q' AND type = 'pt' AND subject = '$subject_id' AND student_id = '$student_id'";
                            $examQuery = "SELECT * FROM announcement_tbl 
                                      LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
                                      WHERE quarterly = '$q' AND type = 'exam' AND subject = '$subject_id' AND student_id = '$student_id'";

                            $quiz = computeAverage(mysqli_query($conn, $quizQuery));
                            $pt = computeAverage(mysqli_query($conn, $ptQuery));
                            $exam = computeAverage(mysqli_query($conn, $examQuery));

                            // Formula: 20% Quiz, 60% PT, 20% Exam
                            $finalGrade = ($quiz * 0.20) + ($pt * 0.60) + ($exam * 0.20);

                            if ($finalGrade > 0) {
                                $quarterGrades[$q] = round($finalGrade); // DepEd usually rounds off grades to whole numbers
                                $finalSum += $finalGrade;
                                $gradeCount++;
                            } else {
                                $quarterGrades[$q] = ''; // Leave blank if no grade yet
                            }
                        }

                        // Compute Final Grade for Subject
                        if ($gradeCount > 0) { // If at least 1 quarter has grade
                            // Note: Usually Final Grade is average of 4 quarters. 
                            // Logic adjusted to handle ongoing year
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

                        $remarkColor = ($remarks == 'Failed') ? 'text-danger' : 'text-black';

                        echo "<tr>
                            <td class='text-left'>$subject_name</td>
                            <td>{$quarterGrades[1]}</td>
                            <td>{$quarterGrades[2]}</td>
                            <td>{$quarterGrades[3]}</td>
                            <td>{$quarterGrades[4]}</td>
                            <td><b>$finalSubjectGrade</b></td>
                            <td class='$remarkColor'>$remarks</td>
                          </tr>";
                    }

                    // GENERAL AVERAGE COMPUTATION
                    if ($totalSubjects > 0) {
                        $generalAverage = round($totalFinalAverage / $totalSubjects);
                        $finalRemarks = ($generalAverage >= 75) ? 'PROMOTED' : 'RETAINED';
                    } else {
                        $generalAverage = '';
                        $finalRemarks = '';
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #f0f0f0;">
                        <th colspan="5" class="text-right">GENERAL AVERAGE</th>
                        <th><?php echo $generalAverage; ?></th>
                        <th><?php echo $finalRemarks; ?></th>
                    </tr>
                </tfoot>
            </table>

    <?php
        } // End While Grade Loop
    } else {
        echo "<p align='center'>No academic records found for this student.</p>";
    }
    ?>

    <div style="margin-top: 20px; font-size: 10px;">
        <p><strong>Descriptors:</strong></p>
        <table style="width: 50%; border-collapse: collapse; font-size: 10px;">
            <tr>
                <td>Outstanding</td>
                <td>90-100</td>
                <td>Very Satisfactory</td>
                <td>85-89</td>
            </tr>
            <tr>
                <td>Satisfactory</td>
                <td>80-84</td>
                <td>Fairly Satisfactory</td>
                <td>75-79</td>
            </tr>
            <tr>
                <td>Did Not Meet Expectations</td>
                <td>Below 75</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <br><br><br>
    <table width="100%" style="text-align: center;">
        <tr>
            <td width="45%">
                <span class="border-bottom" style="width: 80%;">&nbsp;</span><br>
                Signature of Adviser over Printed Name
            </td>
            <td width="10%"></td>
            <td width="45%">
                <span class="border-bottom" style="width: 80%; font-weight:bold;">DR. JUAN DELA CRUZ</span><br> School Principal
            </td>
        </tr>
    </table>

</body>

</html>