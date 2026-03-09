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
                    <a href="teacher_subject" class="btn btn-primary btn-sm">Back to Page</a>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae perferendis id similique, fuga rem velit? Dolorem molestiae dolorum amet nisi cumque porro in dolores, dignissimos nulla sit quasi, culpa accusamus!</p>

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
                                            <th>Student ID</th>
                                            <th>Student Name </th>
                                            <th>Section </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        if (!empty($filterSection)) {
                                            $sql = "SELECT * FROM student_tbl
                                        INNER JOIN enrollment_tbl on enrollment_tbl.enrollmentId  = student_tbl.enrollment_id
                                        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id WHERE student_grade = '$filterGrade'AND student_tbl.section_id = '$filterSection'";
                                        }else{
                                            $sql = "SELECT * FROM student_tbl
                                            INNER JOIN enrollment_tbl on enrollment_tbl.enrollmentId  = student_tbl.enrollment_id
                                            INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id WHERE student_grade = '$filterGrade'";
                                        }
                                        $result = mysqli_query($conn, $sql);
                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];

                                        ?>
                                            <tr>

                                                <td><?php echo $res['student_id'] ?>
                                                </td>
                                                <td><?php echo $res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname']; ?></td>
                                                <td><?php echo $res['section_name'] ?>
                                                </td>
                                                <td><a href="#manage<?php echo $res['student_id']; ?>" data-toggle="modal" class="btn btn-danger btn-sm">Manage Record</a></td>

                                                <!-- MODAL -->
                                                <div class="modal fade" id="manage<?php echo $res['student_id']; ?>" data-studentid="<?php echo $res['student_id']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h5 class="modal-title text-white" style="text-transform:uppercase;font-family:'Times New Roman', Times, serif;"> <?php echo $name . ' - SUBJECT'; ?></h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>

                                                            <div class="modal-body" id="modal-details-body">
                                                                <h4 style="color:black;font-family:'Times New Roman', Times, serif;"> <?php echo $res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname'] . ' | GRADE -  ' . $filterGrade . ' ' . $res['section_name']; ?>
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
</body>

</html>