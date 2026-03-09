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
                <div class="container-fluid">
                    <?php

                    $filter_school_year = $_GET['school_year'] ?? '';

                    // Kukunin ang hawak na section at grade ng naka-login na teacher (Class Adviser)
// Tiyaking ang $teacher_id ay na-declare na (halimbawa, galing sa $_SESSION)
                    $select_tec_class = "SELECT * FROM teacher_tbl WHERE teacher_type = 'Class Adviser' AND teacher_id = '$teacher_id'";
                    $result_sec_tec_class = mysqli_query($conn, $select_tec_class);
                    $tec_class = mysqli_fetch_assoc($result_sec_tec_class);

                    $grade_class = $tec_class["grade_level"] ?? '';
                    $section_class = $tec_class["section_id"] ?? '';
                    ?>
                    <div class="card shadow mb-4">

                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">School Form 1 (SF1) - Class Adviser
                                    </h6>
                                </div>

                                <form method="GET" id="filterForm">
                                    <select name="school_year" id="schoolYearSelect" class="form-control"
                                        onchange="document.getElementById('filterForm').submit()">
                                        <option value="">SELECT SCHOOL YEAR</option>
                                        <?php
                                        $selectedYear = $_GET['school_year'] ?? '';
                                        $currentYear = date('Y');
                                        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
                                            $yearRange = $i . '-' . ($i + 1);
                                            $selected = ($selectedYear == $yearRange) ? 'selected' : '';
                                            echo "<option value='$yearRange' $selected>$yearRange</option>";
                                        }
                                        ?>
                                    </select>
                                </form>

                                <?php if (!empty($filter_school_year)): ?>
                                    <div class="col-">
                                        <a href="print_sf1?school_year=<?= $filter_school_year ?>&section_id=<?= $section_class ?>"
                                            class="btn btn-danger ml-3" target="_blank">
                                            <i class="fas fa-print"></i> Print SF-1
                                        </a>
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
                                            <th>Sex</th>
                                            <th>Birthdate</th>
                                            <th>Age</th>
                                            <th>IP</th>
                                            <th>Mother Tongue</th>
                                            <th>Address</th>
                                            <th>Mother's Name</th>
                                            <th>Father's Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($filter_school_year)) {

                                            // QUERY BY SCHOOL YEAR, GRADE, AND SECTION
                                            $sql = "SELECT * FROM student_tbl
                                                    INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                                    WHERE student_tbl.status = 1
                                                    AND enrollment_tbl.stud_sy = '$filter_school_year'
                                                    AND student_tbl.student_grade = '$grade_class' 
                                                    AND student_tbl.section_id = '$section_class'
                                                    ORDER BY sex DESC, lastname ASC";

                                            $maleCount = 0;
                                            $femaleCount = 0;
                                            $rowsMale = "";
                                            $rowsFemale = "";

                                            $result = mysqli_query($conn, $sql);

                                            while ($res = mysqli_fetch_assoc($result)) {
                                                $fullname = $res['firstname'] . ' ' . $res['middlename'] . ' ' . $res['lastname'];
                                                $gender = strtolower($res['sex']);
                                                $birthdate = new DateTime($res['birthdate']);
                                                $today = new DateTime('today');
                                                $age = $birthdate->diff($today)->y;

                                                $mothertongue = $res['mothertongue'];
                                                $ip = $res['ip'];
                                                $address = $res['current_address'];
                                                $mother_name = $res['mother_firstname'] . ' ' . $res['mother_middlename'] . ' ' . $res['mother_lastname'];
                                                $father_name = $res['father_firstname'] . ' ' . $res['father_middlename'] . ' ' . $res['father_lastname'];

                                                $genderLabel = $gender === 'male' ? 'M' : ($gender === 'female' ? 'F' : '-');

                                                // Napansin kong student_id ang nakalagay dati sa <td> LRN. Binago ko na ito para tumugma sa column name na lrn.
                                                $row = "<tr>
                                                    <td>{$res['lrn']}</td> 
                                                    <td>{$fullname}</td>
                                                    <td><strong>{$genderLabel}</strong></td>
                                                    <td><strong>{$res['birthdate']}</strong></td>
                                                    <td><strong>{$age}</strong></td>
                                                    <td><strong>{$ip}</strong></td>
                                                    <td><strong>{$mothertongue}</strong></td>
                                                    <td><strong>{$address}</strong></td>
                                                    <td><strong>{$mother_name}</strong></td>
                                                    <td><strong>{$father_name}</strong></td>
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

                                            // DISPLAY MALE
                                            if ($maleCount > 0) {
                                                echo $rowsMale;
                                                echo "<tr><td colspan='10'><strong>TOTAL MALE: {$maleCount}</strong></td></tr>";
                                            }

                                            // DISPLAY FEMALE
                                            if ($femaleCount > 0) {
                                                echo $rowsFemale;
                                                echo "<tr><td colspan='10'><strong>TOTAL FEMALE: {$femaleCount}</strong></td></tr>";
                                            }

                                            // DISPLAY TOTAL
                                            echo "<tr class='bg-light font-weight-bold text-uppercase'>
                                                <td colspan='10'>COMBINED STUDENTS: {$overallCount}</td>
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