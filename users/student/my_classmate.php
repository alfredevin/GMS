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

                                    <h6 class="m-0 font-weight-bold text-primary text-center text-uppercase">Learning of my Classmates</h6>
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

                                <table class="table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Sex</th>
                                            <th>Birthdate</th>
                                            <th>Age</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $student_id = $my_student_id;
                                        $counter = 1;
                                        $select_my_subject = "SELECT * FROM student_tbl
                                        INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId  = student_tbl.enrollment_id  WHERE student_grade = '$grade'";
                                        $res_subject = mysqli_query($conn, $select_my_subject);
                                        while ($res = mysqli_fetch_assoc($res_subject)) {
                                            $student_name_sel = $res["firstname"] . ' ' . $res["middlename"] . ' ' . $res["lastname"];
                                        ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td><?php echo $res['student_id']; ?></td>
                                                <td><?php echo $student_name_sel ?></td>
                                                <td><?php echo $res['sex']; ?></td>
                                                <td><?php echo $res['birthdate']; ?></td>
                                                <td>
                                                    <?php
                                                    $birthdate = new DateTime($res['birthdate']);
                                                    $today = new DateTime('today');
                                                    $age = $birthdate->diff($today)->y;
                                                    echo $age;
                                                    ?>
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