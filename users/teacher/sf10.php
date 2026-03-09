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
                                    <h6 class="m-0 font-weight-bold text-primary">Student Report Card </h6>
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

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>LRN</th>
                                            <th>LEARNER'S NAME</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        if (!empty($filter_grade) && !empty($filter_section)) {
                                            $sql = "SELECT * FROM student_tbl
                                            INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                            INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                            WHERE student_tbl.status = 1 AND student_tbl.student_grade = '$filter_grade' AND student_tbl.section_id = '$filter_section' ORDER BY sex DESC, lastname ASC";
                                            $result = mysqli_query($conn, $sql);
                                            while ($res = mysqli_fetch_assoc($result)) {
                                                $student_id = $res['student_id'];
                                                $student_grade = $res['student_grade'];
                                                $fullname = $res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname'];
                                        ?>
                                                <tr>
                                                    <td><?= $student_id; ?></td>
                                                    <td><?= $fullname; ?></td>
                                                    <td>
                                                        <a href="grade_report?studId=<?= $student_id; ?>&grade=<?= $filter_grade ?>&section=<?= $filter_section ?>" class="btn btn-primary btn-sm">View Grades</a>
                                                    </td>
                                                </tr>
                                        <?php }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>