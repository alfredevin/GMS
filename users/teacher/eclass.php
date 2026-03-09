<?php
include '../../config.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php include './../template/header.php' ?>

<style>
    /* User-Friendly Table Styling */
    .grade-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .grade-table th {
        background-color: #4e73df;
        color: white;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        font-size: 13px;
        padding: 12px 15px;
        border-bottom: 2px solid #2e59d9;
    }

    .grade-table td {
        text-align: center;
        vertical-align: middle;
        font-weight: 600;
        font-size: 14px;
        padding: 10px;
        border-bottom: 1px solid #e3e6f0;
        border-right: 1px solid #e3e6f0;
    }

    /* STICKY COLUMN: Makes the Student Name stay on screen when scrolling horizontally */
    .sticky-name-col {
        position: sticky;
        left: 0;
        background-color: #fff !important;
        z-index: 1;
        min-width: 250px;
        border-right: 3px solid #d1d3e2 !important;
        /* Strong border to separate from scrolling grades */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
    }

    th.sticky-name-col {
        background-color: #2e59d9 !important;
        /* Darker blue for the sticky header */
        z-index: 2;
    }

    /* Grade Status Colors */
    .grade-inc {
        color: #e74a3b;
        background-color: #fdecee;
        /* Light red bg for easy spotting */
    }

    .grade-pass {
        color: #1cc88a;
    }

    .gen-avg-cell {
        background-color: #f8f9fc;
        font-size: 15px;
    }

    /* Hover effect for sticky column */
    .grade-table tbody tr:hover td.sticky-name-col {
        background-color: #f1f3f6 !important;
    }
</style>

<body id="page-top">
    <div id="wrapper">
        <?php include './../template/sidebar.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include './../template/navbar.php'; ?>
                <?php

                // Kunin ang Teacher ID mula sa session
                $teacher_id = $_SESSION['teacher_id'] ?? $_GET['teacher_id'] ?? '';

                // --- GRADE COMPUTATION HELPER FUNCTIONS ---
                function computeSpecificAverage($conn, $q, $type, $subject_id, $student_id)
                {
                    $query = "SELECT score, items FROM announcement_tbl 
              INNER JOIN student_scores_tbl ON announcement_tbl.announcement_jd = student_scores_tbl.announcement_id 
              WHERE quarterly = '$q' AND type = '$type' AND subject = '$subject_id' AND student_id = '$student_id'";
                    $res = mysqli_query($conn, $query);
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

                function getFinalSubjectGrade($conn, $student_id, $subject_id)
                {
                    $finalSum = 0;
                    $gradeCount = 0;

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

                    if ($gradeCount == 4) {
                        return number_format($finalSum / 4, 2);
                    } else {
                        return 'INC';
                    }
                }

                // Kunin ang Section at Grade Level ng Adviser
                $adviser_query = mysqli_query($conn, "SELECT grade_level, section_id FROM teacher_tbl WHERE teacher_id = '$teacher_id' AND teacher_type = 'Class Adviser'");
                $is_adviser = false;
                $adviser_grade = '';
                $adviser_section = '';
                $subjects = [];

                if (mysqli_num_rows($adviser_query) > 0) {
                    $is_adviser = true;
                    $adv_row = mysqli_fetch_assoc($adviser_query);
                    $adviser_grade = $adv_row['grade_level'];
                    $adviser_section = $adv_row['section_id'];

                    // Kunin ang lahat ng Subjects para sa Grade Level na ito bilang Columns
                    $sub_res = mysqli_query($conn, "SELECT subject_id, subject_name FROM subject_tbl WHERE subject_grade = '$adviser_grade'");
                    while ($s = mysqli_fetch_assoc($sub_res)) {
                        $subjects[] = $s;
                    }
                }
                ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Master E-Class Record</h1>
                        <a href="print_master_grades" target="_blank"
                            class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-print fa-sm text-white-50"></i> Print / Save as PDF
                        </a>
                    </div>

                    <?php if ($is_adviser): ?>
                        <div class="card shadow mb-4 border-bottom-primary">
                            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary text-uppercase">
                                    <i class="fas fa-chalkboard-teacher mr-2"></i> Class Master Grade Sheet (Grade
                                    <?= htmlspecialchars($adviser_grade) ?>)
                                </h6>
                            </div>
                            <div class="card-body">

                                <?php if (empty($subjects)): ?>
                                    <div class="alert alert-warning text-center">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>
                                        <strong>No Subjects Found!</strong> Please assign subjects to Grade
                                        <?= htmlspecialchars($adviser_grade) ?> in the Subject Management first.
                                    </div>
                                <?php else: ?>

                                    <div class="table-responsive border rounded">
                                        <table class="table table-hover grade-table w-100" id="dataTableGrades">
                                            <thead>
                                                <tr>
                                                    <th class="text-left sticky-name-col">Learner's Name</th>

                                                    <?php foreach ($subjects as $sub): ?>
                                                        <th><?= htmlspecialchars($sub['subject_name']) ?></th>
                                                    <?php endforeach; ?>

                                                    <th class="bg-warning text-dark">Gen. Average</th>
                                                    <th class="bg-success text-white">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Kunin lahat ng estudyante sa section na ito
                                                $stud_query = "SELECT s.student_id, e.firstname, e.lastname, e.middlename 
                                                               FROM student_tbl s
                                                               INNER JOIN enrollment_tbl e ON s.enrollment_id = e.enrollmentId
                                                               WHERE s.section_id = '$adviser_section' AND s.student_grade = '$adviser_grade'
                                                               ORDER BY e.lastname ASC";
                                                $stud_res = mysqli_query($conn, $stud_query);

                                                if (mysqli_num_rows($stud_res) > 0) {
                                                    while ($student = mysqli_fetch_assoc($stud_res)) {
                                                        $mi = !empty($student['middlename']) ? substr($student['middlename'], 0, 1) . '.' : '';
                                                        $full_name = strtoupper($student['lastname'] . ', ' . $student['firstname'] . ' ' . $mi);
                                                        $sid = $student['student_id'];

                                                        echo "<tr>";
                                                        // Sticky Name Cell
                                                        echo "<td class='text-left text-dark font-weight-bold sticky-name-col'>
                                                                <i class='fas fa-user-graduate text-gray-400 mr-2'></i> $full_name
                                                              </td>";

                                                        $totalGenAvg = 0;
                                                        $subjectCount = 0;
                                                        $hasInc = false;

                                                        // I-loop lahat ng subjects para sa grades ng isang bata
                                                        foreach ($subjects as $sub) {
                                                            $sub_id = $sub['subject_id'];
                                                            $finalSubjectGrade = getFinalSubjectGrade($conn, $sid, $sub_id);

                                                            if ($finalSubjectGrade === 'INC') {
                                                                echo "<td class='grade-inc' title='Incomplete Grades'>INC</td>";
                                                                $hasInc = true;
                                                            } else {
                                                                $isPassing = ($finalSubjectGrade >= 75) ? 'grade-pass text-dark' : 'grade-inc';
                                                                echo "<td class='$isPassing'>$finalSubjectGrade</td>";
                                                                $totalGenAvg += $finalSubjectGrade;
                                                                $subjectCount++;
                                                            }
                                                        }

                                                        // Kalkulahin ang General Average
                                                        if ($hasInc || $subjectCount == 0) {
                                                            echo "<td class='grade-inc gen-avg-cell'>INC</td>";
                                                            echo "<td class='text-danger font-weight-bold'><i class='fas fa-times-circle'></i> Incomplete</td>";
                                                        } else {
                                                            $genAvg = number_format($totalGenAvg / $subjectCount, 2);
                                                            $genClass = ($genAvg >= 75) ? 'text-primary font-weight-bolder' : 'grade-inc';

                                                            $remarks = ($genAvg >= 90) ? 'Passed w/ Honors' : (($genAvg >= 75) ? 'Passed' : 'Failed');
                                                            $remarkColor = ($genAvg >= 90) ? 'text-success' : (($genAvg >= 75) ? 'text-success' : 'text-danger');
                                                            $icon = ($genAvg >= 75) ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';

                                                            echo "<td class='gen-avg-cell $genClass'>$genAvg</td>";
                                                            echo "<td class='$remarkColor'>$icon $remarks</td>";
                                                        }

                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    // Handle pag walang students
                                                    $colspan = count($subjects) + 3;
                                                    echo "<tr><td colspan='$colspan' class='text-center py-4 text-muted'>No learners enrolled in your section yet.</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info shadow-sm p-4 d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x mr-3 text-info"></i>
                            <div>
                                <h5 class="alert-heading font-weight-bold mb-1">Access Restricted</h5>
                                <p class="mb-0">The Master Grade Sheet is a consolidated view exclusively available for
                                    <strong>Class Advisers</strong>.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php include './../template/footer.php'; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <?php include './../template/script.php'; ?>

    <script>
        $(document).ready(function () {
            // Enhanced DataTables for better UX
            $('#dataTableGrades').DataTable({
                "pageLength": 50,
                "scrollX": true,           // Enables horizontal scrolling
                "bLengthChange": false,    // Removes the "Show 10 entries" dropdown to keep UI clean
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search learner name..."
                }
            });

            // Clean up the search box styling injected by DataTables
            $('.dataTables_filter input').addClass('form-control form-control-sm');
        });
    </script>
</body>

</html>