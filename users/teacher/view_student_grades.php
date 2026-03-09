<?php
include '../../config.php';
$filterGrade = isset($_GET['subject_grade']) ? $_GET['subject_grade'] : '';
$filterSubject = isset($_GET['subject_name']) ? $_GET['subject_name'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$filterSection = isset($_GET['section']) ? $_GET['section'] : '';
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
                    <a href="student_grade" class="btn btn-primary btn-sm mb-3">Back to Page</a>
                    <a href="print_subject_class_grade?grade=<?= $filterGrade ?>&section=<?= $filterSection ?>&subject=<?= $filterSubject ?>&name=<?= $name ?>&teacher=<?= $teacher_id ?>" class="btn btn-danger btn-sm mb-3" target="_blank">
                        <i class="fas fa-print"></i> Print
                    </a>
                    <a href="#details_grade" data-toggle="modal" class="btn btn-warning btn-sm mb-3"> <i class="fas fa-eye"></i> Grading Details</a>
                    <div class="card shadow mb-4  ">

                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">List of Students </h6>
                                    <h6 class="m-0 font-weight-bold text-primary">Subject Name : <?php echo $name; ?> </h6>
                                    <h6 class="m-0 font-weight-bold text-primary">Grade :
                                        <?php echo $filterGrade;
                                        if (!empty($filterSection)) {
                                            $select_section = "SELECT * FROM section_tbl WHERE section_id  = '$filterSection'";
                                            $result_select_section = mysqli_query($conn, $select_section);
                                            $sec_row = $result_select_section->fetch_array(MYSQLI_ASSOC);
                                            $select_section = $sec_row["section_name"];
                                            echo ' |  ' . $select_section . '';
                                        } ?> </h6>
                                </div>
                                <div class="col-">
                                    <form method="GET">
                                        <!-- Retain subject_grade -->
                                        <input type="hidden" name="subject_grade" value="<?php echo htmlspecialchars($_GET['subject_grade'] ?? $filterGrade); ?>">
                                        <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($_GET['subject_name'] ?? $filterSubject); ?>">
                                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($_GET['name'] ?? $name); ?>">

                                        <select name="section" id="sectionFilter" onchange="this.form.submit()" class="form-control">
                                            <option value="" selected disabled>Select Section</option>
                                            <?php
                                            $select_section = "SELECT * FROM section_tbl WHERE section_grade = '$filterGrade'";
                                            $result_select_section = mysqli_query($conn, $select_section);
                                            while ($row_sec = mysqli_fetch_array($result_select_section)) {
                                                $selected = (isset($_GET['section']) && $_GET['section'] == $row_sec['section_id']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $row_sec['section_id'] ?>" <?php echo $selected; ?>>
                                                    <?php echo $row_sec['section_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-bolder text-center">List of Students</th>
                                            <th colspan="4" class="text-bolder text-center"> Quarterly Ratings</th>
                                            <th colspan="2" class="text-bolder"> </th>
                                        </tr>
                                        <tr>
                                            <th>Student Name </th>
                                            <th>1st </th>
                                            <th>2nd</th>
                                            <th>3rd</th>
                                            <th>4th</th>
                                            <th class="text-bolder text-center">Final</th>
                                            <th class="text-bolder text-center">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                                        if (!empty($filterSection)) {
                                            $sql = "SELECT * FROM student_tbl
                                            INNER JOIN enrollment_tbl on enrollment_tbl.enrollmentId  = student_tbl.enrollment_id
                                            INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                            WHERE student_grade = '$filterGrade' AND student_tbl.section_id = '$filterSection'";
                                        } else {
                                            $sql = "SELECT * FROM student_tbl
                                            INNER JOIN enrollment_tbl on enrollment_tbl.enrollmentId  = student_tbl.enrollment_id
                                            INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id
                                            WHERE student_grade = '$filterGrade' ";
                                        }
                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];

                                            $quarterGrades = [];
                                            $finalSum = 0;
                                            $gradeCount = 0;

                                            for ($q = 1; $q <= 4; $q++) {
                                                // Queries for quiz, PT, and exam
                                                $quizQuery = "SELECT * FROM announcement_tbl 
            LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
            WHERE quarterly = '$q' AND type = 'quiz' AND section ='$filterSection' AND subject = '$filterSubject' AND student_id = '$student_id'";
                                                $ptQuery = "SELECT * FROM announcement_tbl 
            LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
            WHERE quarterly = '$q' AND type = 'pt' AND section ='$filterSection' AND subject = '$filterSubject' AND student_id = '$student_id'";
                                                $examQuery = "SELECT * FROM announcement_tbl 
            LEFT JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
            WHERE quarterly = '$q' AND type = 'exam' AND section ='$filterSection' AND subject = '$filterSubject' AND student_id = '$student_id'";

                                                // Function to compute average


                                                $quizRes = mysqli_query($conn, $quizQuery);
                                                $ptRes = mysqli_query($conn, $ptQuery);
                                                $examRes = mysqli_query($conn, $examQuery);

                                                $quiz = computeAverage($quizRes);
                                                $pt = computeAverage($ptRes);
                                                $exam = computeAverage($examRes);

                                                $finalGrade = ($quiz * 0.20) + ($pt * 0.60) + ($exam * 0.20);

                                                if ($finalGrade > 0) {
                                                    $quarterGrades[$q] = number_format($finalGrade, 2);
                                                    $finalSum += $finalGrade;
                                                    $gradeCount++;
                                                } else {
                                                    $quarterGrades[$q] = '';
                                                }
                                            }

                                            if ($gradeCount == 4) {
                                                $finalAverage = number_format($finalSum / $gradeCount, 2);
                                                $remarks = $finalAverage >= 75 ? 'Passed' : 'Failed';
                                            } else {
                                                $finalAverage = 'Incomplete';
                                                $remarks = 'Grade Not Yet Available';
                                            }
                                        ?>
                                            <tr>
                                                <td><?= $res['lastname'] . ' ' . $res['firstname'] . ' ' . $res['middlename']; ?></td>
                                                <td><?= $quarterGrades[1] !== '' ? $quarterGrades[1] : '---'; ?></td>
                                                <td><?= $quarterGrades[2] !== '' ? $quarterGrades[2] : '---'; ?></td>
                                                <td><?= $quarterGrades[3] !== '' ? $quarterGrades[3] : '---'; ?></td>
                                                <td><?= $quarterGrades[4] !== '' ? $quarterGrades[4] : '---'; ?></td>

                                                <td class="text-center"><?= $finalAverage; ?></td>
                                                <td class="text-center <?= $remarks == 'Passed' ? 'bg-success text-white' : ($remarks == 'Failed' ? 'bg-danger text-white' : ($remarks == 'Grade Not Yet Available' ? 'bg-warning text-dark' : '')) ?>">
                                                    <?= $remarks; ?>
                                                </td>


                                            </tr>
                                        <?php } ?>
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
    <div class="modal fade" id="details_grade" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" style="text-transform:uppercase;font-family:'Times New Roman', Times, serif;"> Grade Details Information</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body" id="modal-details-body">
                    <p style="color: black; font-family:'Times New Roman', Times, serif;">
                        The final grade for each quarter is computed using the following formula:<br><br>

                        <strong>
                            Final Grade = [Average of Quiz Percentages × 20%] + [Average of Performance Task Percentages × 60%] + [Average of Exam Percentages × 20%]
                        </strong><br><br>

                        <u>Weight Distribution:</u><br>
                        - 📘 <strong>Quiz</strong>: 20% of the final grade<br>
                        - ✏️ <strong>Performance Task</strong>: 60% of the final grade<br>
                        - 📝 <strong>Quarterly Exam</strong>: 20% of the final grade<br><br>

                        Each individual activity (Quiz, PT, or Exam) is converted into a percentage:<br>
                        <em>(Score ÷ Total Items × 100)</em><br>
                        Then, all percentages of the same type are averaged. Finally, each average is multiplied by its respective weight. The total gives the final grade for the quarter.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>