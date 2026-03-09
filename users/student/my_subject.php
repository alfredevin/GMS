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

                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">List of Enrolled Subjects</h6>
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

                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th>Teacher</th>
                                            <th> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $student_id = $my_student_id;
                                        $select_my_subject = "SELECT * FROM subject_tbl
                                        INNER JOIN teacher_tbl ON teacher_tbl.teacher_id = subject_tbl.teacher_assign
                                        LEFT JOIN section_tbl ON section_tbl.section_id =  teacher_tbl.section_id WHERE subject_grade = '$student_grade'";
                                        $res_subject = mysqli_query($conn, $select_my_subject);
                                        while ($res = mysqli_fetch_assoc($res_subject)) {
                                            $filterSection = $res['section_id'];
                                            $filterSubject = $res['subject_id'];
                                            $name = $res['subject_name'];
                                            $filterGrade  = $res['grade_level'];
                                            $section_name = $res['section_name'];
                                        ?>
                                            <tr>
                                                <td><?php echo $res['subject_code']; ?></td>
                                                <td><?php echo $res['subject_name']; ?></td>
                                                <td><?php echo $res['teacher_name']; ?></td>
                                                <td><a href="#manage<?php echo $student_id; ?>" data-toggle="modal" class="btn btn-danger btn-sm">View Record</a></td>

                                            </tr>

                                            <div class="modal fade" id="manage<?php echo $student_id; ?>" data-studentid="<?php echo $student_id; ?>" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger">
                                                            <h5 class="modal-title text-white" style="text-transform:uppercase;font-family:'Times New Roman', Times, serif;"> <?php echo $name . ' - SUBJECT'; ?></h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <div class="modal-body" id="modal-details-body">
                                                            <h4 style="color:black;font-family:'Times New Roman', Times, serif;"> 
                                                            </h4>
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

                                                            <hr>
                                                            <!-- TABS FOR QUARTERS -->
                                                            <ul class="nav nav-tabs" id="quarterTabs_<?php echo $student_id; ?>" role="tablist">
                                                                <?php for ($q = 1; $q <= 4; $q++) { ?>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link <?= $q == 1 ? 'active' : '' ?>"
                                                                            id="q<?= $q ?>-tab-<?php echo $student_id; ?>"
                                                                            data-toggle="tab"
                                                                            href="#q<?= $q ?>_<?php echo $student_id; ?>"
                                                                            role="tab">
                                                                            Quarter <?= $q ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                                <li class="nav-item">
                                                                    <a class="nav-link"
                                                                        id="rating-tab-<?php echo $student_id; ?>"
                                                                        data-toggle="tab"
                                                                        href="#rating_<?php echo $student_id; ?>"
                                                                        role="tab">
                                                                        Quarterly Rating
                                                                    </a>
                                                                </li>
                                                            </ul>

                                                            <!-- TAB CONTENTS -->
                                                            <div class="tab-content mt-3" id="quarterContent_<?php echo $student_id; ?>">
                                                                <?php for ($q = 1; $q <= 4; $q++) { ?>
                                                                    <div class="tab-pane fade <?= $q == 1 ? 'show active' : '' ?>"
                                                                        id="q<?= $q ?>_<?php echo $student_id; ?>"
                                                                        role="tabpanel">
                                                                        <div class="row">
                                                                            <?php
                                                                            $announcement = "SELECT * FROM announcement_tbl
                                                                                LEFT JOIN  student_scores_tbl ON announcement_tbl.announcement_jd  = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'quiz' AND section ='$filterSection' AND subject = '$filterSubject' AND student_id = '$student_id'";
                                                                            $result_announcement = mysqli_query($conn, $announcement);
                                                                            $counter = 1;
                                                                            while ($res_announcement = mysqli_fetch_assoc($result_announcement)) {
                                                                            ?>
                                                                                <div class="col-md-3">
                                                                                    <label> Quiz <?php echo $counter . ' | Total Items: ' . $res_announcement['items']; ?></label>
                                                                                    <input type="number"
                                                                                        class="form-control quiz<?= $q ?>_<?php echo $student_id; ?>"
                                                                                        name="quiz[]"
                                                                                        placeholder="Enter Quiz Score"
                                                                                        oninput="calculateFinal(<?= $q ?>, <?= $student_id ?>)"
                                                                                        value="<?php echo $res_announcement['score'] ?>"
                                                                                        data-items="<?php echo $res_announcement['items']; ?>" readonly>
                                                                                </div>
                                                                            <?php

                                                                                $counter++;
                                                                            } ?>
                                                                            <br>
                                                                            <?php
                                                                            $announcement = "SELECT * FROM announcement_tbl 
                                                                                LEFT JOIN  student_scores_tbl ON announcement_tbl.announcement_jd  = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'pt' AND section ='$filterSection' AND subject = '$filterSubject' AND student_id = '$student_id'";
                                                                            $result_announcement = mysqli_query($conn, $announcement);
                                                                            $counter = 1;
                                                                            while ($res_announcement = mysqli_fetch_assoc($result_announcement)) {
                                                                            ?>
                                                                                <div class="col-md-3">
                                                                                    <label>Performance Task <?php echo $counter . ' |   Items: ' . $res_announcement['items']; ?></label>
                                                                                    <input type="number"
                                                                                        class="form-control pt<?= $q ?>_<?php echo $student_id; ?>"
                                                                                        name="pt[]"
                                                                                        placeholder="Enter PT Score"
                                                                                        oninput="calculateFinal(<?= $q ?>, <?= $student_id ?>)"
                                                                                        value="<?php echo $res_announcement['score'] ?>"
                                                                                        data-items="<?php echo $res_announcement['items']; ?>" readonly>

                                                                                </div>
                                                                            <?php
                                                                                $counter++;
                                                                            }
                                                                            ?>
                                                                            <?php
                                                                            $announcement = "SELECT * FROM announcement_tbl
                                                                                LEFT JOIN  student_scores_tbl ON announcement_tbl.announcement_jd  = student_scores_tbl.announcement_id WHERE quarterly = '$q' AND type = 'exam' AND section ='$filterSection' AND subject = '$filterSubject' AND student_id = '$student_id'";
                                                                            $result_announcement = mysqli_query($conn, $announcement);
                                                                            $counter = 1;
                                                                            while ($res_announcement = mysqli_fetch_assoc($result_announcement)) {
                                                                            ?>
                                                                                <div class="col-md-4">
                                                                                    <label>Quarter Exam <?php echo $counter . ' |   Items: ' . $res_announcement['items']; ?></label>
                                                                                    <input type="number"
                                                                                        class="form-control exam<?= $q ?>_<?php echo $student_id; ?>"
                                                                                        name="exam[]"
                                                                                        placeholder="Enter Exam Score"
                                                                                        oninput="calculateFinal(<?= $q ?>, <?= $student_id ?>)"
                                                                                        value="<?php echo $res_announcement['score'] ?>"
                                                                                        data-items="<?php echo $res_announcement['items']; ?>" readonly>
                                                                                </div>
                                                                            <?php
                                                                                $counter++;
                                                                            }
                                                                            ?>
                                                                        </div>

                                                                        <div class="row mt-3">
                                                                            <div class="col-md-4">
                                                                                <label><strong>Total for Quarter <?= $q ?>:</strong></label>
                                                                                <input type="text" class="form-control bg-light" id="final<?= $q ?>_<?php echo $student_id; ?>" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?><div class="tab-pane fade" id="rating_<?php echo $student_id; ?>" role="tabpanel">
                                                                    <div class="row mt-3">
                                                                        <?php for ($q = 1; $q <= 4; $q++) { ?>
                                                                            <div class="col-md-3">
                                                                                <label>Quarter <?= $q ?> Final:</label>
                                                                                <input type="text" class="form-control bg-light" id="quarter<?= $q ?>_<?php echo $student_id; ?>" readonly>
                                                                            </div>
                                                                        <?php } ?>
                                                                        <div class="col-md-3">
                                                                            <label><strong>Remarks:</strong></label>
                                                                            <input type="text" class="form-control" id="remarks_<?php echo $student_id; ?>" readonly>
                                                                        </div>

                                                                        <div class="col-md-3">
                                                                            <label><strong>Final Grade:</strong></label>
                                                                            <input type="text" class="form-control bg-success text-white" id="finalGrade_<?php echo $student_id; ?>" readonly>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="rating_<?php echo $student_id; ?>" role="tabpanel">

                                                                <div class="row mt-3">
                                                                    <?php for ($q = 1; $q <= 4; $q++) { ?>
                                                                        <div class="col-md-3">
                                                                            <label>Quarter <?= $q ?> Final:</label>
                                                                            <input type="text" class="form-control bg-light" id="quarter<?= $q ?>_<?php echo $student_id; ?>" readonly>
                                                                        </div>
                                                                    <?php } ?>

                                                                    <div class="col-md-3">
                                                                        <label><strong>Final Grade:</strong></label>
                                                                        <input type="text" class="form-control bg-success text-white" id="finalGrade_<?php echo $student_id; ?>" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', () => {
                                                    $('div.modal').each(function() {
                                                        const studentId = $(this).data('studentid');
                                                        for (let q = 1; q <= 4; q++) {
                                                            calculateFinal(q, studentId);
                                                        }
                                                    });
                                                });
                                                $('div.modal').on('shown.bs.modal', function() {
                                                    const studentId = $(this).data('studentid');
                                                    for (let q = 1; q <= 4; q++) {
                                                        calculateFinal(q, studentId);
                                                    }
                                                });

                                                function calculateFinal(q, studentId) {
                                                    const quizzes = document.querySelectorAll('.quiz' + q + '_' + studentId);
                                                    const pts = document.querySelectorAll('.pt' + q + '_' + studentId);
                                                    const exams = document.querySelectorAll('.exam' + q + '_' + studentId);
                                                    let quizTotalPercent = 0;
                                                    let ptTotalPercent = 0;
                                                    let examTotalPercent = 0;
                                                    quizzes.forEach(el => {
                                                        const score = parseFloat(el.value) || 0;
                                                        const items = parseFloat(el.dataset.items) || 0;
                                                        if (items > 0) {
                                                            quizTotalPercent += (score / items) * 100;
                                                        }
                                                    });
                                                    const quizAvgPercent = quizzes.length ? (quizTotalPercent / quizzes.length) : 0;
                                                    const quizFinal = quizAvgPercent * 0.20;
                                                    pts.forEach(el => {
                                                        const score = parseFloat(el.value) || 0;
                                                        const items = parseFloat(el.dataset.items) || 0;
                                                        if (items > 0) {
                                                            ptTotalPercent += (score / items) * 100;
                                                        }
                                                    });
                                                    const ptAvgPercent = pts.length ? (ptTotalPercent / pts.length) : 0;
                                                    const ptFinal = ptAvgPercent * 0.60;
                                                    exams.forEach(el => {
                                                        const score = parseFloat(el.value) || 0;
                                                        const items = parseFloat(el.dataset.items) || 0;
                                                        if (items > 0) {
                                                            examTotalPercent += (score / items) * 100;
                                                        }
                                                    });
                                                    const examAvgPercent = exams.length ? (examTotalPercent / exams.length) : 0;
                                                    const examFinal = examAvgPercent * 0.20;
                                                    const final = quizFinal + ptFinal + examFinal;
                                                    document.getElementById('final' + q + '_' + studentId).value = final.toFixed(2);
                                                    updateQuarterlyRating(studentId);
                                                }

                                                function updateQuarterlyRating(studentId) {
                                                    let sum = 0,
                                                        count = 0;
                                                    for (let q = 1; q <= 4; q++) {
                                                        const val = parseFloat(document.getElementById('final' + q + '_' + studentId).value) || 0;
                                                        if (val > 0) {
                                                            document.getElementById('quarter' + q + '_' + studentId).value = val.toFixed(2);
                                                            sum += val;
                                                            count++;
                                                        }
                                                    }
                                                    const finalGradeInput = document.getElementById('finalGrade_' + studentId);
                                                    const remarksInput = document.getElementById('remarks_' + studentId);
                                                    if (count) {
                                                        const final = (sum / count).toFixed(2);
                                                        finalGradeInput.value = final;
                                                        if (final >= 75) {
                                                            remarksInput.value = 'Passed';
                                                            finalGradeInput.classList.remove('bg-danger');
                                                            finalGradeInput.classList.add('bg-success', 'text-white');

                                                            remarksInput.classList.remove('bg-danger', 'text-white');
                                                            remarksInput.classList.add('bg-success', 'text-white');
                                                        } else {
                                                            remarksInput.value = 'Failed';
                                                            finalGradeInput.classList.remove('bg-success');
                                                            finalGradeInput.classList.add('bg-danger', 'text-white');

                                                            remarksInput.classList.remove('bg-success');
                                                            remarksInput.classList.add('bg-danger', 'text-white');
                                                        }
                                                    } else {
                                                        finalGradeInput.value = '';
                                                        remarksInput.value = '';
                                                    }
                                                }
                                            </script>

                                        <?php  
                                        } ?>
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
</body>

</html>