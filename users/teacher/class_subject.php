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
                                    <h6 class="m-0 font-weight-bold text-primary">Class Subject </h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student ID</th>
                                            <th>Student Name </th>
                                            <th>Section </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $select_tec_class = "SELECT * FROM teacher_tbl WHERE teacher_type = 'Class Adviser' AND teacher_id = '$teacher_id'";
                                        $result_sec_tec_class = mysqli_query($conn, $select_tec_class);
                                        $tec_class = mysqli_fetch_assoc($result_sec_tec_class);
                                        $grade_class = $tec_class["grade_level"];
                                        $section_class = $tec_class["section_id"];
                                        $date_today = date('Y-m-d');

                                        $sql = "SELECT * FROM student_tbl
                                        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                        INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                        WHERE student_grade = '$grade_class' AND student_tbl.section_id = '$section_class'";

                                        $result = mysqli_query($conn, $sql);
                                        $counter = 1;
                                        while ($res = mysqli_fetch_assoc($result)) {
                                            $student_id = $res['student_id'];

                                            $check = mysqli_query($conn, "SELECT * FROM attendance_tbl WHERE student_id = '$student_id' AND attendance_date = '$date_today'");
                                            $hasAttendance = mysqli_fetch_assoc($check);
                                        ?>
                                            <tr>
                                                <td><?php echo $counter; ?></td>
                                                <td><?= $res['student_id'] ?></td>
                                                <td><?= $res['lastname'] . ' ' . $res['firstname'] . ' ' . $res['middlename']; ?></td>
                                                <td><?= $res['section_name'] ?></td>
                                                <td>
                                                    <a href="class_student_record?student_id=<?= $student_id ?>" class="btn btn-primary btn-sm">  Student Grade Record</a>
                                                </td>
                                            </tr>
                                        <?php $counter++;
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