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
                                    <h6 class="m-0 font-weight-bold text-primary">Student Clearance </h6>
                                </div>
                                <!-- GRADE SELECT -->
                                <form method="GET" action="">
                                    <div class="col-">
                                        <select name="grade" class="form-control" onchange="this.form.submit()">
                                            <option value="">SELECT GRADE</option>
                                            <option value="7" <?= isset($_GET['grade']) && $_GET['grade'] == 7 ? 'selected' : '' ?>>GRADE 7</option>
                                            <option value="8" <?= isset($_GET['grade']) && $_GET['grade'] == 8 ? 'selected' : '' ?>>GRADE 8</option>
                                            <option value="9" <?= isset($_GET['grade']) && $_GET['grade'] == 9 ? 'selected' : '' ?>>GRADE 9</option>
                                            <option value="10" <?= isset($_GET['grade']) && $_GET['grade'] == 10 ? 'selected' : '' ?>>GRADE 10</option>
                                        </select>
                                    </div>
                                </form>

                                <!-- SECTION SELECT (only shown if grade is selected) -->
                                <?php if (isset($_GET['grade']) && $_GET['grade'] != ''): ?>
                                    <form method="GET" action="">
                                        <input type="hidden" name="grade" value="<?= $_GET['grade'] ?>">
                                        <div class="col- ml-3">
                                            <select name="section" class="form-control" onchange="this.form.submit()">
                                                <option value="">SECTION</option>
                                                <?php
                                                $selectedGrade = $_GET['grade'];
                                                $selectedSection = $_GET['section'] ?? '';
                                                $select_section = "SELECT * FROM section_tbl WHERE section_grade	 = '$selectedGrade'";
                                                $result_select_section = mysqli_query($conn, $select_section);
                                                while ($row_sec = mysqli_fetch_array($result_select_section)) {
                                                    $selected = $selectedSection == $row_sec['section_id'] ? 'selected' : '';
                                                    echo "<option value='{$row_sec['section_id']}' $selected>{$row_sec['section_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </form>
                                <?php endif; ?>



                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name </th>
                                            <th>Grade</th>
                                            <th>Section </th>
                                            <th>Remarks</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $filter_grade = $_GET['grade'] ?? '';
                                        $filter_section = $_GET['section'] ?? '';
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

                                        function getStudentFinalRemarks($conn, $student_id, $student_grade)
                                        {
                                            $subjectQuery = "SELECT * FROM subject_tbl WHERE subject_grade = '$student_grade'";
                                            $subjectResult = mysqli_query($conn, $subjectQuery);

                                            $totalFinalAverage = 0;
                                            $totalSubjects = 0;

                                            while ($subject = mysqli_fetch_assoc($subjectResult)) {
                                                $subject_id = $subject['subject_id'];

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
                                                        $finalSum += $finalGrade;
                                                        $gradeCount++;
                                                    }
                                                }

                                                if ($gradeCount == 4) {
                                                    $finalAverage = $finalSum / 4;
                                                    $totalFinalAverage += $finalAverage;
                                                    $totalSubjects++;
                                                }
                                            }

                                            if ($totalSubjects > 0) {
                                                $generalAverage = $totalFinalAverage / $totalSubjects;

                                                if ($generalAverage < 75) {
                                                    return 'Failed';
                                                } elseif ($generalAverage >= 90) {
                                                    return 'Promoted with Honors';
                                                } else {
                                                    return 'Promoted';
                                                }
                                            } else {
                                                return 'Incomplete';
                                            }
                                        }

                                        $sql = "SELECT * FROM student_tbl
        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
        WHERE student_tbl.status = 1";
                                        if (!empty($filter_grade)) {
                                            $sql .= " AND student_tbl.student_grade = '$filter_grade'";
                                        }
                                        if (!empty($filter_section)) {
                                            $sql .= " AND student_tbl.section_id = '$filter_section'";
                                        }

                                        $result = mysqli_query($conn, $sql);

                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];
                                            $student_grade = $res['student_grade'];
                                            $remarks = getStudentFinalRemarks($conn, $student_id, $student_grade);


                                            echo "<tr>";
                                            echo "<td>{$res['student_id']}</td>";
                                            echo "<td>{$res['firstname']} {$res['middlename']} {$res['lastname']}</td>";
                                            echo "<td> GRADE {$res['student_grade']}</td>";
                                            echo "<td>{$res['section_name']}</td>";
                                            echo "<td><strong>{$remarks}</strong></td>";
                                            echo "<td>";

                                            if ($remarks == 'Promoted' || $remarks == 'Promoted with Honors') {
                                                echo '<button class="btn btn-success btn-sm promote-btn"
                                                        data-student-id="' . $res['student_id'] . '"
                                                        data-current-grade="' . $res['student_grade'] . '">
                                                        Promote
                                                    </button>';
                                            } elseif ($remarks == 'Failed' || $remarks == 'Incomplete') {
                                                echo '<button class="btn btn-warning btn-sm" disabled>Not Promoted</button>';
                                            }

                                            echo " <a href='view_student_grade?student_id={$student_id}' class='btn btn-primary btn-sm'>View Grade</a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                        ?>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.promote-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const studentId = this.dataset.studentId;
                    const currentGrade = parseInt(this.dataset.currentGrade);

                    // If Grade 10, graduate
                    if (currentGrade === 10) {
                        const confirmGraduate = await Swal.fire({
                            title: 'Graduate this student?',
                            text: 'This student will be marked as graduated.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Graduate',
                            cancelButtonText: 'Cancel'
                        });

                        if (confirmGraduate.isConfirmed) {
                            const response = await fetch('graduate_student.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `student_id=${studentId}`
                            });

                            const result = await response.json();

                            if (result.success) {
                                Swal.fire('Success', 'Student marked as graduated.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', result.message || 'Graduation failed.', 'error');
                            }
                        }

                        return; // stop here if graduated
                    }

                    // Otherwise, promote to next grade
                    const nextGrade = currentGrade + 1;

                    // Fetch section list for next grade using AJAX
                    const response = await fetch(`get_sections.php?grade=${nextGrade}`);
                    const data = await response.json();

                    if (data.length === 0) {
                        Swal.fire('No Sections Found', `No sections available for Grade ${nextGrade}`, 'error');
                        return;
                    }

                    let sectionOptions = '';
                    data.forEach(sec => {
                        sectionOptions += `<option value="${sec.section_id}">${sec.section_name}</option>`;
                    });

                    const {
                        value: sectionId
                    } = await Swal.fire({
                        title: `Promote to Grade ${nextGrade}`,
                        html: `
                    <select id="sectionSelect" class="swal2-select form-control">
                        ${sectionOptions}
                    </select>
                `,
                        confirmButtonText: 'Confirm Promotion',
                        focusConfirm: false,
                        preConfirm: () => {
                            return document.getElementById('sectionSelect').value;
                        }
                    });

                    if (sectionId) {
                        const promoteResponse = await fetch('promote_student.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `student_id=${studentId}&grade=${nextGrade}&section_id=${sectionId}`
                        });

                        const result = await promoteResponse.json();

                        if (result.success) {
                            Swal.fire('Success', 'Student promoted successfully!', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', result.message || 'Promotion failed.', 'error');
                        }
                    }
                });
            });
        });
    </script>

</body>

</html>