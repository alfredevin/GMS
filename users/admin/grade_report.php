<?php
include '../../config.php';

$student_id = $_GET['studId'];


$filter_grade = $_GET['grade'] ?? '';
$filter_section = $_GET['section'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <div class="container-fluid  "> <a href="grade_report_card?grade=<?= $filter_grade ?>&section=<?= $filter_section ?>" class="btn btn-primary btn-sm mb-3"> Back to Page</a>
                <a href="print_grade_report_card?grade=<?= $filter_grade ?>&section=<?= $filter_section ?>&studId=<?= $student_id; ?>" class="btn btn-danger btn-sm mb-3" target="_blank"><i class="fas fa-print"></i> Print</a>

                    <div class="card shadow mb-4  ">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">

                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">Learning Academic Record</h6>
                                    <h6 class="m-0 font-weight-bold text-primary text-center">
                                        <?php
                                        $sel_student = "SELECT * FROM student_tbl
                                        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId  = student_tbl.enrollment_id
                                        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id  WHERE   student_id = '$student_id'";
                                        $result_sel_student = mysqli_query($conn, $sel_student);
                                        $res_student = mysqli_fetch_assoc($result_sel_student);
                                        $student_name_sel = $res_student["firstname"] . ' ' . $res_student["middlename"] . ' ' . $res_student["lastname"];
                                        $student_grade_sec = 'GRADE - ' . $res_student["student_grade"] . ' ' . $res_student["section_name"];
                                        $section_grade = $res_student["student_grade"];
                                        echo $student_name_sel . '<br>';
                                        ?> </h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
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

                                $student_id = $_GET['studId'];

                                // Get all distinct grade levels of the student based on subjects taken
                                $gradeLevelsQuery = "SELECT DISTINCT subject_grade FROM subject_tbl ORDER BY subject_grade ASC";
                                $gradeLevelsResult = mysqli_query($conn, $gradeLevelsQuery);

                                while ($gradeRow = mysqli_fetch_assoc($gradeLevelsResult)) {
                                    $gradeLevel = $gradeRow['subject_grade'];
                                    echo "<tr>
                                            <td>
                                            <h4 class='  text-uppercase' style=\"font-family: 'Times New Roman', Times, serif; font-size:18px;\">School:<span style=\"text-decoration:underline;\"> Bangbang National Highschool</span>
                                             </h4></td>
                                            <td><h4 class='  text-uppercase' style=\"font-family: 'Times New Roman', Times, serif; font-size:18px;\">Classifies as:<span style=\"text-decoration:underline;\"> Grade $gradeLevel</span></h4></td>  
                                        </tr>";

                                    // Get all subjects for this grade
                                    $subjectQuery = "SELECT * FROM subject_tbl WHERE subject_grade = '$gradeLevel'";
                                    $subjectResult = mysqli_query($conn, $subjectQuery);

                                    // Same logic as your current table
                                    echo '<table class="table table-bordered">';
                                    echo '<thead>
            <tr><th>Subject</th><th>1st</th><th>2nd</th><th>3rd</th><th>4th</th><th>Final</th><th>Remarks</th></tr>
          </thead>';
                                    echo '<tbody>';

                                    $totalFinalAverage = 0;
                                    $totalSubjects = 0;

                                    while ($subject = mysqli_fetch_assoc($subjectResult)) {
                                        $subject_id = $subject['subject_id'];
                                        $subject_name = $subject['subject_name'];

                                        $quarterGrades = [];
                                        $finalSum = 0;
                                        $gradeCount = 0;

                                        for ($q = 1; $q <= 4; $q++) {
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

                                            $finalGrade = ($quiz * 0.20) + ($pt * 0.60) + ($exam * 0.20);

                                            if ($finalGrade > 0) {
                                                $quarterGrades[$q] = number_format($finalGrade, 2);
                                                $finalSum += $finalGrade;
                                                $gradeCount++;
                                            } else {
                                                $quarterGrades[$q] = '---';
                                            }
                                        }

                                        if ($gradeCount == 4) {
                                            $finalAverage = number_format($finalSum / 4, 2);
                                            $remarks = $finalAverage >= 75 ? 'Passed' : 'Failed';

                                            $totalFinalAverage += $finalAverage;
                                            $totalSubjects++;
                                        } else {
                                            $finalAverage = 'Incomplete';
                                            $remarks = 'Incomplete';
                                        }

                                        $remarksClass = ($remarks == 'Passed' ? 'bg-success text-white' : ($remarks == 'Failed' ? 'bg-danger text-white' : 'bg-warning text-dark'));

                                        echo "<tr>
                <td>$subject_name</td>
                <td>{$quarterGrades[1]}</td>
                <td>{$quarterGrades[2]}</td>
                <td>{$quarterGrades[3]}</td>
                <td>{$quarterGrades[4]}</td>
                <td class='text-center'>$finalAverage</td>
                <td class='text-center $remarksClass'>$remarks</td>
              </tr>";
                                    }

                                    if ($totalSubjects > 0) {
                                        $generalAverage = number_format($totalFinalAverage / $totalSubjects, 2);
                                        $finalRemarks = $generalAverage >= 90 ? 'With Honors' : ($generalAverage >= 75 ? 'Promoted' : 'Failed');
                                        $remarksClass = $generalAverage >= 90 ? 'bg-success text-white' : ($generalAverage >= 75 ? 'bg-success text-white' : 'bg-danger text-white');
                                    } else {
                                        $generalAverage = '---';
                                        $finalRemarks = 'Incomplete';
                                        $remarksClass = 'bg-warning text-dark';
                                    }

                                    echo "<tfoot>
            <tr>
                <th colspan='5' class='text-right'>General Average</th>
                <th class='text-center'>$generalAverage</th>
                <th class='text-center $remarksClass'>$finalRemarks</th>
            </tr>
          </tfoot>";
                                    echo '</tbody></table>';
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include './../template/footer.php'; ?>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <?php include './../template/script.php'; ?>
</body>

</html>