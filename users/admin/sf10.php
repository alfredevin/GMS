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
                                    <h6 class="m-0 font-weight-bold text-primary">School Form 10 </h6>
                                </div>
                                <!-- GRADE SELECT -->


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

                                        $sql = "SELECT * FROM student_tbl
                                            INNER JOIN enrollment_tbl ON enrollment_tbl.enrollmentId = student_tbl.enrollment_id
                                            INNER JOIN section_tbl ON section_tbl.section_id = student_tbl.section_id 
                                            WHERE student_tbl.status = 1   ORDER BY sex DESC, lastname ASC";
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
                                                    <a href="printSf10?studId=<?= $student_id; ?>" class="btn btn-primary btn-sm">View Grades</a>
                                                </td>
                                            </tr>
                                        <?php }
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