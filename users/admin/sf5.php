    <?php
    include '../../config.php';

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
                    <div class="container-fluid  ">

                        <div class="card shadow mb-4  ">

                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">School Form 5 - Report on Promotion and Level of Proficiency & Achievement </h6>
                                    </div>
                                    <!-- GRADE SELECT -->
                                    <form method="GET" id="filterForm">
                                        <div class="row">
                                            <div class="col">
                                                <select name="grade" id="gradeSelect" class="form-control">
                                                    <option value="">SELECT GRADE</option>
                                                    <?php
                                                    $selectedGrade = $_GET['grade'] ?? '';
                                                    foreach ([7, 8, 9, 10] as $grade) {
                                                        $selected = ($selectedGrade == $grade) ? 'selected' : '';
                                                        echo "<option value='$grade' $selected>GRADE $grade</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col">
                                                <select name="section" id="sectionSelect" class="form-control" onchange="document.getElementById('filterForm').submit()" <?= empty($selectedGrade) ? 'disabled' : '' ?>>
                                                    <option value="">SECTION</option>
                                                    <?php
                                                    $section_query = "SELECT * FROM section_tbl";
                                                    $section_result = mysqli_query($conn, $section_query);
                                                    $sectionsByGrade = [];

                                                    // Group sections by grade
                                                    while ($row = mysqli_fetch_assoc($section_result)) {
                                                        $sectionsByGrade[$row['section_grade']][] = $row;
                                                    }

                                                    // Output all as hidden (JS will toggle visibility)
                                                    foreach ($sectionsByGrade as $grade => $sections) {
                                                        foreach ($sections as $sec) {
                                                            $isSelected = (isset($_GET['section']) && $_GET['section'] == $sec['section_id']) ? 'selected' : '';
                                                            echo "<option value='{$sec['section_id']}' data-grade='{$grade}' style='display:none' $isSelected>{$sec['section_name']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </form>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const gradeSelect = document.getElementById('gradeSelect');
                                            const sectionSelect = document.getElementById('sectionSelect');

                                            function updateSections(grade) {
                                                const options = sectionSelect.querySelectorAll('option[data-grade]');
                                                sectionSelect.disabled = false;

                                                options.forEach(opt => {
                                                    if (opt.dataset.grade === grade) {
                                                        opt.style.display = 'block';
                                                    } else {
                                                        opt.style.display = 'none';
                                                        opt.selected = false;
                                                    }
                                                });
                                            }

                                            // On initial load, if grade is preselected (from URL), show correct sections
                                            if (gradeSelect.value !== '') {
                                                updateSections(gradeSelect.value);
                                            }

                                            // When grade changes, show relevant sections only
                                            gradeSelect.addEventListener('change', function() {
                                                const grade = this.value;
                                                updateSections(grade);
                                            });
                                        });
                                    </script>

                                    <?php

                                    if (!empty($filter_grade) && !empty($filter_section)):
                                    ?>
                                        <div class="col-">
                                            <a href="print_sf5?grade=<?= $filter_grade; ?>&section=<?= $filter_section; ?>" class="btn btn-danger  ml-3" target="_blank"><i class="fas fa-print"></i> Print SF-5</a>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>LRN</th>
                                                <th>LEARNER'S NAME</th>
                                                <th>General Average</th>
                                                <th>Action Taken</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            if (!empty($filter_grade) && !empty($filter_section)) {

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
                                                            return ['remarks' => 'Failed', 'generalAverage' => $generalAverage];
                                                        } elseif ($generalAverage >= 90) {
                                                            return ['remarks' => 'Promoted with Honors', 'generalAverage' => $generalAverage];
                                                        } else {
                                                            return ['remarks' => 'Promoted', 'generalAverage' => $generalAverage];
                                                        }
                                                    } else {
                                                        return ['remarks' => 'Incomplete', 'generalAverage' => 0];
                                                    }
                                                }

                                                // Main Query
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

                                                $sql .= " ORDER BY sex DESC, lastname ASC";

                                                // Variables for statistics
                                                $maleCount = 0;
                                                $femaleCount = 0;
                                                $totalGeneralAverage = 0;
                                                $totalStudentWithGrades = 0;
                                                $rowsMale = "";
                                                $rowsFemale = "";

                                                $result = mysqli_query($conn, $sql);

                                                while ($res = mysqli_fetch_assoc($result)) {
                                                    $student_id = $res['student_id'];
                                                    $student_grade = $res['student_grade'];
                                                    $fullname = $res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname'];
                                                    $gender = strtolower($res['sex']);

                                                    $evaluation = getStudentFinalRemarks($conn, $student_id, $student_grade);
                                                    $remarks = $evaluation['remarks'];
                                                    $genAvg = $evaluation['generalAverage'];

                                                    if ($genAvg > 0) {
                                                        $totalGeneralAverage += $genAvg;
                                                        $totalStudentWithGrades++;
                                                    }

                                                    $row = "<tr>
                        <td>{$res['student_id']}</td>
                        <td>{$fullname}</td>
                        <td><strong>" . number_format($genAvg, 2) . "</strong></td>
                        <td><strong>{$remarks}</strong></td>
                    </tr>";

                                                    if ($gender === 'male') {
                                                        $rowsMale .= $row;
                                                        $maleCount++;
                                                    } elseif ($gender === 'female') {
                                                        $rowsFemale .= $row;
                                                        $femaleCount++;
                                                    }
                                                }

                                                $overallCount = $maleCount + $femaleCount;
                                                $classAverage = $totalStudentWithGrades > 0 ? $totalGeneralAverage / $totalStudentWithGrades : 0;

                                                // Display MALE section
                                                if ($maleCount > 0) {
                                                    echo "<tr><th colspan='4'>MALE</th></tr>";
                                                    echo $rowsMale;
                                                    echo "<tr><td colspan='4'><strong>Total Male: {$maleCount}</strong></td></tr>";
                                                }

                                                // Display FEMALE section
                                                if ($femaleCount > 0) {
                                                    echo "<tr><th colspan='4'>FEMALE</th></tr>";
                                                    echo $rowsFemale;
                                                    echo "<tr><td colspan='4'><strong>Total Female: {$femaleCount}</strong></td></tr>";
                                                }

                                                // Display TOTALS
                                                echo "<tr class='bg-light font-weight-bold text-uppercase'>
                <td colspan='2' class='text-right'>Total Students:</td>
                <td colspan='2'>{$overallCount}</td>
            </tr>";
                                                echo "<tr class='bg-light font-weight-bold text-uppercase'>
                <td colspan='2' class='text-right'>Class General Average:</td>
                <td colspan='2'>" . number_format($classAverage, 2) . "</td>
            </tr>";
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

    </body>

    </html>