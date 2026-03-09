<?php
include '../../config.php';
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
                <div class="container-fluid  ">

                    <div class="card shadow mb-4  ">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">

                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">Learning Academic Record</h6>
                                    <h6 class="m-0 font-weight-bold text-primary text-center">
                                        <?php

                                        $student_id = $my_student_id;
                                        $sel_student = "SELECT * FROM student_tbl
                                        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId  = student_tbl.enrollment_id
                                        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id  WHERE   student_id = '$student_id'";
                                        $result_sel_student = mysqli_query($conn, $sel_student);
                                        $res_student = mysqli_fetch_assoc($result_sel_student);
                                        $student_name_sel = $res_student["firstname"] . ' ' . $res_student["middlename"] . ' ' . $res_student["lastname"];
                                        $student_grade_sec = 'GRADE - ' . $res_student["student_grade"] . ' ' . $res_student["section_name"];
                                        $grade = $res_student['student_grade'];
                                        echo $student_name_sel . '<br>';
                                        echo $student_grade_sec;
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

                                $student_grade = $grade; // example, or from DB

                                // Get all subjects under this grade level
                                $subjectQuery = "SELECT * FROM subject_tbl WHERE subject_grade = '$student_grade'";
                                $subjectResult = mysqli_query($conn, $subjectQuery);
                                ?>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Learning Areas</th>
                                            <th colspan="4" class="text-center text-bolder">Quarterly Rating</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th>Subject Name</th>
                                            <th>1st</th>
                                            <th>2nd</th>
                                            <th>3rd</th>
                                            <th>4th</th>
                                            <th>Final Rating</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
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

                                                // Accumulate for general average
                                                $totalFinalAverage += $finalAverage;
                                                $totalSubjects++;
                                            } else {
                                                $finalAverage = 'Incomplete';
                                                $remarks = 'Grade Not Yet Available';
                                            }


                                            echo "<tr>";
                                            echo "<td>$subject_name</td>";
                                            echo "<td>{$quarterGrades[1]}</td>";
                                            echo "<td>{$quarterGrades[2]}</td>";
                                            echo "<td>{$quarterGrades[3]}</td>";
                                            echo "<td>{$quarterGrades[4]}</td>";
                                            echo "<td class='text-center'>$finalAverage</td>";
                                            echo "<td class='text-center " .
                                                ($remarks == 'Passed' ? 'bg-success text-white' : ($remarks == 'Failed' ? 'bg-danger text-white' : 'bg-warning text-dark')) .
                                                "'>$remarks</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    <tfoot>
                                        <?php
                                        if ($totalSubjects > 0) {
                                            $generalAverage = number_format($totalFinalAverage / $totalSubjects, 2);
                                            if ($generalAverage < 75) {
                                                $finalRemarks = 'Failed';
                                                $remarksClass = 'bg-danger text-white';
                                            } elseif ($generalAverage >= 90) {
                                                $finalRemarks = 'Promoted with Honors';
                                                $remarksClass = 'bg-success text-white';
                                            } else {
                                                $finalRemarks = 'Promoted';
                                                $remarksClass = 'bg-success text-white';
                                            }
                                        } else {
                                            $generalAverage = '---';
                                            $finalRemarks = 'Incomplete';
                                            $remarksClass = 'bg-warning text-dark';
                                        }
                                        ?>
                                        <tr>
                                            <th></th>
                                            <th colspan="4" class="text-center">General Average</th>
                                            <th class="text-center"><?= $generalAverage ?></th>
                                            <th class="text-center <?= $remarksClass ?>"><?= $finalRemarks ?></th>
                                        </tr>
                                    </tfoot>

                                    </tbody>
                                </table>

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'en',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: {
                    url: 'fetch_attendance.php?student_id=<?= $student_id ?>',
                    failure: function() {
                        alert('There was an error fetching events!');
                    }
                }
            });

            calendar.render();
        });
    </script>

    <style>
        #calendar {
            max-width: 900px;
            margin: 50px auto;
        }
    </style>
</body>

</html>